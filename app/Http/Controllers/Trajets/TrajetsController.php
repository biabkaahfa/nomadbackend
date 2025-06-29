<?php

namespace App\Http\Controllers\Trajets;
use App\Http\Controllers\Controller;
use App\Models\Trajets;
use App\Models\FrequenceTrajets;
use App\Http\Requests\StoreTrajetsRequest;
use App\Http\Requests\UpdateTrajetsRequest;

class TrajetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $trajets=Trajets::all();
        return view("back.Trajets.index",["trajets"=>$trajets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         $frequences = FrequenceTrajets::all();
    
        return view("back.Trajets.create",["frequences"=>$frequences]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreTrajetsRequest $request)
{
    $data = $request->validated(); // Validation depuis StoreTrajetsRequest

    Trajets::create($data);
    

    return redirect()->route('trajets.index')->with('success', 'Trajet ajouté avec succès.');
}


    /**
     * Display the specified resource.
     */
    public function show(Trajets $trajets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trajets $trajet)
    {
        //
        $frequences = FrequenceTrajets::all();
         return view("back.Trajets.create",["trajet"=>$trajet,"frequences"=>$frequences]);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateTrajetsRequest $request, Trajets $trajet)
{
    $data = $request->validated(); // Validation depuis UpdateTrajetsRequest

    $trajet->update($data);

    return redirect()->route('trajets.index')->with('success', 'Trajet mis à jour avec succès.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trajets $trajet)
    {
        //
        $trajet::delete();
    }
}
