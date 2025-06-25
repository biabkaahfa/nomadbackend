<?php

namespace App\Http\Controllers;

use App\Models\Paiements;
use App\Http\Requests\StorePaiementsRequest;
use App\Http\Requests\UpdatePaiementsRequest;

class PaiementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaiementsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiements $paiements)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paiements $paiements)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaiementsRequest $request, Paiements $paiements)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiements $paiements)
    {
        //
    }
}
