<?php

namespace App\Http\Controllers\Bus;

use App\Models\Bus;
use App\Models\Compagnies;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBusRequest;
use App\Http\Requests\UpdateBusRequest;
use Illuminate\Support\Facades\Auth;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     //
    //     $buses=Bus::all();
    //     return view("back.buses.index",['buses'=>$buses]);
    // }
    public function index()
{
    $user = Auth::user();

    // Vérifie le nom du profil
    $profil = $user->profil?->name; // ou ->libelle si ton champ s'appelle comme ça

    // Si admin général → tous les bus
    if ($profil === 'Admin général') {
        $buses = Bus::all();
    }
    // Si admin compagnie → uniquement les bus de sa compagnie
    elseif ($profil === 'Admin compagnie') {
        $buses = Bus::where('idCompagnie', $user->idCompagnie)->get();
    }
    // Sinon → aucun bus ou comportement par défaut
    else {
        $buses = collect(); // liste vide
    }

    return view("back.buses.index", ['buses' => $buses]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

    $user = Auth::user();

    // Vérifie le nom du profil
    $profil = $user->profil?->name; // ou ->libelle si ton champ s'appelle comme ça

    // dd($profil);

    // Si admin général → tous les bus
    if ($profil === 'Admin général') {
        $compagnies=Compagnies::all();
    }
    // Si admin compagnie → uniquement les bus de sa compagnie
    elseif ($profil === 'Admin compagnie') {

       $compagnies=Compagnies::where('id', $user->idCompagnie)->get();
      // dd($compagnies);
    }
    // Sinon → aucun bus ou comportement par défaut
    else {
       $compagnies = collect(); // liste vide
    }

    // return view("back.buses.index", ['buses' => $buses]);


        //$compagnies=Compagnies::all();
        return view("back.buses.create",['compagnies'=>$compagnies]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusRequest $request)
    {
        //
        $data = $request->validated();

    Bus::create($data);

    return redirect()->route('buses.index')->with('success', 'Bus créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bus $bus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bus $bus)
    {
        //
         //
        $compagnies=Compagnies::all();
        return view("back.buses.create",['compagnies'=>$compagnies,"buses"=>$bus]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusRequest $request, Bus $bus)
    {
        //
         $data = $request->validated();

    $bus->update($data);

    return redirect()->route('buses.index')->with('success', 'Bus mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bus $bus)
    {
        //
        $bus->delete();
        return redirect()->route('buses.index')->with('success', 'Bus supprimer avec succès.');
    }
}
