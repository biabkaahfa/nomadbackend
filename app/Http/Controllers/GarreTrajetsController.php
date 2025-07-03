<?php

namespace App\Http\Controllers;

use App\Models\GarreTrajets;
use App\Models\Garres;
use App\Models\Trajets;
use App\Http\Requests\StoreGarreTrajetsRequest;
use App\Http\Requests\UpdateGarreTrajetsRequest;
use Illuminate\Http\Request;

class GarreTrajetsController extends Controller
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
        $garres = Garres::with('trajets')->get();
    $trajets = Trajets::all();

    return view('back.Trajets.affectation', compact('garres', 'trajets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreGarreTrajetsRequest $request)
    // {
    //     //
    // }
    public function store(Request $request)
{
    foreach ($request->affectations as $idGarre => $trajetIds) {
        \App\Models\GarreTrajets::where('idGarre', $idGarre)->delete();

        foreach ($trajetIds as $idTrajet) {
            \App\Models\GarreTrajets::create([
                'idGarre' => $idGarre,
                'idTrajet' => $idTrajet,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Les trajets ont été affectés aux gares avec succès.');
    }


    /**
     * Display the specified resource.
     */
    public function show(GarreTrajets $garreTrajets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GarreTrajets $garreTrajets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGarreTrajetsRequest $request, GarreTrajets $garreTrajets)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GarreTrajets $garreTrajets)
    {
        //
    }
}
