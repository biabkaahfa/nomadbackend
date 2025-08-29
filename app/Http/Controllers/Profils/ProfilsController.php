<?php

namespace App\Http\Controllers\Profils;

use App\Models\Profils;
use App\Models\Permissions;
use App\Http\Requests\StoreProfilsRequest;
use App\Http\Requests\UpdateProfilsRequest;
use App\Http\Controllers\Controller;

class ProfilsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // //
        // $profils = Profils::with('permissions')->get();
        // $profils=Profils::all();
        // return view('back.profils.index',compact('profils'));
        $profils = Profils::with('permissions')->get();
        return view('back.profils.index', compact('profils'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         $permissions = Permissions::all();
        return view("back.profils.create",compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfilsRequest $request)
    {
        //
         $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    $profil = Profils::create([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
    ]);

    if (!empty($validated['permissions'])) {
        $profil->permissions()->attach($validated['permissions']);
    }

    return redirect()->route('profils.index')->with('success', 'Profil créé avec permissions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Profils $profils)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Profils $profil)
    {
        //
        $permissions = Permissions::all();
        return view('back.profils.create',['profil'=>$profil,'permissions'=>$permissions]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfilsRequest $request, Profils $profil)
    {
        //

// $profil->update($request->validated());



        $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    $profil->update([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
    ]);

   $profil->permissions()->sync($request->input('permissions', []));

    return redirect()->route('profils.index')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profils $profils)
    {
        //
        $profils->delete();
         return redirect()->route('profils.index')->with('success', 'Profil supprimer  avec succès.');
    }
}
