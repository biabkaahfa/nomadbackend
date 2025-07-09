<?php

namespace App\Http\Controllers;

//use App\Models\reservations;
use App\Http\Requests\StorereservationsRequest;
use App\Http\Requests\UpdatereservationsRequest;
use App\Models\Reservations;
use App\Models\Voyages;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
   public function store(StorereservationsRequest $request)
{
    DB::beginTransaction();

    try {
        $data = $request->validated();
        $idVoyage = $data['idVoyage'];
        $nombrePlaces = $data['nombrePlaces'];

        $voyage = Voyages::with(['bus', 'trajet.frequences'])->findOrFail($idVoyage);

        // 🧮 Vérification des places disponibles
        if ($voyage->idBus && $voyage->bus) {
            if ($voyage->bus->placesDisponible < $nombrePlaces) {
                return response()->json(['message' => 'Pas assez de places dans le bus.'], 400);
            }
        } else {
            $frequence = $voyage->trajet->frequences
                ->where('heureDepart', $voyage->heuresDepart)->first();

            if (!$frequence) {
                return response()->json(['message' => 'Fréquence introuvable pour ce voyage.'], 400);
            }

            $nbTickets = $voyage->tickets()->count();

            if (($frequence->nombrePlaceMinimum - $nbTickets) < $nombrePlaces) {
                return response()->json(['message' => 'Pas assez de places disponibles (sans bus).'], 400);
            }
        }

        // 💾 Enregistrement de la réservation
        $reservation = Reservations::create([
            'idUtilisateur' => auth()->id(),
            'idVoyage' => $idVoyage,
            'nombrePlaces' => $nombrePlaces,
            'montantTotal' => $data['montantTotal'],
            'passagers' => $data['passagers'],
            'statut' => 'en_attente_paiement',
            'idPaiement' => null,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Réservation enregistrée. Veuillez effectuer le paiement.',
            'reservation_id' => $reservation->id,
            'montant' => $reservation->montantTotal,
            'paiement_url' => 'https://paiement.exemple.com/reservations/' . $reservation->id,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur création réservation : " . $e->getMessage());
        return response()->json(['message' => 'Erreur serveur'], 500);
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
