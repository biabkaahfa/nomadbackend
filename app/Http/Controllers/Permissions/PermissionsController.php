<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
// use App\Http\Requests\StorePermissionsRequest;
// use App\Http\Requests\UpdatePermissionsRequest;

// use App\Models\Permissions;
use App\Http\Requests\StorePermissionsRequest;
use App\Http\Requests\UpdatePermissionsRequest;

// use App\Http\Controllers\Controller;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         $permissions = Permissions::all();
        return view('back.permissions.index',['permissions'=>$permissions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        return view('back.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionsRequest $request)
    {
        $validated = $request->validated();

        $permission = Permissions::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permissions $permissions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permissions $permission)
    {
        //
         return view('back.permissions.create',['permission'=>$permission]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionsRequest $request, Permissions $permission)
    {

        
        $validated = $request->validated();

        $permission->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permissions $permission)
    {
        // 
        $permission->delete();
         return redirect()->route('permissions.index')->with('success', 'permission supprimer  avec succès.');

    }
}
