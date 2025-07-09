<?php

namespace App\Http\Controllers\Api;

//use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller;
use App\Models\Trajets;
use Illuminate\Http\Request;
//use Illuminate\Http\Request;

class DestinationController extends Controller
{
    //
    public function destinationsPubliques()
    {
        // Récupère les destinations (point d'arrivée) des compagnies publiques
        $destinations = Trajets::whereHas('compagnie', function ($query) {
                $query->where('type', 'PUBLIC');
            })
            ->select('pointArrive')
            ->distinct()
            ->orderBy('pointArrive')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $destinations
        ]);
    }
    public function destinationsPrivees()
{
    $destinations = Trajets::whereHas('compagnie', function ($query) {
            $query->where('type', 'PRIVE');
        })
        ->select('pointArrive')
        ->distinct()
        ->orderBy('pointArrive')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $destinations
    ]);
}
public function departsPubliques()
{
    $pointsDepart = Trajets::whereHas('compagnie', function ($query) {
            $query->where('type', 'PUBLIC');
        })
        ->select('pointDepart')
        ->distinct()
        ->orderBy('pointDepart')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $pointsDepart
    ]);
}
public function departsPrives()
{
    $pointsDepart = Trajets::whereHas('compagnie', function ($query) {
            $query->where('type', 'PRIVE');
        })
        ->select('pointDepart')
        ->distinct()
        ->orderBy('pointDepart')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $pointsDepart
    ]);
}



}
