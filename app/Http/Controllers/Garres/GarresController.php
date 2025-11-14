<?php

namespace App\Http\Controllers\Garres;

use App\Models\Garres;
use App\Models\Compagnie;
use App\Models\Compagnies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Gare\StoreGarresRequest;
use App\Http\Requests\Gare\UpdateGarresRequest;

class GarresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        try {
        $user = Auth::user();

        $query = Garres::with(['compagnie', 'trajets']);

        // 🔍 Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // 🌐 Filtrage dynamique selon le rôle
        if ($user->profil->name === 'Admin compagnie') {
            $query->where('idCompagnie', $user->idCompagnie);
        } elseif ($user->profil->name === 'Chef gare') {
            $query->where('id', $user->idGarre);
        }

        // 🔁 Filtrage manuel de compagnie via requête
        if ($request->filled('compagnie')) {
            $query->where('idCompagnie', $request->compagnie);
        }

        $gares = $query->paginate(15);

        // 🔁 Pour le filtre dropdown (seulement si admin général)
        $compagnies = $user->profil->name === 'Admin général'
            ? Compagnies::all()
            : ($user->profil->name === 'Admin compagnie'
                ? Compagnies::where('id', $user->idCompagnie)->get()
                : collect());

        return view('gares.index', compact('gares', 'compagnies'));

    } catch (\Exception $e) {
        \Log::error('Erreur lors de la récupération des gares : ' . $e->getMessage());
        return back()->with('error', 'Erreur de récupération des données');
    }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       $user = Auth::user();

    // 🚫 Refuser l'accès aux rôles non autorisés
    if (!in_array($user->profil->name, ['Admin général', 'Admin compagnie'])) {
        abort(403, 'Accès non autorisé.');
    }

    // ✅ Admin général → toutes les compagnies
    if ($user->profil->name === 'Admin général') {
        $compagnies = Compagnies::all();
    }

    // ✅ Admin compagnie → uniquement sa propre compagnie
    if ($user->profil->name === 'Admin compagnie') {
        $compagnies = Compagnies::where('id', $user->idCompagnie)->get();
    }

    $mode = 'create';
    return view('gares.create', compact('compagnies', 'mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGarresRequest $request)
    {
        try {
            Garres::create($request->validated());

            return redirect()->route('garres.index')
                           ->with('success', 'Gare créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erreur lors de la création de la gare : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Garres $garre)
    {
        $garre->load('compagnie');
        $mode = 'show';
        return view('gares.create', compact('garre', 'mode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Garres $garre)
    {
        $compagnies = Compagnies::all();
        $mode = 'edit';
        return view('gares.create', compact('garre', 'compagnies', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGarresRequest $request, Garres $garre)
    {
        try {
            $garre->update($request->validated());

            return redirect()->route('garres.index')
                           ->with('success', 'Gare modifiée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erreur lors de la modification de la gare : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Garres $garre)
    {
        try {
            $garre->delete();

            return redirect()->route('gares.index')
                           ->with('success', 'Gare supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression de la gare : ' . $e->getMessage());
        }
    }
}
