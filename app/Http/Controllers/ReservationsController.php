<?php

namespace App\Http\Controllers;

//use App\Models\reservations;
use App\Models\User;
use App\Models\Voyages;
use App\Models\Reservations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorereservationsRequest;
use App\Http\Requests\UpdatereservationsRequest;

class ReservationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function getUserReservations()
    {
        // 1. Récupérer l'utilisateur authentifié.
        $user = Auth::user();

        // 2. Si aucun utilisateur n'est authentifié, renvoyer une erreur 401.
        if (!$user) {
            return response()->json([
                'message' => 'Non authentifié. Veuillez vous connecter.'
            ], 401);
        }

        // 3. Récupérer les réservations de l'utilisateur, en triant par date de voyage
        // pour que les plus récentes apparaissent en premier.
        // On charge aussi les relations nécessaires pour éviter les requêtes N+1.
        $reservations = Reservations::where('idUtilisateur', $user->id)
                                ->with([
                                    'voyage.trajet.compagnie',
                                    'voyage.bus',
                                    'paiement'
                                ])
                                ->orderByDesc(
                                    Voyages::select('dateDepart')
                                        ->whereColumn('voyages.id', 'reservations.idVoyage')
                                )
                                ->get();

        // 4. Formater les données pour une réponse JSON claire.
        // On itère sur chaque réservation pour construire une structure de données
        // simple et facile à utiliser côté client Flutter.
        $formattedReservations = $reservations->map(function ($reservation) {
            $voyage = $reservation->voyage;
            $trajet = $voyage->trajet;
            $compagnie = $trajet->compagnie;

            return [
                'id' => $reservation->id,
                'nombrePlaces' => $reservation->nombrePlaces,
                'montantTotal' => $reservation->montantTotal,
                'statut' => $reservation->statut,
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at,
                'voyage' => [
                    'id' => $voyage->id,
                    'dateDepart' => $voyage->dateDepart,
                    'heureDepart' => $voyage->heureDepart,
                    'trajet' => [
                        'id' => $trajet->id,
                        'pointDepart' => $trajet->pointDepart,
                        'pointArrive' => $trajet->pointArrive,
                        'compagnie' => [
                            'name' => $compagnie->name,
                        ],
                    ],
                ],
                'paiement' => [
                    'id' => $reservation->paiement->id ?? null,
                    'montant' => $reservation->paiement->montant ?? null,
                ],
                // Étant donné que 'passagers' est un JSON, il est déjà décodé
                'passagers' => $reservation->passagers,
            ];
        });

        // 5. Renvoyer la liste des réservations formatées en JSON.
        return response()->json([
            'reservations' => $formattedReservations
        ], 200);
    }


    public function recapitulatifTemporaire(StorereservationsRequest $request)
{
    $data = $request->validated();

    // Tu ne crées PAS la réservation ici, juste un retour temporaire pour affichage
    $voyage = Voyages::with('trajet.compagnie')->findOrFail($data['idVoyage']);

    return response()->json([
        'message' => 'Récapitulatif de la réservation',
        'data' => [
            'compagnie' => [
                'id' => $voyage->trajet->compagnie->id,
                'name' => $voyage->trajet->compagnie->name,
                'logo' => $voyage->trajet->compagnie->logo,
                'telephone' => $voyage->trajet->compagnie->telephone,
                'email' => $voyage->trajet->compagnie->email,
            ],
            'trajet' => [
                'depart' => $voyage->trajet->pointDepart,
                'arrivee' => $voyage->trajet->pointArrive,
                'prix' => $voyage->trajet->prix,
                'date' => $voyage->dateDepart,
                'heure' => $voyage->heuresDepart,
            ],
            'nombrePlaces' => $data['nombrePlaces'],
            'montantTotal' => $data['montantTotal'],
            'passagers' => $data['passagers']
        ]
    ]);
}

    public function recapitulatif($id)
{
    $reservation = Reservations::with([
        'voyage.trajet',
        'voyage.bus',
        'voyage.trajet.compagnie'
    ])->where('id', $id)->where('idUtilisateur', auth()->id())->first();

    if (!$reservation) {
        return response()->json(['message' => 'Réservation introuvable'], 404);
    }

    return response()->json([
        'reservation_id' => $reservation->id,
        'nombrePlaces' => $reservation->nombrePlaces,
        'montantTotal' => $reservation->montantTotal,
        'passagers' => $reservation->passagers,
        'voyage' => [
            'date' => $reservation->voyage->dateDepart,
            'heure' => $reservation->voyage->heuresDepart,
            'trajet' => [
                'depart' => $reservation->voyage->trajet->pointDepart,
                'arrivee' => $reservation->voyage->trajet->pointArrive,
                'prixUnitaire' => $reservation->voyage->trajet->prix,
                'compagnie' => $reservation->voyage->trajet->compagnie->name,
            ],
            'bus' => $reservation->voyage->bus ? [
                'numero' => $reservation->voyage->bus->numeroBus
            ] : null,
        ]
    ]);
}


     /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationsRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $idVoyageAller = $data['idVoyage'];
            $idVoyageRetour = $data['idVoyageRetour'] ?? null;
            $nombrePlaces = $data['nombrePlaces'];

            // ✅ CORRECTION : Charger les relations nécessaires
            $voyageAller = Voyages::with(['bus', 'trajet.frequence'])->find($idVoyageAller);
            if (!$voyageAller) {
                return response()->json(['message' => 'Voyage aller introuvable.'], 404);
            }

            $voyages = collect([$voyageAller]);

            // ✅ Ajouter le voyage retour s'il existe
            if ($idVoyageRetour) {
                $voyageRetour = Voyages::with(['bus', 'trajet.frequence'])->find($idVoyageRetour);
                if (!$voyageRetour) {
                    return response()->json(['message' => 'Voyage retour introuvable.'], 404);
                }
                $voyages->push($voyageRetour);
            }

            // ✅ Vérifier la disponibilité des places
            foreach ($voyages as $voyage) {
                if ($voyage->bus) {
                    // Cas avec bus assigné
                    if ($voyage->bus->nombrePlaceDispo < $nombrePlaces) {
                        return response()->json([
                            'message' => "Pas assez de places dans le bus pour le voyage {$voyage->id}. Places disponibles: {$voyage->bus->nombrePlaceDispo}"
                        ], 400);
                    }
                } else {
                    // Cas sans bus (fréquence)
                    // ✅ CORRECTION : Vérifier d'abord si la fréquence existe
                    $frequence = $voyage->trajet->frequence;

                    if (!$frequence) {
                        // Si pas de fréquence, chercher par heure de départ
                        $frequence = FrequenceTrajets::where('idTrajet', $voyage->trajet->id)
                            ->where('heureDepart', $voyage->heuresDepart)
                            ->first();

                        if (!$frequence) {
                            return response()->json([
                                'message' => "Aucune fréquence trouvée pour ce trajet et cet horaire."
                            ], 400);
                        }
                    }

                    // ✅ CORRECTION : Calculer les places disponibles
                    $nbTickets = $voyage->tickets()->count();
                    $placesRestantes = $frequence->nombrePlaceMinimum - $nbTickets;

                    if ($placesRestantes < $nombrePlaces) {
                        return response()->json([
                            'message' => "Pas assez de places disponibles pour le voyage {$voyage->id}. Places restantes: {$placesRestantes}"
                        ], 400);
                    }

                    // ✅ DEBUG : Log pour vérifier
                    Log::info("Voyage {$voyage->id} - Fréquence trouvée: " . $frequence->id);
                    Log::info("Places minimum: {$frequence->nombrePlaceMinimum}, Tickets vendus: {$nbTickets}, Places restantes: {$placesRestantes}");
                }
            }

            // ✅ Créer la réservation
            $reservation = Reservations::create([
                'idUtilisateur' => auth()->id(),
                'idVoyage' => $idVoyageAller,
                'idVoyageRetour' => $idVoyageRetour,
                'nombrePlaces' => $nombrePlaces,
                'montantTotal' => $data['montantTotal'],
                'passagers' => $data['passagers'],
                'statut' => 'en_attente_paiement',
                'idPaiement' => null,
            ]);

            // ✅ Mettre à jour les places disponibles si bus assigné
            foreach ($voyages as $voyage) {
                if ($voyage->bus) {
                    $voyage->bus->decrement('nombrePlaceDispo', $nombrePlaces);
                    Log::info("Bus {$voyage->bus->id} - Places mises à jour: -{$nombrePlaces}");
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Réservation enregistrée. Veuillez effectuer le paiement.',
                'reservation_id' => $reservation->id,
                'montant' => $reservation->montantTotal,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création réservation : " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'message' => 'Erreur serveur lors de la création de la réservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(reservations $reservations)
    {
        //
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(reservations $reservations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatereservationsRequest $request, reservations $reservations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(reservations $reservations)
    {
        //
    }
}
