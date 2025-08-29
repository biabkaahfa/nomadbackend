<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
  use Illuminate\Support\Facades\Auth; // Importez la façade Auth
    use App\Models\Ticket;

class TicketController extends Controller
{
    //


     public function recuperationTickets()
    {
        // Récupère l'utilisateur actuellement authentifié
        $user = Auth::user();

        // Vérifie si un utilisateur est authentifié
        if (!$user) {
            return response()->json([
                'message' => 'Non authentifié. Veuillez vous connecter.'
            ], 401);
        }

        // Récupère les tickets de cet utilisateur en chargeant les relations
        // 'voyage', 'voyage.trajet', 'voyage.trajet.compagnie' et 'paiement'
        $tickets = Ticket::with(['voyage.trajet.compagnie', 'voyage.trajet', 'paiement'])
                         ->where('idUtilisateur', $user->id)
                         ->get();

        // Transforme les tickets pour inclure les informations supplémentaires et le QR code
        $ticketsWithDetails = $tickets->map(function ($ticket) {
            $voyage = $ticket->voyage;
            $trajet = $voyage->trajet;
            $compagnie = $trajet->compagnie;
            $paiement = $ticket->paiement;

            // Définir le chemin du logo pour le QR code
            $logoPath = public_path('back_auth/assets/img/Movyx.png');
            // if ($compagnie && $compagnie->logo) {
            //     $companyLogoPath = public_path('storage/' . $compagnie->logo);
            //     if (file_exists($companyLogoPath)) {
            //         $logoPath = $companyLogoPath;
            //     }
            // }

            // Préparer les données pour le QR Code
            $qrData = [
                'ticket_id' => $ticket->id,
                'client' => $ticket->name,
                'email' => $ticket->email,
                'montant' => $paiement->montant,
                'moyenPaiement' => $paiement->moyenPaiement,
                'reference' => $paiement->referenceTransaction,
                'compagnie' => $compagnie->name ?? 'N/A',
                'dateDepart' => $voyage->dateDepart ?? 'N/A',
                'pointDepart' => $trajet->pointDepart ?? 'N/A',
                'pointArrive' => $trajet->pointArrive ?? 'N/A',
            ];

            // Générer le QR Code avec le logo fusionné et le convertir en Base64
            $qrCode = QrCode::format('png')
                            ->size(200)
                            ->merge($logoPath, 0.2, true)
                            ->generate(json_encode($qrData));

            $qrCodeBase64 = base64_encode($qrCode);

            // Retourner les données du ticket enrichies
            return [
                'id' => $ticket->id,
                'dateReservation' => $ticket->dateReservation,
                'statut' => $ticket->statut,
                'name' => $ticket->name,
                'telephone' => $ticket->telephone,
                'email' => $ticket->email,
                'modeReception' => $ticket->modeReception,
                'typeAchat' => $ticket->typeAchat,
                'dateScan' => $ticket->dateScan,
                'namePersonneAPrevenir' => $ticket->namePersonneAPrevenir,
                'numeroPersonneAPrevenir' => $ticket->numeroPersonneAPrevenir,
                'emailPersonneAPrevenir' => $ticket->emailPersonneAPrevenir,
                'created_at' => $ticket->created_at,
                'updated_at' => $ticket->updated_at,
                // Nouvelles données
                'compagnie' => [
                    'name' => $compagnie->name ?? 'N/A',
                ],
                'voyage' => [
                    'dateDepart' => $voyage->dateDepart ?? 'N/A',
                    'trajet' => [
                        'pointDepart' => $trajet->pointDepart ?? 'N/A',
                        'pointArrive' => $trajet->pointArrive ?? 'N/A',
                    ]
                ],
                'qrCodeBase64' => $qrCodeBase64,
            ];
        });

        // Renvoie les tickets modifiés sous forme de réponse JSON
        return response()->json([
            'tickets' => $ticketsWithDetails
        ], 200);
    }
}
