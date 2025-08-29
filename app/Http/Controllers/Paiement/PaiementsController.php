<?php

namespace App\Http\Controllers\Paiement;

use App\Models\Paiements;
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
    $profil = $user->profils?->name; // Assure-toi que la relation s'appelle `profils`

    $paiements = Paiements::with(['ticket.voyage.trajet']); // Précharge les relations

    if ($profil === 'Admin compagnie') {
        // Filtrer les paiements liés aux voyages/trajets de la même compagnie
        $paiements = $paiements->whereHas('ticket.voyage.trajet', function ($q) use ($user) {
            $q->where('idCompagnie', $user->idCompagnie);
        });
    } elseif (in_array($profil, ['Chef gare', 'Réceptionniste'])) {
        // Récupère les trajets associés à la gare du chef de gare ou réceptionniste
        $trajetIds = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');

        $paiements = $paiements->whereHas('ticket.voyage', function ($q) use ($trajetIds) {
            $q->whereIn('idTrajet', $trajetIds);
        });
    }

    // Exécuter la requête
    $paiements = $paiements->whereNotNull('created_at')->get();

    // Grouper les paiements par date
    $grouped = $paiements->groupBy(function ($item) {
        return $item->created_at->format('Y-m-d');
    });

    // Préparer les données pour le graphique
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

    $chartData = [
        'labels' => $labels,
        'OM' => $dataOM,
        'MOOV' => $dataMOOV,
        'ESPECE' => $dataESPECE,
        'CARTE' => $dataCARTE,
    ];

    return view('back.paiements.index', [
        'paiements' => $paiements,
        'chartData' => $chartData
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

        Paiments::create($data);

        return redirect()->route('paiements.index')->with('success','Paiement enregistrer avec success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiements $paiements)
    {
        //
        $data=request->$paiements;
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

    $bus->update($data);

    return redirect()->route('paiements.index')->with('success', 'Paiements mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiements $paiements)
    {
        //
        delete()->$paiements;
        return redirect()->route('paiements.index')->with('success', 'Paiements supprimer  avec succès.');
    }
}
