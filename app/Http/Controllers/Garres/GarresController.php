<?php

namespace App\Http\Controllers\Garres;

use App\Models\Garres;
use App\Models\Compagnie;
use App\Http\Requests\Gare\StoreGarresRequest;
use App\Http\Requests\Gare\UpdateGarresRequest;
use App\Models\Compagnies;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GarresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

          
        try {
            $query = Garres::with([ 'compagnie']);

            // Filtrage par recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                    //   ->orWhere('email', 'like', "%{$search}%")
                    //   ->orWhere('telephone', 'like', "%{$search}%");
                });
            }

            // Filtrage par compagnie
            if ($request->filled('compagnie')) {
                $query->where('idCompagnie', $request->compagnie);
            }

            $gares = $query->paginate(15);
            
            $compagnies = Compagnies::all();

            return view('gares.index', compact('gares', 'compagnies'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
        }
    

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $compagnies = Compagnies::all();
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