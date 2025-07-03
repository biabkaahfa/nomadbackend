<?php

namespace App\Http\Controllers\Voyages;

use App\Models\Voyages;
use App\Models\Bus;
use App\Models\Trajets;
use App\Models\Tickets;
use App\Models\FrequenceTrajets;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoyagesRequest;
use App\Http\Requests\UpdateVoyagesRequest;

class VoyagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        
      $query = Voyages::with(['trajet.frequences', 'bus', 'tickets']);

    // 🔎 Filtrage par recherche
    if ($request->filled('search')) {
        $search = strtolower($request->search);
        $query->whereHas('trajet', function ($q) use ($search) {
            $q->whereRaw('LOWER(pointDepart) LIKE ?', ["%$search%"])
              ->orWhereRaw('LOWER(pointArrive) LIKE ?', ["%$search%"]);
        });
    }

    // 🔄 Tri par date
    if ($request->filled('sort') && in_array($request->sort, ['asc', 'desc'])) {
        $query->orderBy('dateDepart', $request->sort);
    } else {
        $query->orderBy('dateDepart', 'asc');
    }

    $voyages = $query->get();

    return view('back.Voyages.index', compact('voyages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $bus=Bus::all();
       $trajets=Trajets::all();
        return view("back.voyages.create",["trajets"=>$trajets,"buses"=>$bus]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVoyagesRequest $request)
    {
        //
        $data = $request->validated();

    $voyage = new Voyages();
    $voyage->idBus = $data['idBus'];
    $voyage->idTrajet = $data['idTrajet'];
    $voyage->dateDepart = $data['dateDepart'];
    $voyage->heuresDepart = $data['heuresDepart'];
    // $voyage->status = $data['status'] ?? 'ACTIF';       
    $voyage->save();

    return redirect()->route('voyages.index')->with('success', 'Voyage créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Voyages $voyages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voyages $voyage)
    {
        //
       // $voyages=Voyages::all();
       $bus=Bus::all();
       $trajets=Trajets::all();
        return view("back.voyages.create",["voyage"=>$voyage,"trajets"=>$trajets,"buses"=>$bus]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVoyagesRequest $request, Voyages $voyage)
    {

       // dd('Requête reçue', $request->all());
        // dd('Formulaire bien envoyé');
        
         $data = $request->validated();

    $voyage->update([
        'idBus' => $data['idBus'],
        'idTrajet' => $data['idTrajet'],
        'dateDepart' => $data['dateDepart'],
        'heuresDepart' => $data['heuresDepart'],
        // 'status' => $data['status'],
    ]);

    return redirect()->route('voyages.index')->with('success', 'Voyage mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voyages $voyage)
    {
        //
       
         $voyage->delete();
          return to_route('voyages.index')->with('success','Voyages Supprimer  avec success');
    }
}
