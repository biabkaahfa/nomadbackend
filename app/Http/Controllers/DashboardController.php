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

//    private function voyagesDuJourParGarre($garre)
// {
//     // Récupère tous les trajets de la gare
//     $trajetIds = $garre->trajets->pluck('id')->toArray();

//     // Récupère tous les voyages de ces trajets, pour aujourd’hui
//     return \App\Models\Voyages::with(['trajet', 'tickets', 'bus'])
//         ->whereDate('dateDepart', today())
//         ->whereIn('idTrajet', $trajetIds)
//         ->get();
// }


// public function index()
// {
//     $user = auth()->user();
//     $profil = Str::lower($user->profil->name);

//     switch ($profil) {
//         case 'admin général':
//             $compagnies = Compagnies::all();
//             $voyagesEffectues = Voyages::where('dateDepart', '<', today())->count();
//             $voyagesJour = Voyages::whereDate('dateDepart', today())->count();
//             $gains = Paiements::sum('montant');

//             $compagnies->each(function($compagnie) {
//                 $compagnie->trajetsCount = $compagnie->trajets()->count();
//                 $compagnie->garresCount = $compagnie->garres()->count();
//                 $compagnie->busesCount = $compagnie->buses()->count();
//             });

//             return view('back.dashboard', compact('profil', 'compagnies', 'voyagesEffectues', 'voyagesJour', 'gains'));

//         case 'admin compagnie':
//             $compagnie = $user->compagnie;
//             $buses = $compagnie->buses;
//             $garres = $compagnie->garres;
//            // dd($garres);

//             // Récupération des trajets de la compagnie
//             $trajetIds = $compagnie->trajets->pluck('id')->toArray();
//             $voyagesJour = Voyages::whereIn('idTrajet', $trajetIds)
//                 ->whereDate('dateDepart', today())
//                 ->count();
//                 //dd($voyagesJour);

//             return view('back.dashboard', compact('profil', 'buses', 'garres', 'voyagesJour'));

//         case 'chef de gare':
//             $garre = $user->garre;
//             $voyages = $this->voyagesDuJourParGarre($garre);
//             $personnels = User::where('idGarre', $garre->id)->count();
//             return view('back.dashboard', compact('profil', 'voyages', 'personnels'));

//         case 'réceptionniste':
//             $garre = $user->garre;
//             $voyages = $this->voyagesDuJourParGarre($garre);
//             return view('back.dashboard', compact('profil', 'voyages'));

//         default:
//             abort(403, 'Profil non autorisé.');
//     }
// }

public function index()
    {
        $user = auth()->user();
        $profil = Str::lower($user->profil->name);

        switch ($profil) {
            case 'admin général':
                return $this->adminGeneralDashboard();

            case 'admin compagnie':
                return $this->adminCompagnieDashboard($user);

            case 'chef de gare':
                return $this->chefGareDashboard($user);

            case 'réceptionniste':
                return $this->receptionnisteDashboard($user);

            default:
                abort(403, 'Profil non autorisé.');
        }
    }

    private function adminGeneralDashboard()
    {
        $user = auth()->user();
        $profil = 'admin général';

        // Statistiques principales
        $compagnies = Compagnies::withCount(['trajets', 'garres', 'buses'])->get();
        $voyagesEffectues = Voyages::where('dateDepart', '<', today())->count();
        $voyagesJour = Voyages::whereDate('dateDepart', today())->count();
        $gains = Paiements::sum('montant');
        $totalTickets = Ticket::count();
        $ticketsAujourdhui = Ticket::whereDate('created_at', today())->count();

        // Données pour les graphiques
        $voyagesMensuels = $this->getVoyagesMensuels();
        $repartitionCompagnies = $this->getRepartitionCompagnies();
        $topTrajets = $this->getTopTrajets();

        return view('back.dashboard', compact(
            'profil', 'compagnies', 'voyagesEffectues', 'voyagesJour',
            'gains', 'totalTickets', 'ticketsAujourdhui', 'voyagesMensuels',
            'repartitionCompagnies', 'topTrajets'
        ));
    }

    private function adminCompagnieDashboard($user)
    {
        $profil = 'admin compagnie';
        $compagnie = $user->compagnie;

        // Statistiques de base
        $buses = $compagnie->buses;
        $garres = $compagnie->garres()->withCount(['trajets'])->get();

        // Voyages du jour avec les gares associées
        $voyagesJour = Voyages::whereIn('idTrajet', $compagnie->trajets->pluck('id'))
            ->whereDate('dateDepart', today())
            ->count();

        // Voyages par gare - version corrigée avec la relation many-to-many
        $voyagesParGarre = [];
        foreach ($garres as $garre) {
            // Récupérer les trajets associés à cette gare
            $trajetIds = $garre->trajets->pluck('id');

            $voyages = Voyages::whereIn('idTrajet', $trajetIds)
                ->whereDate('dateDepart', today())
                ->with(['trajet', 'bus', 'tickets', 'trajet.garres'])
                ->orderBy('heuresDepart')
                ->get();

            $voyagesParGarre[$garre->id] = [
                'garre' => $garre,
                'voyages' => $voyages
            ];
        }

        // Statistiques financières
        $revenusMois = Paiements::whereHas('ticket.voyage.trajet', function($query) use ($compagnie) {
            $query->where('idCompagnie', $compagnie->id);
        })
        ->whereMonth('created_at', now()->month)
        ->sum('montant');

        $occupationMoyenne = $this->getTauxOccupationCompagnie($compagnie);

        return view('back.dashboard', compact(
            'profil', 'buses', 'garres', 'voyagesJour', 'voyagesParGarre',
            'revenusMois', 'occupationMoyenne', 'compagnie'
        ));
    }


 private function chefGareDashboard($user)
{
    $profil = 'chef de gare';
    $garre = $user->garre;

    // Récupérer la compagnie via la gare
    $compagnie = $garre->compagnie;

    // Récupérer les voyages directement avec une requête plus simple
    $voyages = Voyages::whereHas('trajet', function($query) use ($garre, $compagnie) {
        $query->where('idCompagnie', $compagnie->id)
              ->whereHas('garres', function($q) use ($garre) {
                  $q->where('garres.id', $garre->id);
              });
    })
    ->whereDate('dateDepart', today())
    ->with([
        'trajet',
        'bus',
        'tickets',
        'trajet.garres',
        'trajet.compagnie'
    ])
    ->orderBy('heuresDepart')
    ->get();

    $personnels = User::where('idGarre', $garre->id)->count();

    // Statistiques de la gare - uniquement pour sa compagnie
    $ticketsVendus = Ticket::whereHas('voyage.trajet', function($query) use ($garre, $compagnie) {
        $query->where('idCompagnie', $compagnie->id)
              ->whereHas('garres', function($q) use ($garre) {
                  $q->where('garres.id', $garre->id);
              });
    })
    ->whereDate('created_at', today())
    ->count();

    return view('back.dashboard', compact(
        'profil', 'voyages', 'personnels', 'garre', 'ticketsVendus', 'compagnie'
    ));
}

/**
 * Récupère les voyages associés à une gare ET à la compagnie spécifique
 */
private function getVoyagesParGarreEtCompagnie($garre, $compagnie)
{
    // Récupérer les IDs des trajets associés à cette gare ET à la compagnie
    $trajetIds = $garre->trajets()
        ->where('idCompagnie', $compagnie->id)
        ->pluck('id');

    return Voyages::whereIn('idTrajet', $trajetIds)
        ->whereDate('dateDepart', today())
        ->with([
            'trajet',
            'bus',
            'tickets',
            'trajet.garres',
            'trajet.compagnie'
        ])
        ->orderBy('heuresDepart')
        ->get();
}

//     private function chefGareDashboard($user)
// {
//     $profil = 'chef de gare';
//     $garre = $user->garre;

//     // Récupérer les voyages via les trajets associés à la gare
//     $voyages = $this->getVoyagesParGarre($garre);
//     $personnels = User::where('idGarre', $garre->id)->count();

//     // Statistiques de la gare
//     $ticketsVendus = Ticket::whereHas('voyage.trajet.garres', function($query) use ($garre) {
//         $query->where('garres.id', $garre->id);
//     })
//     ->whereDate('created_at', today())
//     ->count();

//     // Calculer les départs et arrivées spécifiques à cette gare
//     $voyagesDepart = $voyages->filter(function($voyage) use ($garre) {
//         // Un départ = la gare est le point de départ du trajet
//         return $voyage->trajet->garres->contains('id', $garre->id);
//     })->count();

//     $voyagesArrivee = $voyages->filter(function($voyage) use ($garre) {
//         // Une arrivée = la gare est le point d'arrivée du trajet
//         // Vous devrez peut-être adapter cette logique selon votre modèle
//         return $voyage->trajet->garres->contains('id', $garre->id);
//     })->count();


//         return view('back.dashboard', compact(
//  'profil', 'voyages', 'personnels', 'garre',
//         'ticketsVendus', 'voyagesDepart', 'voyagesArrivee'
//         ));
//     }

    private function receptionnisteDashboard($user)
    {
        $profil = 'réceptionniste';
        $garre = $user->garre;

        // Récupérer tous les voyages disponibles pour la vente
        $voyagesDisponibles = $this->getVoyagesDisponiblesPourVente($garre);

        // Statistiques de vte
        $ticketsVendusAujourdhui = Ticket::whereHas('voyage.trajet.garres', function($query) use ($garre) {
            $query->where('garres.id', $garre->id);
        })
        ->whereDate('created_at', today())
        ->count();

        $revenusAujourdhui = Paiements::whereHas('ticket.voyage.trajet.garres', function($query) use ($garre) {
            $query->where('garres.id', $garre->id);
        })
        ->whereDate('created_at', today())
        ->sum('montant');

        return view('back.dashboard', compact(
            'profil', 'voyagesDisponibles', 'garre',
            'ticketsVendusAujourdhui', 'revenusAujourdhui'
        ));
    }

    /**
     * Récupère les voyages associés à une gare via la relation many-to-many
     */
    // private function getVoyagesParGarre($garre)
    // {
    //     // Récupérer les IDs des trajets associés à cette gare
    //     $trajetIds = $garre->trajets->pluck('id');

    //     return Voyages::whereIn('idTrajet', $trajetIds)
    //         ->whereDate('dateDepart', today())
    //         ->with(['trajet', 'bus', 'tickets', 'trajet.garres'])
    //         ->orderBy('heuresDepart')
    //         ->get();
    // }
    private function getVoyagesParGarre($garre)
{
    // Récupérer les IDs des trajets associés à cette gare
    $trajetIds = $garre->trajets->pluck('id');

    return Voyages::whereIn('idTrajet', $trajetIds)
        ->whereDate('dateDepart', today())
        ->with([
            'trajet',
            'bus',
            'tickets',
            'trajet.garres',
            'trajet.compagnie'
        ])
        ->orderBy('heuresDepart')
        ->get();
}

    /**
     * Récupère les voyages disponibles pour la vente dans une gare
     * Un voyage est disponible s'il est associé à la gare ET a des places libres
     */
    private function getVoyagesDisponiblesPourVente($garre)
    {
        $trajetIds = $garre->trajets->pluck('id');

        return Voyages::whereIn('idTrajet', $trajetIds)
            ->whereDate('dateDepart', '>=', today())
            ->with(['trajet', 'bus', 'tickets', 'trajet.garres'])
            ->orderBy('dateDepart')
            ->orderBy('heuresDepart')
            ->get()
            ->filter(function($voyage) {
                // Filtrer les voyages avec des places disponibles
                return $voyage->bus && ($voyage->bus->nombrePlaces - $voyage->tickets->count()) > 0;
            });
    }

    // Méthodes pour les graphiques et statistiques
    private function getVoyagesMensuels()
    {
        return Voyages::selectRaw('MONTH(dateDepart) as mois, COUNT(*) as total')
            ->whereYear('dateDepart', date('Y'))
            ->groupBy('mois')
            ->get()
            ->pluck('total', 'mois')
            ->toArray();
    }

    private function getRepartitionCompagnies()
    {
        return Compagnies::withCount('voyages')
            ->get()
            ->pluck('voyages_count', 'name')
            ->toArray();
    }

    private function getTopTrajets($limit = 5)
    {
        return Voyages::selectRaw('idTrajet, COUNT(*) as total')
            ->with('trajet')
            ->groupBy('idTrajet')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get();
    }

    private function getTauxOccupationCompagnie($compagnie)
    {
        $voyages = Voyages::whereIn('idTrajet', $compagnie->trajets->pluck('id'))
            ->whereDate('dateDepart', '>=', now()->subMonth())
            ->withCount('tickets')
            ->get();

        if ($voyages->isEmpty()) return 0;

        $totalPlaces = $voyages->sum(function($voyage) {
            return $voyage->bus->nombrePlaces ?? 0;
        });

        $totalTickets = $voyages->sum('tickets_count');

        return $totalPlaces > 0 ? round(($totalTickets / $totalPlaces) * 100, 2) : 0;
    }
}
