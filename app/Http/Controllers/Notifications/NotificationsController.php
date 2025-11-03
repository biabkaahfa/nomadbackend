<?php

namespace App\Http\Controllers\Notifications;

use Log;
use App\Models\Bus;
use App\Models\User;
use App\Models\Tickets;
use App\Models\Trajets;
use App\Models\Voyages;
use App\Models\GarreTrajets;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Carbon;
use App\Models\FrequenceTrajets;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseServiceV1;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\FirebaseService; // ✅ AJOUT
use App\Http\Requests\StoreNotificationsRequest;
use App\Http\Requests\UpdateNotificationsRequest;

class NotificationsController extends Controller
{
    protected $firebaseService;

    public function __construct()
    {
        // Utilisez le nouveau service FCM v1
        $this->firebaseService = new FirebaseService();
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
                        'is_read' => $notification->isRead ?? false, // CORRECTION: isRead au lieu de is_read
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
            ->where('isRead', false) // CORRECTION: isRead au lieu de is_read
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

            $notification->update(['isRead' => true]); // CORRECTION: isRead au lieu de is_read

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
            })->update(['isRead' => true]); // CORRECTION: isRead au lieu de is_read

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string',
            'contenu' => 'required|string',
            'type' => 'required|string',
            'idVoyage' => 'required|exists:voyages,id',
        ]);

        $data['DateEnvoie'] = now();

        $notification = Notifications::create($data);
        $voyage = Voyages::with(['tickets.user', 'trajet'])->findOrFail($data['idVoyage']);

        // ✅ AJOUT: Préparer les données pour la notification push
        $pushData = $this->preparePushNotificationData($data, $voyage);

        foreach ($voyage->tickets as $ticket) {
            $email = $data['type'] === 'accident'
                ? $ticket->emailPersonneAPrevenir
                : ($ticket->email ?? $ticket->user?->email);

            // ✅ ENVOI EMAIL (votre code existant)
            if ($email) {
                $voyage = $ticket->voyage;
                $trajet = $voyage->trajet;

                $pointDepart = $trajet->pointDepart ?? 'N/A';
                $pointArrive = $trajet->pointArrive ?? 'N/A';
                $dateDepart = $voyage->dateDepart ?? 'N/A';
                $heureDepart = $voyage->heuresDepart ?? 'N/A';
                $busNumero = $voyage->bus?->numero ?? 'Aucun';
                $compagnie = $trajet->compagnie?->name ?? 'Compagnie inconnue';

                $contenuFinal = <<<EOT
{$data['contenu']}

🚌 Compagnie : {$compagnie}
📍 Trajet : {$pointDepart} → {$pointArrive}
🗓️ Date de départ : {$dateDepart}
🕒 Heure de départ : {$heureDepart}
🚌 Bus n° : {$busNumero}
EOT;

                Mail::raw($contenuFinal, function ($message) use ($email, $data) {
                    $message->to($email)->subject($data['titre']);
                });
            }

            // ✅ AJOUT: ENVOI NOTIFICATION PUSH
            if ($ticket->user && $ticket->user->hasFcmTokens()) {
                $this->sendPushNotificationToUser($ticket->user, $pushData);
            }
        }

        return redirect()->route('notifications.index')->with('success', 'Notification envoyée par email et push.');
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Préparer les données pour la notification push
     */
    private function preparePushNotificationData(array $data, Voyages $voyage): array
    {
        $trajet = $voyage->trajet;

        // CORRECTION : Gérer le format de date de manière sécurisée
        $dateDepart = $voyage->dateDepart;

        // Si c'est une string, la convertir en Carbon
        if (is_string($dateDepart)) {
            $dateDepart = \Carbon\Carbon::parse($dateDepart);
        }

        // Si c'est un objet Carbon, formater la date
        if ($dateDepart instanceof \Carbon\Carbon) {
            $dateDepartFormatted = $dateDepart->format('Y-m-d');
        } else {
            // Sinon, utiliser la valeur telle quelle ou une valeur par défaut
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
                'date_depart' => $dateDepartFormatted, // CORRIGÉ
                'heure_depart' => $voyage->heuresDepart,
                'screen' => 'voyage_details',
                'action' => 'voir_details',
                'urgence' => in_array($data['type'], ['accident', 'retard_important', 'annulation']),
            ]
        ];
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Envoyer une notification push à un utilisateur
     */
    private function sendPushNotificationToUser(User $user, array $notificationData): void
    {
        try {
            \Log::info("Tentative d'envoi push à l'utilisateur", [
                'user_id' => $user->id,
                'tokens' => $user->fcm_tokens,
                'tokens_count' => count($user->fcm_tokens ?? []),
                'notification_title' => $notificationData['title']
            ]);

            // Vérifiez si l'utilisateur a des tokens
            if (!$user->hasFcmTokens()) {
                \Log::warning("L'utilisateur {$user->id} n'a pas de tokens FCM");
                return;
            }

            $result = $this->firebaseService->sendToUser(
                $user,
                $notificationData['title'],
                $notificationData['body'],
                $notificationData['data']
            );

            Log::info("Résultat envoi push", [
                'success' => $result['success'] ?? false,
                'response' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error("Erreur envoi notification push: " . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE: API pour enregistrer les tokens FCM (pour l'app mobile)
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
     * ✅ NOUVELLE MÉTHODE: API pour supprimer un token FCM
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

    public function testPushNotification(Request $request)
    {
        $user = Auth::user();

        // 1. Vérifier les tokens actuels
        $currentTokens = $user->fcm_tokens ?? [];
        \Log::info("Tokens FCM actuels", ['tokens' => $currentTokens]);

        // 2. Tester l'ajout d'un token
        $testToken = "test_token_123";
        $user->addFcmToken($testToken);

        // 3. Vérifier après ajout
        $updatedTokens = $user->fresh()->fcm_tokens;
        \Log::info("Tokens FCM après ajout", ['tokens' => $updatedTokens]);

        // 4. Tester l'envoi de notification
        $firebaseService = new FirebaseService();
        $result = $firebaseService->sendToUser(
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

        $notifications = $query->get();

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
            // Récupérer les trajets liés à la gare de l'utilisateur
            $trajetIds = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');
            $query->whereIn('idTrajet', $trajetIds);
        }

        $voyages = $query->get();

        return view('back.notifications.create', compact('voyages'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Notifications $notifications)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notifications $notifications)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationsRequest $request, Notifications $notifications)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notifications $notifications)
    {
        //
    }
}
