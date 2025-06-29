<?php

namespace App\Http\Controllers;

use App\Models\FrequenceTrajets;
use App\Models\Trajets;
use App\Http\Requests\StoreFrequenceTrajetsRequest;
use App\Http\Requests\UpdateFrequenceTrajetsRequest;

class FrequenceTrajetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         //
        $frequences=FrequenceTrajets::all();

        return view("back.Frequences.index",['frequences'=>$frequences]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
       $trajets=Trajets::all();

        return view("back.Frequences.create",['trajets'=>$trajets]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFrequenceTrajetsRequest $request)
    {
        //
       // $dd('envoyer');
        $data = $request->validated();
        FrequenceTrajets::create($data);

        return redirect()->route('frequences.index')->with('success', 'Frequence mis à jour avec succès.');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(FrequenceTrajets $frequenceTrajets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FrequenceTrajets $frequence)
    {
        //
        $tajets=Trajets::all();
        return view("back.Frequences.create",['frequence'=>$frequence,'trajets'=>$tajets]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFrequenceTrajetsRequest $request, FrequenceTrajets $frequence)
    {
        //
       // $dd('non hein');
        $data = $request->validated();

       $frequence->update($data);

       return redirect()->route('frequences.index')->with('success', 'Frequence mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FrequenceTrajets $frequence)
    {
        //
         $frequence->delete();
        return redirect()->route('frequences.index')->with('success', 'Bus supprimer avec succès.');
    }
}
