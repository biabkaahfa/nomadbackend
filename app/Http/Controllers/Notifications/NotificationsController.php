<?php

namespace App\Http\Controllers\Notifications;

use Log;
use App\Models\Bus;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Tickets;
use App\Models\Trajets;
use App\Models\Voyages;
use App\Models\GarreTrajets;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Carbon;
use App\Models\FrequenceTrajets;
use App\Services\FirebaseService;
use App\Services\AqilasSmsService;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseServiceV1;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreNotificationsRequest;
use App\Http\Requests\UpdateNotificationsRequest;

class NotificationsController extends Controller
{
    protected $firebaseService;
    protected $smsService;

    public function __construct()
    {
        $this->firebaseService = new FirebaseService();
        $this->smsService = new AqilasSmsService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $query = Notifications::with(['voyage.tickets.user', 'voyage.trajet'])
            ->orderBy('DateEnvoie', 'desc');

        // Si ce n'est pas un admin général, on filtre les notifications
        if ($user->profil?->name !== 'Admin général') {
            $query->whereHas('voyage.trajet', function ($q) use ($user) {
                $q->where('idCompagnie', $user->idCompagnie);
            });
        }

        // Utiliser paginate() au lieu de get()
        $notifications = $query->paginate(10);

        return view('back.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $query = Voyages::with(['trajet.garresDepart', 'trajet.garresArrivee', 'trajet.frequences', 'bus', 'tickets'])
            ->orderBy('dateDepart', 'desc');

        if ($user->profil?->name === 'Admin compagnie') {
            $query->whereHas('trajet', function ($q) use ($user) {
                $q->where('idCompagnie', $user->idCompagnie);
            });
        } elseif (in_array($user->profil?->name, ['Chef de gare', 'Réceptionniste'])) {
            $trajetIds = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');
            $query->whereIn('idTrajet', $trajetIds);
        }

        $voyages = $query->get();

        return view('back.notifications.create', compact('voyages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationsRequest $request)
    {
        try {
            $data = $request->validated();
            $data['DateEnvoie'] = now();
            $data['idUtilisateur'] = Auth::id();

            $modesEnvoi = $data['modes_envoi'];

            $voyage = Voyages::with(['tickets.user', 'trajet'])->findOrFail($data['idVoyage']);

            // Préparer les données communes
            $pushData = $this->preparePushNotificationData($data, $voyage);
            $contenuFinal = $this->prepareMessageContent($data, $voyage);

            $results = [
                'email' => 0,
                'sms' => 0,
                'push' => 0
            ];

            $totalSmsCost = 0;
            $smsSentCount = 0;

            foreach ($voyage->tickets as $ticket) {
                // ENVOI EMAIL si coché
                if (in_array('email', $modesEnvoi)) {
                    $email = $data['type'] === 'accident'
                        ? $ticket->emailPersonneAPrevenir
                        : ($ticket->email ?? $ticket->user?->email);

                    if ($email) {
                        try {
                            Mail::raw($contenuFinal, function ($message) use ($email, $data) {
                                $message->to($email)->subject($data['titre']);
                            });
                            $results['email']++;
                        } catch (\Exception $e) {
                            Log::error("Erreur envoi email", [
                                'ticket_id' => $ticket->id,
                                'email' => $email,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }

                // ENVOI SMS si coché
                if (in_array('sms', $modesEnvoi)) {
                    $smsResult = $this->sendSmsToTicket($ticket, $data['contenu'], $data['type']);
                    if ($smsResult['success']) {
                        $results['sms']++;
                        $totalSmsCost += $smsResult['cost'] ?? 0;
                        $smsSentCount++;
                    }
                }

                // ENVOI NOTIFICATION PUSH si coché
                if (in_array('push', $modesEnvoi)) {
                    if ($ticket->user && $ticket->user->hasFcmTokens()) {
                        try {
                            $this->sendPushNotificationToUser($ticket->user, $pushData);
                            $results['push']++;
                        } catch (\Exception $e) {
                            Log::error("Erreur envoi push", [
                                'ticket_id' => $ticket->id,
                                'user_id' => $ticket->user->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

            // Log du coût total des SMS
            if ($smsSentCount > 0) {
                Log::info("Coût total SMS pour cette notification", [
                    'notification_id' => $data['idVoyage'],
                    'sms_count' => $smsSentCount,
                    'total_cost' => $totalSmsCost . ' XOF',
                    'average_cost_per_sms' => $smsSentCount > 0 ? ($totalSmsCost / $smsSentCount) . ' XOF' : '0 XOF'
                ]);
            }

            // Créer la notification avec statistiques
            $notification = Notifications::createNotification($data, $modesEnvoi, $results);

            $message = $this->getCombinedSuccessMessage($modesEnvoi, $results);

            return redirect()->route('notifications.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de la notification", [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi de la notification: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Récupérer les statistiques d'un voyage pour l'affichage des compteurs
     */
    public function getVoyageStats($voyageId)
    {
        try {
            $voyage = Voyages::with(['tickets.user'])->findOrFail($voyageId);

            $stats = [
                'emails_count' => $voyage->tickets->filter(function($ticket) {
                    return $ticket->email || $ticket->user?->email || $ticket->emailPersonneAPrevenir;
                })->count(),

                'sms_count' => $voyage->tickets->filter(function($ticket) {
                    return $ticket->telephone || $ticket->user?->phone_number || $ticket->telephonePersonneAPrevenir;
                })->count(),

                'push_count' => $voyage->tickets->filter(function($ticket) {
                    return $ticket->user && $ticket->user->fcm_tokens && count($ticket->user->fcm_tokens) > 0;
                })->count(),

                'total_tickets' => $voyage->tickets->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur récupération stats voyage", [
                'voyage_id' => $voyageId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques'
            ], 500);
        }
    }

    /**
     * Préparer les données pour la notification push
     */
    private function preparePushNotificationData(array $data, Voyages $voyage): array
    {
        $trajet = $voyage->trajet;

        $dateDepart = $voyage->dateDepart;
        if (is_string($dateDepart)) {
            $dateDepart = Carbon::parse($dateDepart);
        }

        if ($dateDepart instanceof Carbon) {
            $dateDepartFormatted = $dateDepart->format('Y-m-d');
        } else {
            $dateDepartFormatted = $dateDepart ?? 'Date inconnue';
        }

        return [
            'title' => $data['titre'],
            'body' => $data['contenu'],
            'data' => [
                'type' => 'notification_voyage',
                'notification_type' => $data['type'],
                'voyage_id' => (string) $voyage->id,
                'trajet' => $trajet->pointDepart . ' → ' . $trajet->pointArrive,
                'date_depart' => $dateDepartFormatted,
                'heure_depart' => $voyage->heuresDepart,
                'screen' => 'voyage_details',
                'action' => 'voir_details',
                'urgence' => in_array($data['type'], ['accident', 'retard_important', 'annulation']),
            ]
        ];
    }

    /**
     * Préparer le contenu du message (unifié pour email et SMS)
     */
    private function prepareMessageContent(array $data, Voyages $voyage): string
    {
        $trajet = $voyage->trajet;
        $busNumero = $voyage->bus?->numero ?? 'Aucun';
        $compagnie = $trajet->compagnie?->name ?? 'Compagnie inconnue';

        $dateDepart = $voyage->dateDepart;
        if (is_string($dateDepart)) {
            $dateDepart = Carbon::parse($dateDepart);
        }
        $dateDepartFormatted = $dateDepart instanceof Carbon ? $dateDepart->format('d/m/Y') : ($dateDepart ?? 'Date inconnue');

        return <<<EOT
{$data['contenu']}

🚌 Compagnie : {$compagnie}
📍 Trajet : {$trajet->pointDepart} → {$trajet->pointArrive}
🗓️ Date de départ : {$dateDepartFormatted}
🕒 Heure de départ : {$voyage->heuresDepart}
🚌 Bus n° : {$busNumero}
EOT;
    }

    /**
     * Préparer le contenu optimisé pour SMS (version courte et économique)
     */
    private function prepareSmsContent(array $data, Voyages $voyage): string
    {
        $trajet = $voyage->trajet;
        $compagnie = $trajet->compagnie?->name ?? 'Compagnie';

        // Abréger le nom de la compagnie
        $compagnieAbregee = $this->abregerCompagnie($compagnie);

        // Utiliser le template par type si disponible
        $templateMessage = $this->getSmsTemplate($data['type'], $voyage);
        if ($templateMessage && strlen($templateMessage) <= 160) {
            return $templateMessage;
        }

        // Message principal court
        $mainMessage = $data['contenu'];
        if (strlen($mainMessage) > 70) {
            $mainMessage = substr($mainMessage, 0, 67) . '...';
        }

        // Informations essentielles
        $infos = [
            $compagnieAbregee,
            $this->abregerVille($trajet->pointDepart) . '>' . $this->abregerVille($trajet->pointArrive),
            \Carbon\Carbon::parse($voyage->dateDepart)->format('d/m'),
            substr($voyage->heuresDepart, 0, 5)
        ];

        $essentialInfo = ' | ' . implode(' ', $infos);

        // Calculer la longueur totale
        $totalLength = strlen($mainMessage) + strlen($essentialInfo);

        // Ajuster si nécessaire
        if ($totalLength > 160) {
            $mainMessage = substr($mainMessage, 0, 50) . '...';
            $infos = [
                $compagnieAbregee,
                $this->abregerVille($trajet->pointDepart) . '>' . $this->abregerVille($trajet->pointArrive),
                \Carbon\Carbon::parse($voyage->dateDepart)->format('d/m')
            ];
            $essentialInfo = ' | ' . implode(' ', $infos);
        }

        $finalMessage = $mainMessage . $essentialInfo;

        // Nettoyer le message
        $finalMessage = $this->removeEmojis($finalMessage);
        $finalMessage = preg_replace('/\s+/', ' ', $finalMessage);

        // Garantir ≤ 160 caractères
        if (strlen($finalMessage) > 160) {
            $finalMessage = substr($finalMessage, 0, 157) . '...';
        }

        Log::info('Message SMS optimisé', [
            'length' => strlen($finalMessage),
            'message' => $finalMessage
        ]);

        return $finalMessage;
    }

    /**
     * Abréger les noms de compagnies
     */
    private function abregerCompagnie(string $compagnie): string
    {
        $abreviations = [
            'Transport Faso' => 'TransFaso',
            'Transport du Burkina' => 'TransBur',
            'Société de Transport' => 'ST',
            'Compagnie de Transport' => 'CT',
            'Rakieta' => 'Rakieta',
            'STMB' => 'STMB',
            'TSR' => 'TSR',
            'Sotrao' => 'Sotrao',
            'Bani Transport' => 'BaniTrans',
            'Ben Transport' => 'BenTrans',
            'Somaf' => 'Somaf',
            'Rim Transport' => 'RimTrans',
            'Saga Transport' => 'SagaTrans',
            'Sinalbi' => 'Sinalbi',
            'Sogeb' => 'Sogeb',
            'Somagef' => 'Somagef',
        ];

        return $abreviations[$compagnie] ?? substr($compagnie, 0, 8);
    }

    /**
     * Abréger les noms de villes
     */
    private function abregerVille(string $ville): string
    {
        $abreviations = [
            'Ouagadougou' => 'Ouaga',
            'Bobo-Dioulasso' => 'Bobo',
            'Koudougou' => 'Koud',
            'Banfora' => 'Banf',
            'Kaya' => 'Kaya',
            'Fada' => 'Fada',
            'Dori' => 'Dori',
            'Dédougou' => 'Déd',
            'Gaoua' => 'Gao',
            'Tenkodogo' => 'Tenk',
            'Manga' => 'Mang',
            'Ziniaré' => 'Zini',
            'Kombissiri' => 'Komb',
            'Houndé' => 'Houn',
            'Tougan' => 'Toug',
            'Yako' => 'Yako',
            'Pô' => 'Pô',
            'Zorgho' => 'Zorg',
            'Koupéla' => 'Koup',
            'Garango' => 'Gara',
        ];

        return $abreviations[$ville] ?? substr($ville, 0, 4);
    }

    /**
     * Supprimer les émojis et caractères spéciaux
     */
    private function removeEmojis(string $text): string
    {
        $emoji_pattern = '/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]/u';
        $cleanText = preg_replace($emoji_pattern, '', $text);

        $replacements = [
            '🚌' => '', '📍' => '', '🗓️' => '', '🕒' => '',
            '→' => '>', '📧' => '', '📱' => '', '🔔' => '', '👤' => '',
        ];

        $cleanText = str_replace(array_keys($replacements), array_values($replacements), $cleanText);
        return trim(preg_replace('/\s+/', ' ', $cleanText));
    }

    /**
     * Déterminer le coût estimé du SMS
     */
    private function estimateSmsCost(string $message, string $phoneNumber): array
    {
        $messageLength = strlen($message);
        $isInternational = !str_starts_with($phoneNumber, '+226');
        $segments = ceil($messageLength / 160);

        $costPerSegment = $isInternational ? 30 : 11;
        $totalCost = $segments * $costPerSegment;

        return [
            'segments' => $segments,
            'cost_per_segment' => $costPerSegment,
            'total_cost' => $totalCost,
            'is_international' => $isInternational,
            'message_length' => $messageLength
        ];
    }

    /**
     * Template de messages courts par type de notification
     */
    private function getSmsTemplate(string $type, Voyages $voyage): string
    {
        $trajet = $voyage->trajet;
        $compagnie = $this->abregerCompagnie($trajet->compagnie?->name ?? 'Compagnie');
        $date = \Carbon\Carbon::parse($voyage->dateDepart)->format('d/m');
        $heure = substr($voyage->heuresDepart, 0, 5);
        $route = $this->abregerVille($trajet->pointDepart) . '>' . $this->abregerVille($trajet->pointArrive);

        $templates = [
            'annulation' => "Annulation {$compagnie} {$route} {$date} {$heure}. Remboursement auto.",
            'retard' => "Retard {$compagnie} {$route} {$date} {$heure}. Desole pour le desagrement.",
            'report' => "Voyage {$compagnie} {$route} reporte. Nouvelle date vous sera communiquee.",
            'accident' => "Alerte: Incident {$compagnie} {$route}. Retard important prevu.",
            'rappel' => "Rappel: Depart {$compagnie} {$route} {$date} a {$heure}. Presentez-vous 30min avant.",
        ];

        $message = $templates[$type] ?? "Notification {$compagnie} {$route} {$date} {$heure}";

        // Vérifier que le template ne dépasse pas 160 caractères
        return strlen($message) <= 160 ? $message : '';
    }

    /**
     * Envoyer un SMS pour un ticket
     */
    private function sendSmsToTicket(Ticket $ticket, string $message, string $type): array
    {
        try {
            $phoneNumber = $this->getPhoneNumberForSms($ticket, $type);

            if (!$phoneNumber) {
                Log::warning("Aucun numéro de téléphone trouvé pour l'envoi SMS", [
                    'ticket_id' => $ticket->id,
                    'type' => $type
                ]);
                return ['success' => false, 'cost' => 0];
            }

            $cleanedPhone = $this->cleanPhoneNumber($phoneNumber);

            if (!$this->isValidPhoneNumber($cleanedPhone)) {
                Log::warning("Numéro de téléphone invalide pour l'envoi SMS", [
                    'ticket_id' => $ticket->id,
                    'original' => $phoneNumber,
                    'cleaned' => $cleanedPhone
                ]);
                return ['success' => false, 'cost' => 0];
            }

            $optimizedMessage = $this->prepareSmsContent(['contenu' => $message, 'type' => $type], $ticket->voyage);
            $costEstimate = $this->estimateSmsCost($optimizedMessage, $cleanedPhone);

            Log::info("Tentative d'envoi SMS", [
                'ticket_id' => $ticket->id,
                'to' => $cleanedPhone,
                'message_length' => $costEstimate['message_length'],
                'estimated_cost' => $costEstimate['total_cost'] . ' XOF'
            ]);

            $result = $this->smsService->sendSms($cleanedPhone, $optimizedMessage, 'MOVYX');

            if (isset($result['success']) && $result['success']) {
                Log::info("SMS envoyé avec succès", [
                    'ticket_id' => $ticket->id,
                    'to' => $cleanedPhone,
                    'actual_cost' => ($result['cost'] ?? 'N/A') . ' XOF'
                ]);
                return [
                    'success' => true,
                    'cost' => $result['cost'] ?? $costEstimate['total_cost']
                ];
            } else {
                Log::error("Échec envoi SMS", [
                    'ticket_id' => $ticket->id,
                    'to' => $cleanedPhone,
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
                return ['success' => false, 'cost' => 0];
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi du SMS", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'cost' => 0];
        }
    }

    /**
     * Récupérer le numéro de téléphone selon le type
     */
    private function getPhoneNumberForSms(Ticket $ticket, string $type): ?string
    {
        if ($type === 'accident') {
            return $ticket->telephonePersonneAPrevenir;
        }
        return $ticket->telephone ?? $ticket->user?->phone_number;
    }

    /**
     * Nettoyer le numéro de téléphone
     */
    private function cleanPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        if (str_starts_with($cleaned, '00')) {
            $cleaned = '+' . substr($cleaned, 2);
        }

        if (!str_starts_with($cleaned, '+')) {
            $cleaned = '+226' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Valider le format du numéro
     */
    private function isValidPhoneNumber(string $phone): bool
    {
        $pattern = '/^\+\d{1,3}\d{4,14}$/';
        return preg_match($pattern, $phone) === 1;
    }

    /**
     * Message de succès pour combinaisons multiples
     */
    private function getCombinedSuccessMessage(array $modesEnvoi, array $results): string
    {
        $messages = [];

        if (in_array('email', $modesEnvoi)) {
            $messages[] = "{$results['email']} email(s) envoyé(s)";
        }

        if (in_array('sms', $modesEnvoi)) {
            $messages[] = "{$results['sms']} SMS envoyé(s)";
        }

        if (in_array('push', $modesEnvoi)) {
            $messages[] = "{$results['push']} notification(s) push envoyée(s)";
        }

        return "Notification envoyée : " . implode(' | ', $messages);
    }

    /**
     * Envoyer une notification push à un utilisateur
     */
    private function sendPushNotificationToUser(User $user, array $notificationData): void
    {
        try {
            Log::info("Tentative d'envoi push à l'utilisateur", [
                'user_id' => $user->id,
                'tokens_count' => count($user->fcm_tokens ?? [])
            ]);

            if (!$user->hasFcmTokens()) {
                Log::warning("L'utilisateur {$user->id} n'a pas de tokens FCM");
                return;
            }

            $result = $this->firebaseService->sendToUser(
                $user,
                $notificationData['title'],
                $notificationData['body'],
                $notificationData['data']
            );

            Log::info("Résultat envoi push", [
                'success' => $result['success'] ?? false
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur envoi notification push: " . $e->getMessage());
        }
    }

    /**
     * API pour tester l'envoi SMS
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $phone = $request->input('phone');
            $message = $request->input('message');

            $cleanedPhone = $this->cleanPhoneNumber($phone);

            // Voyage fictif pour test
            $voyage = new Voyages();
            $voyage->dateDepart = now();
            $voyage->heuresDepart = '12:00';

            $trajet = new Trajets();
            $trajet->pointDepart = 'Ouagadougou';
            $trajet->pointArrive = 'Bobo-Dioulasso';
            $trajet->setRelation('compagnie', (object)['name' => 'Transport Faso']);
            $voyage->trajet = $trajet;

            $data = ['contenu' => $message, 'type' => 'annulation'];
            $optimizedMessage = $this->prepareSmsContent($data, $voyage);

            $costEstimate = $this->estimateSmsCost($optimizedMessage, $cleanedPhone);

            $result = $this->smsService->sendSms($cleanedPhone, $optimizedMessage, 'MOVYX');

            return response()->json([
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'Unknown error',
                'cost' => $result['cost'] ?? null,
                'optimized_message' => $optimizedMessage,
                'original_length' => strlen($message),
                'optimized_length' => strlen($optimizedMessage),
                'estimated_cost' => $costEstimate['total_cost'],
                'segments' => $costEstimate['segments']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API pour enregistrer les tokens FCM
     */
    public function registerFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();
        $token = $request->input('token');

        $user->addFcmToken($token);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM enregistré avec succès'
        ]);
    }

    /**
     * API pour supprimer un token FCM
     */
    public function removeFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();
        $token = $request->input('token');

        $user->removeFcmToken($token);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM supprimé avec succès'
        ]);
    }

    /**
     * Récupérer les notifications de l'utilisateur mobile
     */
    public function getUserNotifications()
    {
        try {
            $user = Auth::user();

            $notifications = Notifications::with(['voyage.trajet'])
                ->whereHas('voyage.tickets', function($query) use ($user) {
                    $query->where('idUtilisateur', $user->id);
                })
                ->orderBy('DateEnvoie', 'desc')
                ->get()
                ->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'titre' => $notification->titre,
                        'contenu' => $notification->contenu,
                        'DateEnvoie' => $notification->DateEnvoie,
                        'type' => $notification->type,
                        'idVoyage' => $notification->idVoyage,
                        'is_read' => $notification->isRead ?? false,
                        'voyage' => $notification->voyage ? [
                            'trajet' => [
                                'pointDepart' => $notification->voyage->trajet->pointDepart ?? 'N/A',
                                'pointArrive' => $notification->voyage->trajet->pointArrive ?? 'N/A',
                            ]
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le nombre de notifications non lues (mobile)
     */
    public function getUnreadNotificationsCount()
    {
        try {
            $user = Auth::user();

            $unreadCount = Notifications::whereHas('voyage.tickets', function($query) use ($user) {
                $query->where('idUtilisateur', $user->id);
            })
            ->where('isRead', false)
            ->count();

            return response()->json([
                'success' => true,
                'unreadCount' => $unreadCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du compteur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer une notification comme lue (mobile)
     */
    public function markNotificationAsRead($id)
    {
        try {
            $user = Auth::user();

            $notification = Notifications::where('id', $id)
                ->whereHas('voyage.tickets', function($query) use ($user) {
                    $query->where('idUtilisateur', $user->id);
                })
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }

            $notification->update(['isRead' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de la notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer toutes les notifications comme lues (mobile)
     */
    public function markAllNotificationsAsRead()
    {
        try {
            $user = Auth::user();

            Notifications::whereHas('voyage.tickets', function($query) use ($user) {
                $query->where('idUtilisateur', $user->id);
            })->update(['isRead' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications marquées comme lues'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage des notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test des notifications push
     */
    public function testPushNotification(Request $request)
    {
        $user = Auth::user();

        $currentTokens = $user->fcm_tokens ?? [];
        Log::info("Tokens FCM actuels", ['tokens' => $currentTokens]);

        $testToken = "test_token_123";
        $user->addFcmToken($testToken);

        $updatedTokens = $user->fresh()->fcm_tokens;
        Log::info("Tokens FCM après ajout", ['tokens' => $updatedTokens]);

        $result = $this->firebaseService->sendToUser(
            $user,
            'Test Debug',
            'Ceci est un test de notification',
            ['test' => 'true']
        );

        return response()->json([
            'user_id' => $user->id,
            'initial_tokens' => $currentTokens,
            'updated_tokens' => $updatedTokens,
            'push_result' => $result
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Notifications $notification)
    {
        return view('back.notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notifications $notification)
    {
        $user = Auth::user();
        $query = Voyages::with(['trajet.garresDepart', 'trajet.garresArrivee', 'trajet.frequences', 'bus', 'tickets'])
            ->orderBy('dateDepart', 'desc');

        if ($user->profil?->name === 'Admin compagnie') {
            $query->whereHas('trajet', function ($q) use ($user) {
                $q->where('idCompagnie', $user->idCompagnie);
            });
        } elseif (in_array($user->profil?->name, ['Chef de gare', 'Réceptionniste'])) {
            $trajetIds = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');
            $query->whereIn('idTrajet', $trajetIds);
        }

        $voyages = $query->get();

        return view('back.notifications.edit', compact('notification', 'voyages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationsRequest $request, Notifications $notification)
    {
        try {
            $data = $request->validated();

            $notification->update($data);

            return redirect()->route('notifications.index')
                ->with('success', 'Notification mise à jour avec succès');

        } catch (\Exception $e) {
            Log::error("Erreur mise à jour notification", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour de la notification')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notifications $notification)
    {
        try {
            $notification->delete();

            return redirect()->route('notifications.index')
                ->with('success', 'Notification supprimée avec succès');

        } catch (\Exception $e) {
            Log::error("Erreur suppression notification", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('notifications.index')
                ->with('error', 'Erreur lors de la suppression de la notification');
        }
    }
}
