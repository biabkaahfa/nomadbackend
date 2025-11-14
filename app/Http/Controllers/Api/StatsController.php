<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;;
use App\Models\Ticket;
class StatsController extends Controller
{

    public function statsUtilisateur(Request $request)
    {
        $user = Auth::user();

        $tickets = Ticket::with(['voyage.trajet'])
            ->where('idUtilisateur', $user->id)
            ->whereNotNull('idGarre') // scanné
            ->get();

        $distanceTotale = 0;
        $montantTotal = 0;

        foreach ($tickets as $ticket) {
            $trajet = $ticket->voyage->trajet ?? null;
            if ($trajet) {
                $distanceTotale += $trajet->distance ?? 0; // Assure-toi que ce champ existe
                $montantTotal += $trajet->prix ?? 0;       // Ou ticket->prix si tu stockes directement
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'distanceTotale' => $distanceTotale . ' km',
                'montantTotalDepense' => $montantTotal . ' F CFA',
            ]
        ]);
    }
}

