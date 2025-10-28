<?php

namespace App\Http\Controllers\Paiement;

use App\Models\Garres;
use App\Models\Paiements;
use App\Models\Compagnies;
use App\Models\GarreTrajets;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePaiementsRequest;
use App\Http\Requests\UpdatePaiementsRequest;

class PaiementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $user = Auth::user();
        $profil = $user->profil?->name;

        $paiementsQuery = Paiements::query()
            ->select('paiements.*')
            ->with(['ticket.voyage.trajet.compagnie', 'ticket.garre']);

        // Initialiser les variables pour tous les profils
        $garre = null;
        $compagnie = null;

        if ($profil === 'Admin général') {
            // Admin général voit tous les paiements
        } elseif ($profil === 'Admin compagnie') {
            $compagnie = $user->compagnie;
            $paiementsQuery = $paiementsQuery
                ->join('tickets', 'paiements.id', '=', 'tickets.idPaiement')
                ->join('voyages', 'tickets.idVoyage', '=', 'voyages.id')
                ->join('trajets', 'voyages.idTrajet', '=', 'trajets.id')
                ->where('trajets.idCompagnie', $user->idCompagnie);
        } elseif (in_array($profil, ['Chef de gare', 'Réceptionniste'])) {
            // CORRECTION : Charger explicitement la gare avec sa compagnie
            $garre = Garres::with('compagnie')->find($user->idGarre);

            if ($garre && $garre->compagnie) {
                $compagnie = $garre->compagnie;

                logger("=== FILTRAGE CHEF GARE ===");
                logger("Gare ID: " . $garre->id);
                logger("Gare Name: " . $garre->name);
                logger("Compagnie ID: " . $compagnie->id);
                logger("Compagnie Name: " . $compagnie->name);

                // CORRECTION STRICTE : Filtrer par la gare ET la compagnie de la gare
                $paiementsQuery = $paiementsQuery
                    ->whereHas('ticket', function($query) use ($user, $compagnie) {
                        $query->where('idGarre', $user->idGarre) // Uniquement SA gare
                              ->whereNotNull('dateScan') // Uniquement tickets scannés
                              ->whereHas('voyage.trajet', function($q) use ($compagnie) {
                                  $q->where('idCompagnie', $compagnie->id); // Uniquement SA compagnie
                              });
                    });
            } else {
                logger("ERREUR: Gare ou Compagnie non trouvée");
                logger("Gare trouvée: " . ($garre ? 'OUI' : 'NON'));
                logger("Compagnie trouvée: " . ($garre && $garre->compagnie ? 'OUI' : 'NON'));
                $paiements = collect();
            }
        }

        $paiementsQuery = $paiementsQuery->whereNotNull('paiements.created_at');
        $paiements = $paiementsQuery->get();

        // DEBUG: Vérifier le filtrage
        logger("=== RÉSULTATS FILTRAGE ===");
        logger("Profil: {$profil}");
        logger("Paiements trouvés: " . $paiements->count());

        // Vérifier chaque paiement pour le chef de gare
        if (in_array($profil, ['Chef gare', 'Réceptionniste']) && $paiements->count() > 0) {
            logger("=== DÉTAILS DES PAIEMENTS FILTRÉS ===");
            foreach ($paiements as $paiement) {
                $garreTicket = $paiement->ticket->garre->name ?? 'N/A';
                $garreTicketId = $paiement->ticket->garre->id ?? 'N/A';
                $compagnieTicket = $paiement->ticket->voyage->trajet->compagnie->name ?? 'N/A';
                $compagnieTicketId = $paiement->ticket->voyage->trajet->compagnie->id ?? 'N/A';
                $dateScan = $paiement->ticket->dateScan ? 'SCANNÉ' : 'NON SCANNÉ';

                logger("Paiement {$paiement->id}: Gare={$garreTicket}({$garreTicketId}), Compagnie={$compagnieTicket}({$compagnieTicketId}), Statut={$dateScan}");
            }
        }

        // Récupérer toutes les compagnies pour le sélecteur (uniquement pour Admin général)
        $compagnies = $profil === 'Admin général' ? Compagnies::all() : collect();

        // Préparer les données selon le profil
        if ($profil === 'Admin général') {
            $compagniesData = [];
            foreach ($paiements as $paiement) {
                $compagnieName = 'Non attribué';
                if ($paiement->ticket && $paiement->ticket->voyage && $paiement->ticket->voyage->trajet && $paiement->ticket->voyage->trajet->compagnie) {
                    $compagnieName = $paiement->ticket->voyage->trajet->compagnie->name ?? 'Compagnie inconnue';
                }

                if (!isset($compagniesData[$compagnieName])) {
                    $compagniesData[$compagnieName] = [
                        'OM' => 0, 'MOOV' => 0, 'ESPECE' => 0, 'CARTE' => 0, 'total' => 0,
                        'scannes' => 0, 'non_scannes' => 0
                    ];
                }

                $compagniesData[$compagnieName][$paiement->moyenPaiement] += $paiement->montant;
                $compagniesData[$compagnieName]['total'] += $paiement->montant;

                if ($paiement->ticket && $paiement->ticket->dateScan) {
                    $compagniesData[$compagnieName]['scannes'] += 1;
                } else {
                    $compagniesData[$compagnieName]['non_scannes'] += 1;
                }
            }

            $chartData = [
                'type' => 'compagnies',
                'compagnies' => $compagniesData
            ];

        } elseif ($profil === 'Admin compagnie') {
            $garesData = [];
            $totalCompagnie = ['OM' => 0, 'MOOV' => 0, 'ESPECE' => 0, 'CARTE' => 0, 'total' => 0];

            foreach ($paiements as $paiement) {
                $garreName = 'Non scanné';
                if ($paiement->ticket && $paiement->ticket->garre && $paiement->ticket->dateScan) {
                    $garreName = $paiement->ticket->garre->name ?? 'Gare inconnue';
                }

                if (!isset($garesData[$garreName])) {
                    $garesData[$garreName] = [
                        'OM' => 0, 'MOOV' => 0, 'ESPECE' => 0, 'CARTE' => 0, 'total' => 0
                    ];
                }

                $garesData[$garreName][$paiement->moyenPaiement] += $paiement->montant;
                $garesData[$garreName]['total'] += $paiement->montant;

                $totalCompagnie[$paiement->moyenPaiement] += $paiement->montant;
                $totalCompagnie['total'] += $paiement->montant;
            }

            $chartData = [
                'type' => 'gares',
                'gares' => $garesData,
                'total_compagnie' => $totalCompagnie
            ];

        } else {
            // Pour Chef de gare/Réceptionniste
            if (!$garre || !$compagnie) {
                // Si la gare ou la compagnie n'est pas trouvée, retourner des données vides
                $chartData = [
                    'type' => 'dates',
                    'labels' => [],
                    'OM' => [],
                    'MOOV' => [],
                    'ESPECE' => [],
                    'CARTE' => [],
                    'total_gare' => [
                        'OM' => 0, 'MOOV' => 0, 'ESPECE' => 0, 'CARTE' => 0,
                        'total' => 0, 'tickets_scannes' => 0, 'tickets_en_attente' => 0
                    ],
                    'garre_name' => $garre->name ?? 'Gare non trouvée',
                    'compagnie_name' => $compagnie->name ?? 'Compagnie non trouvée',
                    'paiements_en_attente' => 0
                ];
            } else {
                // Calculer les paiements en attente (tickets de SA gare mais non scannés)
                $paiementsEnAttente = Paiements::query()
                    ->whereHas('ticket', function($query) use ($user, $compagnie) {
                        $query->where('idGarre', $user->idGarre)
                              ->whereNull('dateScan')
                              ->whereHas('voyage.trajet', function($q) use ($compagnie) {
                                  $q->where('idCompagnie', $compagnie->id);
                              });
                    })
                    ->count();

                // Regrouper par date pour les graphiques
                $paiementsAvecDate = $paiements->filter(function ($item) {
                    return !is_null($item->created_at);
                });

                $grouped = $paiementsAvecDate->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });

                $labels = [];
                $dataOM = [];
                $dataMOOV = [];
                $dataESPECE = [];
                $dataCARTE = [];

                foreach ($grouped as $date => $items) {
                    $labels[] = $date;
                    $dataOM[] = $items->where('moyenPaiement', 'OM')->sum('montant');
                    $dataMOOV[] = $items->where('moyenPaiement', 'MOOV')->sum('montant');
                    $dataESPECE[] = $items->where('moyenPaiement', 'ESPECE')->sum('montant');
                    $dataCARTE[] = $items->where('moyenPaiement', 'CARTE')->sum('montant');
                }

                // Statistiques complètes pour chef de gare
                $totalGare = [
                    'OM' => $paiements->where('moyenPaiement', 'OM')->sum('montant'),
                    'MOOV' => $paiements->where('moyenPaiement', 'MOOV')->sum('montant'),
                    'ESPECE' => $paiements->where('moyenPaiement', 'ESPECE')->sum('montant'),
                    'CARTE' => $paiements->where('moyenPaiement', 'CARTE')->sum('montant'),
                    'total' => $paiements->sum('montant'),
                    'tickets_scannes' => $paiements->count(),
                    'tickets_en_attente' => $paiementsEnAttente
                ];

                $chartData = [
                    'type' => 'dates',
                    'labels' => $labels,
                    'OM' => $dataOM,
                    'MOOV' => $dataMOOV,
                    'ESPECE' => $dataESPECE,
                    'CARTE' => $dataCARTE,
                    'total_gare' => $totalGare,
                    'garre_name' => $garre->name,
                    'compagnie_name' => $compagnie->name,
                    'paiements_en_attente' => $paiementsEnAttente
                ];
            }
        }

        return view('back.paiements.index', [
            'paiements' => $paiements,
            'chartData' => $chartData,
            'profil' => $profil,
            'compagnies' => $compagnies
        ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view("back.paiements.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaiementsRequest $request)
    {
        //
        $data=$request->validated();

        Paiements::create($data);

        return redirect()->route('paiements.index')->with('success','Paiement enregistrer avec success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiements $paiements)
    {
        //
        $data=request()->$paiements;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paiements $paiements)
    {
        //
         return view("back.paiements.create",['paiements'=>$paiements]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaiementsRequest $request, Paiements $paiements)
    {
        //

        $data = $request->validated();

    $paiements->update($data);

    return redirect()->route('paiements.index')->with('success', 'Paiements mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiements $paiements)
    {
        //
        $paiements->delete();
        return redirect()->route('paiements.index')->with('success', 'Paiements supprimer  avec succès.');
    }
}
