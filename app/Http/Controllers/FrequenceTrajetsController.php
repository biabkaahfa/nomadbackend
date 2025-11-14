<?php

namespace App\Http\Controllers;

use App\Models\Trajets;
use App\Models\User;
use App\Models\GarreTrajets;
use App\Models\FrequenceTrajets;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
    $frequences = collect(); // Collection vide par défaut

    if ($user->profil->name === 'Admin général') {
        $frequences = FrequenceTrajets::with('trajet')->get();

    } elseif ($user->profil->name === 'Admin compagnie') {
        // On récupère les trajets de cette compagnie
        $idTrajets = Trajets::where('idCompagnie', $user->idCompagnie)->pluck('id');
        $frequences = FrequenceTrajets::with('trajet')
                        ->whereIn('idTrajet', $idTrajets)
                        ->get();

    } elseif ($user->profil->name === 'Chef gare') {
        // Obtenir les trajets liés à sa gare via la table garre_trajets
        $idTrajets = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');
        $frequences = FrequenceTrajets::with('trajet')
                        ->whereIn('idTrajet', $idTrajets)
                        ->get();
    }

    return view("back.Frequences.index", ['frequences' => $frequences]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $user = Auth::user();
    $trajets = collect(); // Vide par défaut

    if ($user->profil->name === 'Admin général') {
        // Tous les trajets
        $trajets = Trajets::all();

    } elseif ($user->profil->name === 'Admin compagnie') {
        // Trajets liés à la compagnie de l'utilisateur
        $trajets = Trajets::where('idCompagnie', $user->idCompagnie)->get();

    } elseif ($user->profil->name === 'Chef gare') {
        // Trajets liés à la gare du chef
        $trajetIds = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');
        $trajets = Trajets::whereIn('id', $trajetIds)->get();

    } else {
        // Autres profils : accès interdit
        abort(403, "Accès refusé.");
    }

    return view("back.Frequences.create", ['trajets' => $trajets]);
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
