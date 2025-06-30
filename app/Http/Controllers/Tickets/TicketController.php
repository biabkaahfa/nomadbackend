<?php

//namespace App\Http\Controllers;
namespace App\Http\Controllers\Tickets;

use App\Models\Ticket;
use App\Models\Voyages;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //
        $ticket=Ticket::all();
        return view("back.Tickets.index",['tickets'=>$ticket]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       $voyages = Voyages::all();
    return view("back.Tickets.create", compact('voyages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        //
        $data= $request->validated();
         $data['typeAchat'] = 'sur_place';
        Ticket::create($data);
        
        return redirect()->route('tickets.index')->with('success', 'Ticket enregistré avec succès.');


    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
      // $ticket=Ticket::all();
      $voyages=Voyages::all();
        return view("back.Tickets.create",['ticket'=>$ticket,'voyages'=>$voyages]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        //
       // dd('envoyer');
      
    $data = $request->validated();
    $ticket->update($data);

    return redirect()->route('tickets.index')->with('success', 'Ticket modifié avec succès.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
        $ticket->delete();

       return redirect()->route('tickets.index')->with('success', 'Ticket supprimer avec succès.');

    }
}
