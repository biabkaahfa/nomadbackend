<?php

namespace App\Http\Controllers;

use App\Models\Garres;
use App\Models\Compagnie;
use App\Http\Requests\Gare\StoreGareRequest;
use App\Http\Requests\Gare\UpdateGareRequest;
use App\Models\Compagnies;
use Illuminate\Http\Request;

class GarresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $garres = Garres::with('compagnie')->paginate(10);
        return view('garres.index', compact('garres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $compagnies = Compagnies::all();
        $mode = 'create';
        return view('garres.form', compact('compagnies', 'mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGareRequest $request)
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
        return view('garres.form', compact('garre', 'mode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Garres $garre)
    {
        $compagnies = Compagnies::all();
        $mode = 'edit';
        return view('garres.form', compact('garre', 'compagnies', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGareRequest $request, Garres $garre)
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
            
            return redirect()->route('garres.index')
                           ->with('success', 'Gare supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression de la gare : ' . $e->getMessage());
        }
    }
}