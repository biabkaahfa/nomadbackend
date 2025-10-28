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

        // ✅ CORRECTION : Charger les relations correctement
        $reservation = Reservations::with([
            'voyage.trajet.compagnie',
            'voyage.bus',
            'voyageRetour.trajet.compagnie',
            'voyageRetour.bus'
        ])->findOrFail($reservationId);

        // Vérifier si la réservation est déjà confirmée
        if ($reservation->statut === 'payée') {
            return response()->json([
                'message' => 'Cette réservation est déjà payée et confirmée.'
            ], 400);
        }

        // 1. Créer le paiement dans la base de données
        $paiement = Paiements::create([
            'montant' => $request->montant,
            'moyenPaiement' => $request->moyenPaiement,
            'statut' => 'SUCCES',
            'typeSource' => 'MOBILE',
            'telephone' => $request->telephone,
            'referenceTransaction' => $request->referenceTransaction,
        ]);

        // 2. ✅ CORRECTION : Récupérer les passagers depuis la réservation
        // Avec le cast 'array', Laravel décode automatiquement le JSON en tableau
        $passagers = $reservation->passagers;

        Log::info('Passagers récupérés: ' . json_encode($passagers));
        Log::info('Type des passagers: ' . gettype($passagers));

        // Vérifier que passagers est bien un tableau
        if (!is_array($passagers)) {
            Log::error('Passagers n\'est pas un tableau pour la réservation: ' . $reservationId);
            $passagers = [];
        }

        Log::info('Nombre de passagers: ' . count($passagers));

        $ticketsData = [];

        foreach ($passagers as $index => $passager) {
            Log::info("Traitement passager $index: " . json_encode($passager));

            // ✅ Vérifier que chaque passager est un tableau
            if (!is_array($passager)) {
                Log::warning("Passager $index invalide - Type: " . gettype($passager));
                continue;
            }

            // ✅ Vérifier les données du passager
            if (empty($passager['name'])) {
                Log::warning("Passager $index sans nom");
                continue;
            }

            Log::info("Création ticket pour: " . $passager['name']);

            // ✅ TICKET ALLER
            $ticketAller = Ticket::create([
                'dateReservation' => now()->toDateString(),
                'statut' => 'CONFIRME',
                'idUtilisateur' => $reservation->idUtilisateur,
                'modeReception' => $passager['modeReception'] ?? 'email',
                'typeAchat' => 'en_ligne',
                'name' => $passager['name'] ?? '',
                'telephone' => $passager['telephone'] ?? '',
                'email' => $passager['email'] ?? '',
                'idVoyage' => $reservation->idVoyage,
                'dateScan' => null,
                'idPaiement' => $paiement->id,
                'namePersonneAPrevenir' => $passager['namePersonneAPrevenir'] ?? null,
                'numeroPersonneAPrevenir' => $passager['numeroPersonneAPrevenir'] ?? null,
                'emailPersonneAPrevenir' => $passager['emailPersonneAPrevenir'] ?? null,
            ]);

            Log::info("Ticket aller créé - ID: " . $ticketAller->id);

            // Générer et envoyer le ticket aller
            $this->genererEtEnvoyerTicket($ticketAller, $reservation->voyage, $paiement);

            // Ajouter aux données de retour
            $ticketsData[] = [
                'type' => 'aller',
                'id' => $ticketAller->id,
                'client' => $ticketAller->name,
                'voyage' => [
                    'date' => $reservation->voyage->dateDepart,
                    'depart' => $reservation->voyage->trajet->pointDepart,
                    'arrivee' => $reservation->voyage->trajet->pointArrive,
                    'heure' => $reservation->voyage->heuresDepart,
                ],
                'compagnie' => $reservation->voyage->trajet->compagnie->name ?? 'N/A',
            ];

            // ✅ TICKET RETOUR (si aller-retour)
            if ($reservation->idVoyageRetour && $reservation->voyageRetour) {
                $ticketRetour = Ticket::create([
                    'dateReservation' => now()->toDateString(),
                    'statut' => 'CONFIRME',
                    'idUtilisateur' => $reservation->idUtilisateur,
                    'modeReception' => $passager['modeReception'] ?? 'email',
                    'typeAchat' => 'en_ligne',
                    'name' => $passager['name'] ?? '',
                    'telephone' => $passager['telephone'] ?? '',
                    'email' => $passager['email'] ?? '',
                    'idVoyage' => $reservation->idVoyageRetour,
                    'dateScan' => null,
                    'idPaiement' => $paiement->id,
                    'namePersonneAPrevenir' => $passager['namePersonneAPrevenir'] ?? null,
                    'numeroPersonneAPrevenir' => $passager['numeroPersonneAPrevenir'] ?? null,
                    'emailPersonneAPrevenir' => $passager['emailPersonneAPrevenir'] ?? null,
                ]);

                Log::info("Ticket retour créé - ID: " . $ticketRetour->id);

                // Générer et envoyer le ticket retour
                $this->genererEtEnvoyerTicket($ticketRetour, $reservation->voyageRetour, $paiement);

                $ticketsData[] = [
                    'type' => 'retour',
                    'id' => $ticketRetour->id,
                    'client' => $ticketRetour->name,
                    'voyage' => [
                        'date' => $reservation->voyageRetour->dateDepart,
                        'depart' => $reservation->voyageRetour->trajet->pointDepart,
                        'arrivee' => $reservation->voyageRetour->trajet->pointArrive,
                        'heure' => $reservation->voyageRetour->heuresDepart,
                    ],
                    'compagnie' => $reservation->voyageRetour->trajet->compagnie->name ?? 'N/A',
                ];
            }
        }

        // 3. Mettre à jour la réservation
        // ✅ CORRECTION : Utiliser update() avec un tableau associatif
        $reservation->update([
            'statut' => 'payée',
            'idPaiement' => $paiement->id,
        ]);

        // 4. Décrémenter les places pour l'aller
        if ($reservation->voyage->idBus) {
            $reservation->voyage->bus->decrement('nombrePlaceDispo', $reservation->nombrePlaces);
        }

        // 5. Décrémenter les places pour le retour (si aller-retour)
        if ($reservation->idVoyageRetour && $reservation->voyageRetour && $reservation->voyageRetour->idBus) {
            $reservation->voyageRetour->bus->decrement('nombrePlaceDispo', $reservation->nombrePlaces);
        }

        DB::commit();

        Log::info('Nombre final de tickets créés: ' . count($ticketsData));

        return response()->json([
            'message' => 'Paiement confirmé et tickets générés avec succès.',
            'tickets' => $ticketsData,
            'type_voyage' => $reservation->idVoyageRetour ? 'aller_retour' : 'aller_simple',
            'nombre_tickets' => count($ticketsData),
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur confirmation de paiement : ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        Log::error('File: ' . $e->getFile());
        Log::error('Line: ' . $e->getLine());
        return response()->json([
            'message' => 'Erreur serveur lors de la confirmation du paiement.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

// ✅ Méthode helper pour générer et envoyer les tickets
private function genererEtEnvoyerTicket($ticket, $voyage, $paiement)
{
    try {
        // Vérifier si l'email doit être envoyé
        if ($ticket->modeReception === 'email' && $ticket->email) {

            $compagnie = $voyage->trajet->compagnie ?? null;

            // Générer le QR Code
            $qrData = [
                'ticket_id' => $ticket->id,
                'client' => $ticket->name,
                'email' => $ticket->email,
                'montant' => $paiement->montant,
                'reference' => $paiement->referenceTransaction,
            ];

            $logoPath = public_path('back_auth/assets/img/Movyx.png');
            $qrCode = QrCode::format('png')
                            ->size(200)
                            ->merge($logoPath, 0.2, true)
                            ->generate(json_encode($qrData));

            // Récupérer les gares
            $depart = $voyage->trajet->pointDepart;
            $arrivee = $voyage->trajet->pointArrive;

            $garres = $compagnie
                ? $compagnie->garres()
                    ->where(function ($query) use ($depart, $arrivee) {
                        $query->whereRaw('LOWER(ville) = ?', [strtolower($depart)])
                              ->orWhereRaw('LOWER(ville) = ?', [strtolower($arrivee)]);
                    })
                    ->get(['name', 'ville', 'localisation'])
                : collect();

            // Générer le PDF
            $pdf = PDF::loadView('back.pdf.ticket', [
                'ticket'    => $ticket,
                'qrCode'    => $qrCode,
                'voyage'    => $voyage,
                'compagnie' => $compagnie,
                'client'    => $ticket->name,
                'date'      => $ticket->voyage->dateDepart,
                'ticket_id' => $ticket->id,
                'depart'    => $depart,
                'arrivee'   => $arrivee,
                'garres'    => $garres,
            ]);

            // Envoyer l'email
            Mail::to($ticket->email)->send(new TicketMail(
                $ticket->name,
                $compagnie,
                $ticket->voyage->dateDepart,
                $pdf->output(),
                $garres
            ));
        }
    } catch (\Exception $e) {
        Log::error('Erreur génération/envoi ticket ID ' . $ticket->id . ': ' . $e->getMessage());
        // Ne pas bloquer le processus principal en cas d'erreur d'envoi d'email
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

