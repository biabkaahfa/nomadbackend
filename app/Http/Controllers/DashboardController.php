<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
//use Illuminate\Http\Request;
use App\Models\Compagnies;
use App\Models\Voyages;
use App\Models\Bus;
use App\Models\Garres;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Paiements;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    //

   //use Illuminate\Support\Str;

   private function voyagesDuJourParGarre($garre)
{
    // Récupère tous les trajets de la gare
    $trajetIds = $garre->trajets->pluck('id')->toArray();

    // Récupère tous les voyages de ces trajets, pour aujourd’hui
    return \App\Models\Voyages::with(['trajet', 'tickets', 'bus'])
        ->whereDate('dateDepart', today())
        ->whereIn('idTrajet', $trajetIds)
        ->get();
}


public function index()
{
    $user = auth()->user();
    $profil = Str::lower($user->profil->name);

    switch ($profil) {
        case 'admin général':
            $compagnies = Compagnies::all();
            $voyagesEffectues = Voyages::where('dateDepart', '<', today())->count();
            $voyagesJour = Voyages::whereDate('dateDepart', today())->count();
            $gains = Paiements::sum('montant');

            $compagnies->each(function($compagnie) {
                $compagnie->trajetsCount = $compagnie->trajets()->count();
                $compagnie->garresCount = $compagnie->garres()->count();
                $compagnie->busesCount = $compagnie->buses()->count();
            });

            return view('back.dashboard', compact('profil', 'compagnies', 'voyagesEffectues', 'voyagesJour', 'gains'));

        case 'admin compagnie':
            $compagnie = $user->compagnie;
            $buses = $compagnie->buses;
            $garres = $compagnie->garres;

            // Récupération des trajets de la compagnie
            $trajetIds = $compagnie->trajets->pluck('id')->toArray();
            $voyagesJour = Voyages::whereIn('idTrajet', $trajetIds)
                ->whereDate('dateDepart', today())
                ->count();

            return view('back.dashboard', compact('profil', 'buses', 'garres', 'voyagesJour'));

        case 'chef de gare':
            $garre = $user->garre;
            $voyages = $this->voyagesDuJourParGarre($garre);
            $personnels = User::where('idGarre', $garre->id)->count();
            return view('back.dashboard', compact('profil', 'voyages', 'personnels'));

        case 'réceptionniste':
            $garre = $user->garre;
            $voyages = $this->voyagesDuJourParGarre($garre);
            return view('back.dashboard', compact('profil', 'voyages'));

        default:
            abort(403, 'Profil non autorisé.');
    }
}



}
