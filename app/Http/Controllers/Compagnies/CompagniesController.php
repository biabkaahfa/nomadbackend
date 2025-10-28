<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Compagnies;

use App\Models\Compagnies;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompagniesRequest;
use App\Http\Requests\UpdateCompagniesRequest;
use Illuminate\Support\Facades\Storage;

class CompagniesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $compagnies=Compagnies::all();
        return view("back.compagnies.index",["compagnies"=>$compagnies]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('back.compagnies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompagniesRequest $request)
    {
        //
         //
        $request->validated($request->all());

        $image=$request->logo;

        if($image != null && !$image->getError()){

            $image=$request->logo->store('asset','public');


        }


        $compagnies= Compagnies::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'type'=>$request->type,
            'telephone'=>$request->telephone,
            'description'=>$request->description,

            'logo'=>$image,




           ] );


           return to_route('compagnies.index')->with('success','compagnies enregistrer avec success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Compagnies $compagnies)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Compagnies $compagny)
    {
        //
        return view("back.compagnies.create",['compagny'=>$compagny]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompagniesRequest $request, Compagnies $compagny)
    {   //
        $request->validated($request->all());

        $image=$request->logo;

        if($image != null && !$image->getError()){

            if($compagny->logo){
                Storage::disk('public')->delete($compagny->logo);
            }

            $image=$request->logo->store('abonnements','public');


        }



        if($image==null){

            $compagny->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'type'=>$request->type,
            'telephone'=>$request->telephone,
            'description'=>$request->description,

            //'logo'=>$image,


               ] );

        }else{

        $compagny->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'type'=>$request->type,
            'telephone'=>$request->telephone,
            'description'=>$request->description,

            'logo'=>$image,



           ] );}





           return to_route('compagnies.index')->with('success','Compagnies Modifier  avec success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compagnies $compagny)
    {
        //
        $compagny->delete();
          return to_route('compagnies.index')->with('success','Compagnies Supprimer  avec success');
    }
}
