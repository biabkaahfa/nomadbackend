<?php

namespace App\Http\Controllers\Api;
    use App\Models\Trajets;
use App\Models\Voyages;
use App\Models\FrequenceTrajets;
use App\Models\Ticket;
use App\Models\Bus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;

class CompagnieController extends Controller
{
//     //
// public function compagniesDisponibles(Request $request)
// {
//     return response()->json(['message' => 'Méthode atteinte']);
// }



public function compagniesDisponibles(Request $request)
{
    $request->validate([
        'depart' => 'required|string',
        'arrive' => 'required|string',
        'date' => 'required|date',
        'places' => 'required|integer|min:1',
    ]);

    $depart = $request->depart;
    $arrive = $request->arrive;
    $date = $request->date;
    $placesDemandées = $request->places;

    $trajets = Trajets::with(['compagnie', 'voyages'])
        ->where('pointDepart', $depart)
        ->where('pointArrive', $arrive)
        ->get();

    $resultat = [];

    foreach ($trajets as $trajet) {
        $voyages = $trajet->voyages()->where('dateDepart', $date)->get();

        $horairesDisponibles = [];

        foreach ($voyages as $voyage) {
            $placesDisponibles = 0;

            if ($voyage->idBus) {
                $bus = Bus::find($voyage->idBus);
                if ($bus && $bus->nombrePlaceDispo >= $placesDemandées) {
                    $horairesDisponibles[] = [
                        'idVoyage' => $voyage->id,
                        'heure' => $voyage->heuresDepart
                    ];
                }
            } else {
                $frequence = FrequenceTrajets::where('idTrajet', $trajet->id)->first();
                $ticketsVendus = Ticket::where('idVoyage', $voyage->id)->count();
                $placesRestantes = $frequence ? ($frequence->nombrePlaceMinimum - $ticketsVendus) : 0;

                if ($placesRestantes >= $placesDemandées) {
                    $horairesDisponibles[] = [
                        'idVoyage' => $voyage->id,
                        'heure' => $voyage->heuresDepart
                    ];
                }
            }
        }

        if (count($horairesDisponibles) > 0) {
            $resultat[] = [
                'compagnie' => [
                    'id' => $trajet->compagnie->id,
                    'name' => $trajet->compagnie->name,
                    'logo' => $trajet->compagnie->logo,
                    'telephone' => $trajet->compagnie->telephone,
                    'email' => $trajet->compagnie->email,
                ],
                'trajet' => [
                    'pointDepart' => $trajet->pointDepart,
                    'pointArrive' => $trajet->pointArrive,
                    'prix' => $trajet->prix,
                ],
                'date' => $date,
                'horaires' => $horairesDisponibles
            ];
        }
    }

    return response()->json([
        'status' => 'success',
        'data' => $resultat
    ]);
}


}
