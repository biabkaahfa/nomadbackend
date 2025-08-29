<?php

namespace App\Http\Controllers\Notes;

use App\Models\Notes;
use App\Models\Ticket;
use App\Models\Trajets;
use Illuminate\Support\Carbon;
use App\Models\Voyages;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreNotesRequest;
use App\Http\Requests\UpdateNotesRequest;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
//     public function index()
//     {


// $latestNotes = DB::table('notes as n')
//     ->join('tickets as t', 'n.idTicket', '=', 't.id')
//     ->join('voyages as v', 't.idVoyage', '=', 'v.id')
//     ->join('trajets as tr', 'v.idTrajet', '=', 'tr.id')
//     ->select(
//         'n.*',
//         'v.id as idVoyage',
//         'v.dateDepart',
//         'tr.pointDepart',
//         'tr.pointArrive'
//     )
//     ->orderBy('n.dateNote', 'desc')
//     ->get()
//     ->map(function ($note) {
//         $note->nomVoyage = $note->pointDepart . ' → ' . $note->pointArrive . ' le ' . Carbon::parse($note->dateDepart)->format('d/m/Y');
//         return $note;
//     })
//     ->groupBy('idVoyage')
//     ->map(function ($group) {
//         return $group->take(5); // 5 dernières notes par voyage
//     });

//     // Statistiques globales

// $stats = DB::table('notes')
//     ->join('tickets', 'notes.idTicket', '=', 'tickets.id')
//     ->join('voyages', 'tickets.idVoyage', '=', 'voyages.id')
//     ->join('trajets', 'voyages.idTrajet', '=', 'trajets.id')
//     ->select(
//         DB::raw("CONCAT(trajets.pointDepart, ' → ', trajets.pointArrive, ' le ', DATE_FORMAT(voyages.dateDepart, '%d/%m/%Y')) as voyage"),
//         DB::raw('COUNT(notes.id) as total_notes'),
//         DB::raw('AVG(notes.note) as moyenne'),
//         DB::raw('MAX(notes.note) as max_note'),
//         DB::raw('MIN(notes.note) as min_note')
//     )
//     ->groupBy('voyage')
//     ->get();

//     return view('back.notes.index', [
//         'latestNotes' => $latestNotes,
//         'stats' => $stats,
//     ]);

//         //
//         // $notes=Notes::all()->limit(5);
//         // return view('back.notes.index',['notes'=>$notes]);
//     }
public function index()
{
    $user = Auth::user();

    $notesQuery = DB::table('notes as n')
        ->join('tickets as t', 'n.idTicket', '=', 't.id')
        ->join('voyages as v', 't.idVoyage', '=', 'v.id')
        ->join('trajets as tr', 'v.idTrajet', '=', 'tr.id');

    // ⚠️ Si l'utilisateur n'est PAS admin général, on filtre sur idCompagnie
    if ($user->profil?->name !== 'Admin général') {
        $notesQuery->where('tr.idCompagnie', $user->idCompagnie);
    }

    $latestNotes = $notesQuery
        ->select(
            'n.*',
            'v.id as idVoyage',
            'v.dateDepart',
            'tr.pointDepart',
            'tr.pointArrive'
        )
        ->orderBy('n.dateNote', 'desc')
        ->get()
        ->map(function ($note) {
            $note->nomVoyage = $note->pointDepart . ' → ' . $note->pointArrive . ' le ' . Carbon::parse($note->dateDepart)->format('d/m/Y');
            return $note;
        })
        ->groupBy('idVoyage')
        ->map(function ($group) {
            return $group->take(5); // 5 dernières notes par voyage
        });

    // Statistiques globales
    $statsQuery = DB::table('notes')
        ->join('tickets', 'notes.idTicket', '=', 'tickets.id')
        ->join('voyages', 'tickets.idVoyage', '=', 'voyages.id')
        ->join('trajets', 'voyages.idTrajet', '=', 'trajets.id');

    if ($user->profil?->name !== 'admin général') {
        $statsQuery->where('trajets.idCompagnie', $user->idCompagnie);
    }

    $stats = $statsQuery
        ->select(
            DB::raw("CONCAT(trajets.pointDepart, ' → ', trajets.pointArrive, ' le ', DATE_FORMAT(voyages.dateDepart, '%d/%m/%Y')) as voyage"),
            DB::raw('COUNT(notes.id) as total_notes'),
            DB::raw('AVG(notes.note) as moyenne'),
            DB::raw('MAX(notes.note) as max_note'),
            DB::raw('MIN(notes.note) as min_note')
        )
        ->groupBy('voyage')
        ->get();

    return view('back.notes.index', [
        'latestNotes' => $latestNotes,
        'stats' => $stats,
    ]);
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
    public function store(StoreNotesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notes $notes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notes $notes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotesRequest $request, Notes $notes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notes $notes)
    {
        //
    }
}
