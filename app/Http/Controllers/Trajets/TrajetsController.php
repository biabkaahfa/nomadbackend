<?php

namespace App\Http\Controllers\Trajets;
use App\Models\Trajets;
use App\Models\Compagnies;
use App\Models\FrequenceTrajets;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTrajetsRequest;
use App\Http\Requests\UpdateTrajetsRequest;

class TrajetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $user = Auth::user();
    $trajets = collect(); // initialisation vide

    if ($user->profil->name === 'Admin général') {
        $trajets = Trajets::all();

    } elseif ($user->profil->name === 'Admin compagnie') {
        $trajets = Trajets::where('idCompagnie', $user->idCompagnie)->get();

    } elseif ($user->profil->name === 'chef gare') {
        $idTrajets = \App\Models\GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet')->unique();
        $trajets = Trajets::whereIn('id', $idTrajets)->get();
    }

    return view("back.Trajets.index", ["trajets" => $trajets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $user = Auth::user();
    $frequences = collect();
    $compagnies = collect();

    if ($user->profil->name === 'Admin général') {
        $frequences = FrequenceTrajets::all();
        $compagnies = Compagnies::all();

    } elseif ($user->profil->name === 'Admin compagnie') {
        $compagnies = Compagnies::where('id', $user->idCompagnie)->get();

        // On récupère les gares de la compagnie
        $idGarres = \App\Models\Garres::where('idCompagnie', $user->idCompagnie)->pluck('id');
        $frequences = FrequenceTrajets::whereIn('idGarre', $idGarres)->get();

    } elseif ($user->profil->name === 'chef de gare') {
        $compagnies = Compagnies::where('id', $user->idCompagnie)->get();
        $frequences = FrequenceTrajets::where('idGarre', $user->idGarre)->get();
    }

    return view("back.Trajets.create", [
        "frequences" => $frequences,
        'compagnies' => $compagnies
    ]);
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
        $compagnies=Compagnies::all();
        $frequences = FrequenceTrajets::all();
         return view("back.Trajets.create",["trajet"=>$trajet,"frequences"=>$frequences,'compagnies'=>$compagnies,]);
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
