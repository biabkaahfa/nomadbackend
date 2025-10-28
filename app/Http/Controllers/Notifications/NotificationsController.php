<?php

namespace App\Http\Controllers\Notifications;

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
        // ✅ AJOUT: Initialiser le service Firebase
        $this->firebaseService = new FirebaseService();
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

        return [
            'title' => $data['titre'],
            'body' => $data['contenu'],
            'data' => [
                'type' => 'notification_voyage',
                'notification_type' => $data['type'],
                'voyage_id' => (string) $voyage->id,
                'trajet' => $trajet->pointDepart . ' → ' . $trajet->pointArrive,
                'date_depart' => $voyage->dateDepart?->toDateString(),
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
            $result = $this->firebaseService->sendToUser(
                $user,
                $notificationData['title'],
                $notificationData['body'],
                $notificationData['data']
            );

            // Log du résultat
            \Log::info("Notification push envoyée à l'utilisateur {$user->id}", [
                'success' => $result['success'] ?? false,
                'user_id' => $user->id,
                'tokens_count' => count($user->fcm_tokens ?? []),
                'error' => $result['error'] ?? null,
            ]);

        } catch (\Exception $e) {
            \Log::error("Erreur envoi notification push à l'utilisateur {$user->id}: " . $e->getMessage());
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
    /**
     * Display a listing of the resource.
     */
    public function index()
{
     $user = Auth::user();

    $query = Notifications::with(['voyage.tickets.user'])
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
{$user = Auth::user();
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
     * Store a newly created resource in storage.
     */
    // public function store(StoreNotificationsRequest $request)
    // {
//       public function store(Request $request)
// {
//     $data = $request->validate([
//         'titre' => 'required|string',
//         'contenu' => 'required|string',
//         'type' => 'required|string',
//         'idVoyage' => 'required|exists:voyages,id',
//     ]);

//     $data['DateEnvoie'] = now(); // Automatiquement la date/heure du moment

//     $notification = Notifications::create($data);

//     $voyage = Voyages::with('tickets')->findOrFail($data['idVoyage']);

//    foreach ($voyage->tickets as $ticket) {
//     $email = $data['type'] === 'accident'
//         ? $ticket->emailPersonneAPrevenir
//         : ($ticket->email ?? $ticket->user?->email);

//     if ($email) {
//         // ✅ Récupération des infos
//         $voyage = $ticket->voyage;
//         $trajet = $voyage->trajet;

//         $pointDepart = $trajet->pointDepart ?? 'N/A';
//         $pointArrive = $trajet->pointArrive ?? 'N/A';
//         $dateDepart = $voyage->dateDepart ?? 'N/A';
//         $heureDepart = $voyage->heuresDepart ?? 'N/A';
//         $busNumero = $voyage->bus?->numero ?? 'Aucun';
//         $compagnie = $trajet->compagnie?->name ?? 'Compagnie inconnue';

//         // ✅ Contenu final
//         $contenuFinal = <<<EOT
// {$data['contenu']}

// 🚌 Compagnie : {$compagnie}
// 📍 Trajet : {$pointDepart} → {$pointArrive}
// 🗓️ Date de départ : {$dateDepart}
// 🕒 Heure de départ : {$heureDepart}
// 🚌 Bus n° : {$busNumero}
// EOT;

//         // ✅ Envoi de l'e-mail
//         Mail::raw($contenuFinal, function ($message) use ($email, $data) {
//             $message->to($email)->subject($data['titre']);
//         });
//     }
// }


//      return redirect()->route('notifications.index')->with('success', 'Notification envoyée.');
//     //return redirect()->back()->with('success', 'Notification envoyée.');
// }
  //
    // }

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
