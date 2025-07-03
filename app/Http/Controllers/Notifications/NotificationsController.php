<?php



namespace App\Http\Controllers\Notifications;

//use App\Http\Controllers\Notifications;

use App\Models\Trajets;
use Illuminate\Support\Carbon;
use App\Models\Voyages;
use App\Models\Bus;
use App\Models\FrequenceTrajets;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Models\Tickets;

use App\Http\Controllers\Notifications\NotificationsController;

use App\Models\User;
use App\Http\Requests\StoreNotificationsRequest;
use App\Http\Requests\UpdateNotificationsRequest;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $notifications = Notifications::with(['voyage.tickets.user'])
        ->orderBy('DateEnvoie', 'desc')
        ->get();

    return view('back.notifications.index', compact('notifications'));
}


    /**
     * Show the form for creating a new resource.
     */
 public function create()
{
    $voyages = Voyages::with(['trajet.garresDepart', 'trajet.garresArrivee', 'trajet.frequences', 'bus', 'tickets'])->orderBy('dateDepart', 'desc')->get();
    return view('back.notifications.create', compact('voyages'));
}



    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreNotificationsRequest $request)
    // {
      public function store(Request $request)
{
    $data = $request->validate([
        'titre' => 'required|string',
        'contenu' => 'required|string',
        'type' => 'required|string',
        'idVoyage' => 'required|exists:voyages,id',
    ]);

    $data['DateEnvoie'] = now(); // Automatiquement la date/heure du moment

    $notification = Notifications::create($data);

    $voyage = Voyages::with('tickets')->findOrFail($data['idVoyage']);

   foreach ($voyage->tickets as $ticket) {
    $email = $data['type'] === 'accident'
        ? $ticket->emailPersonneAPrevenir
        : ($ticket->email ?? $ticket->user?->email);

    if ($email) {
        // ✅ Récupération des infos
        $voyage = $ticket->voyage;
        $trajet = $voyage->trajet;

        $pointDepart = $trajet->pointDepart ?? 'N/A';
        $pointArrive = $trajet->pointArrive ?? 'N/A';
        $dateDepart = $voyage->dateDepart ?? 'N/A';
        $heureDepart = $voyage->heuresDepart ?? 'N/A';
        $busNumero = $voyage->bus?->numero ?? 'Aucun';
        $compagnie = $trajet->compagnie?->name ?? 'Compagnie inconnue';

        // ✅ Contenu final
        $contenuFinal = <<<EOT
{$data['contenu']}

🚌 Compagnie : {$compagnie}
📍 Trajet : {$pointDepart} → {$pointArrive}
🗓️ Date de départ : {$dateDepart}
🕒 Heure de départ : {$heureDepart}
🚌 Bus n° : {$busNumero}
EOT;

        // ✅ Envoi de l'e-mail
        Mail::raw($contenuFinal, function ($message) use ($email, $data) {
            $message->to($email)->subject($data['titre']);
        });
    }
}


     return redirect()->route('notifications.index')->with('success', 'Notification envoyée.');
    //return redirect()->back()->with('success', 'Notification envoyée.');
}
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
