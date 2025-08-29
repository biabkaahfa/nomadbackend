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


    public function confirmerPaiement(Request $request)
{
    DB::beginTransaction();

    try {
        // Valider les données de la requête
        $request->validate([
            'reservationId' => 'required|integer|exists:reservations,id',
            'referenceTransaction' => 'required|string',
            'montant' => 'required|numeric',
            'telephone' => 'required|string',
            'moyenPaiement' => 'required|string',
        ]);

        $reservationId = $request->input('reservationId');
        $reservation = Reservations::with(['voyage.trajet.compagnie', 'voyage.bus'])
                                 ->findOrFail($reservationId);

        // Vérifier si la réservation est déjà confirmée
        if ($reservation->statut === 'confirmé') {
            DB::rollBack();
            return response()->json([
                'message' => 'Cette réservation est déjà payée et confirmée.'
            ], 400);
        }

        // 1. Créer le paiement dans la base de données
        $paiement = Paiements::create([
            'montant' => $request->montant,
            'moyenPaiement' => $request->moyenPaiement,
            'statut' => 'SUCCES', // Le paiement est considéré comme réussi
            'typeSource' => 'MOBILE',
            'telephone' => $request->telephone,
            'referenceTransaction' => $request->referenceTransaction,
        ]);

        // 2. Créer les tickets pour chaque passager
        $passagers = $reservation->passagers;
        $ticketsData = [];

        // Définition des variables nécessaires pour l'envoi du mail
        $compagnie = $reservation->voyage->trajet->compagnie ?? null;
        $depart = $reservation->voyage->trajet->pointDepart;
        $arrivee = $reservation->voyage->trajet->pointArrive;

        $garres = $compagnie
            ? $compagnie->garres()
                ->where(function ($query) use ($depart, $arrivee) {
                    $query->whereRaw('LOWER(ville) = ?', [strtolower($depart)])
                            ->orWhereRaw('LOWER(ville) = ?', [strtolower($arrivee)]);
                })
                ->get(['name', 'ville', 'localisation'])
            : collect();

        foreach ($passagers as $passager) {
            $ticket = Ticket::create([
                'dateReservation' => now()->toDateString(),
                'statut' => 'CONFIRME',
                'idUtilisateur' => $reservation->idUtilisateur,
                'modeReception' => $passager['modeReception'] ?? 'email',
                'typeAchat' => 'en_ligne',
                'name' => $passager['name'],
                'telephone' => $passager['telephone'],
                'email' => $passager['email'],
                'idVoyage' => $reservation->idVoyage,
                'dateScan' => null,
                'idPaiement' => $paiement->id,
                'namePersonneAPrevenir' => $passager['namePersonneAPrevenir'] ?? null,
                'numeroPersonneAPrevenir' => $passager['numeroPersonneAPrevenir'] ?? null,
                'emailPersonneAPrevenir' => $passager['emailPersonneAPrevenir'] ?? null,
            ]);

            // Préparer les données pour le QR Code
            $qrData = [
                'ticket_id' => $ticket->id,
                'client' => $ticket->name,
                'email' => $ticket->email,
                'montant' => $paiement->montant,
                'reference' => $paiement->referenceTransaction,
            ];
             $logoPath = public_path('back_auth/assets/img/Movyx.png');

                // Générer le QR Code avec le logo fusionné
                // La méthode 'merge' prend le chemin de l'image, et en option, le ratio de taille (0.2 = 20%) et si la transparence est activée.
                $qrCode = QrCode::format('png')
                                ->size(200)
                                ->merge($logoPath, 0.2, true)
                                ->generate(json_encode($qrData));
                // --- FIN DE LA MODIFICATION ---

            // Gérer l'envoi des tickets
            if ($ticket->modeReception === 'email' && $ticket->email) {
                // Nous passons explicitement toutes les variables nécessaires à la vue
                $pdf = PDF::loadView('back.pdf.ticket', [
                    'ticket'    => $ticket,
                    'qrCode'    => $qrCode,
                    'voyage'    => $reservation->voyage,
                    'compagnie' => $compagnie,
                    'client'    => $ticket->name,
                    'date'      => $ticket->dateReservation,
                    'ticket_id' => $ticket->id,
                    'depart'    => $depart,
                    'arrivee'   => $arrivee,
                    'garres'    => $garres,
                ]);

                try {
                    // CORRECTION: Envoi de l'email avec les 5 arguments corrects
                    Mail::to($ticket->email)->send(new TicketMail(
                        $ticket->name,
                        $compagnie,
                        $ticket->dateReservation,
                        $pdf->output(),
                        $garres
                    ));
                } catch (\Exception $e) {
                    Log::error('Erreur envoi email : ' . $e->getMessage());
                }
            } else {
                // Si modeReception est 'application', on ajoute les données pour le retour JSON
                $ticketsData[] = [
                    'id' => $ticket->id,
                    'client' => $ticket->name,
                    'voyage' => [
                        'date' => $reservation->voyage->dateDepart,
                        'depart' => $depart,
                        'arrivee' => $arrivee,
                        'heure' => $reservation->voyage->heuresDepart,
                    ],
                    'compagnie' => $compagnie->name ?? 'N/A',
                    'montant' => $paiement->montant,
                    'modeReception' => 'application',
                    'qrCode' => 'data:image/png;base64,' . base64_encode($qrCode),
                ];
            }
        }

        // 3. Mettre à jour la réservation
        $reservation->update([
            'statut' => 'payée',
            'idPaiement' => $paiement->id,
        ]);

        // 4. Décrémenter les places
        if ($reservation->voyage->idBus) {
            $reservation->voyage->bus->decrement('nombrePlaceDispo', $reservation->nombrePlaces);
        }

        DB::commit();

        return response()->json([
            'message' => 'Paiement confirmé et tickets générés avec succès.',
            'tickets' => $ticketsData, // Retourne les tickets à afficher dans l'application si nécessaire
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur confirmation de paiement : ' . $e->getMessage());
        return response()->json([
            'message' => 'Erreur serveur lors de la confirmation du paiement.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

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

