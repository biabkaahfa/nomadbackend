<?php

namespace App\Http\Controllers;

use App\Models\Reservations;
use App\Models\Paiements;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class PaiementCallbackController extends Controller
{
    public function confirm(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'statutPaiement' => 'required|in:SUCCES,ECHEC',
            'montant' => 'required|numeric',
            'reference' => 'required|string|unique:paiements,referenceTransaction',
            'moyenPaiement' => 'required|string',
            'telephone' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $reservation = Reservations::with('voyage.trajet', 'utilisateur')->findOrFail($request->reservation_id);

            if ($reservation->statut !== 'en_attente_paiement') {
                return response()->json(['message' => 'Réservation déjà traitée.'], 400);
            }

            if ($request->statutPaiement === 'ECHEC') {
                $reservation->update(['statut' => 'annulée']);
                return response()->json(['message' => 'Paiement échoué. Réservation annulée.'], 200);
            }

            // Création du paiement
            $paiement = Paiements::create([
                'montant' => $request->montant,
                'moyenPaiement' => $request->moyenPaiement,
                'referenceTransaction' => $request->reference,
                'statut' => 'VALIDE',
                'telephone' => $request->telephone,
                'typeSource' => 'MOBILE',
            ]);

            $reservation->update([
                'statut' => 'payée',
                'idPaiement' => $paiement->id,
            ]);

            // Création des tickets
            foreach ($reservation->passagers as $passager) {
                $ticket = Ticket::create([
                    'dateReservation' => now(),
                    'statut' => 'CONFIRME',
                    'idVoyage' => $reservation->idVoyage,
                    'idUtilisateur' => $reservation->idUtilisateur,
                    'name' => $passager['name'] ?? null,
                    'telephone' => $passager['telephone'] ?? null,
                    'email' => $passager['email'] ?? null,
                    'namePersonneAPrevenir' => $passager['namePersonneAPrevenir'] ?? null,
                    'numeroPersonneAPrevenir' => $passager['numeroPersonneAPrevenir'] ?? null,
                    'emailPersonneAPrevenir' => $passager['emailPersonneAPrevenir'] ?? null,
                    'typeAchat' => 'En_ligne',
                    'modeReception' => 'email',
                    'idPaiement' => $paiement->id,
                ]);

                // 📦 Génération et envoi PDF
                if ($ticket->modeReception === 'email' && $ticket->email) {
                    $compagnie = $ticket->voyage->trajet->compagnie ?? null;

                    $qrCode = QrCode::format('png')->size(200)->generate(json_encode([
                        'ticket_id' => $ticket->id,
                        'client' => $ticket->name,
                        'email' => $ticket->email,
                        'montant' => $paiement->montant,
                        'reference' => $paiement->referenceTransaction,
                    ]));

                    $pdf = Pdf::loadView('back.pdf.ticket', [
                        'ticket' => $ticket,
                        'client' => $ticket->name,
                        'date' => $ticket->voyage->dateDepart,
                        'depart' => $ticket->voyage->trajet->pointDepart,
                        'arrivee' => $ticket->voyage->trajet->pointArrive,
                        'compagnie' => $compagnie,
                        'qrCode' => $qrCode,
                        'garres' => $compagnie?->garres ?? [],
                    ]);

                    Mail::to($ticket->email)->send(new TicketMail(
                        $ticket->name,
                        $compagnie,
                        $ticket->voyage->dateDepart,
                        $pdf->output(),
                        $compagnie?->garres ?? []
                    ));
                }
            }

            DB::commit();

            return response()->json(['message' => 'Paiement confirmé. Tickets envoyés.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur callback paiement : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }
}

