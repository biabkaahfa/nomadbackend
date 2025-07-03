<?php

namespace App\Http\Controllers\Paiement;

use App\Models\Paiements;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaiementsRequest;
use App\Http\Requests\UpdatePaiementsRequest;

class PaiementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $paiements = \App\Models\Paiements::whereNotNull('created_at')->get();

    // Grouper par date
    $grouped = $paiements->groupBy(function ($item) {
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

    $chartData = [
        'labels' => $labels,
        'OM' => $dataOM,
        'MOOV' => $dataMOOV,
        'ESPECE' => $dataESPECE,
        'CARTE' => $dataCARTE,
    ];
   // dd($chartData);

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
