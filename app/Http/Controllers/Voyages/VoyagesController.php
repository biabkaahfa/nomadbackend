<?php

namespace App\Http\Controllers\Voyages;

use App\Models\Bus;
use App\Models\Garres;
use App\Models\Tickets;
use App\Models\Trajets;
use App\Models\Voyages;
use App\Models\GarreTrajets;

use Illuminate\Http\Request;
use App\Models\FrequenceTrajets;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    $user = Auth::user();
    $query = Voyages::with(['trajet.frequences', 'bus', 'tickets', 'trajet']);

    // 🔍 Recherche texte
    if ($request->filled('search')) {
        $search = strtolower($request->search);
        $query->whereHas('trajet', function ($q) use ($search) {
            $q->whereRaw('LOWER(pointDepart) LIKE ?', ["%$search%"])
              ->orWhereRaw('LOWER(pointArrive) LIKE ?', ["%$search%"]);
        });
    }

    // 📅 Tri
    $query->orderBy('dateDepart', $request->input('sort', 'asc'));

    // 🛡️ Filtrage selon le rôle
    if ($user->profil->name === 'Admin compagnie') {
        // Ne garder que les voyages dont le trajet appartient à sa compagnie
        $query->whereHas('trajet', function ($q) use ($user) {
            $q->where('idCompagnie', $user->idCompagnie);
        });

    } elseif (in_array($user->profil->name, ['Chef gare', 'Réceptionniste'])) {
        // Récupérer les ID des trajets liés à la gare du user
        $idTrajets = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');
        $query->whereIn('idTrajet', $idTrajets);
    }

    $voyages = $query->get();

    return view('back.voyages.index', compact('voyages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       $user = Auth::user();
    $buses = collect();
    $trajets = collect();

    if ($user->profil->name === 'Admin général') {
        // Admin général : tous les bus et trajets
        $buses = Bus::all();
        $trajets = Trajets::all();

    } elseif ($user->profil->name === 'Admin compagnie') {
        // Bus de sa compagnie
        $buses = Bus::where('idCompagnie', $user->idCompagnie)->get();

        // Trajets de sa compagnie, liés à ses gares
        $idGares = Garres::where('idCompagnie', $user->idCompagnie)->pluck('id');
        $idTrajets = GarreTrajets::whereIn('idGarre', $idGares)->pluck('idTrajet')->unique();
        $trajets = Trajets::whereIn('id', $idTrajets)
                    ->where('idCompagnie', $user->idCompagnie)
                    ->get();

    } elseif ($user->profil->name === 'Chef gare') {
        // Chef gare : trajets liés à sa gare
        $idTrajets = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet')->unique();
        $trajets = Trajets::whereIn('id', $idTrajets)->get();

        // Bus de la compagnie de sa gare
        $buses = Bus::where('idCompagnie', $user->garre->idCompagnie ?? null)->get();
    }

    return view("back.voyages.create", [
        "trajets" => $trajets,
        "buses" => $buses
    ]);
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
        return view("back.voyages.update",["voyage"=>$voyage,"trajets"=>$trajets,"buses"=>$bus]);
    }

    public function affectation(Voyages $voyage)
    {
        //
       // $voyages=Voyages::all();
       $bus=Bus::all();
       $trajets=Trajets::all();
        return view("back.voyages.affectation",["voyage"=>$voyage,"trajets"=>$trajets,"buses"=>$bus]);
    }
    public function affecterBus(Request $request, Voyages $voyage)
{
    $request->validate([
        'idBus' => 'required|exists:buses,id',
    ]);

    $voyage->idBus = $request->idBus;
    $voyage->save();

    return redirect()->route('voyages.index')->with('success', 'Bus affecté avec succès.');
}




    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateVoyagesRequest $request, Voyages $voyage)
{
    $data = $request->validated();

    $voyage->update([
        'idBus' => $data['idBus'],
        'idTrajet' => $data['idTrajet'],
        'dateDepart' => $data['dateDepart'],
        'heuresDepart' => $data['heuresDepart'],
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
