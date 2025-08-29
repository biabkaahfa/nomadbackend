<?php

//namespace App\Http\Controllers;
namespace App\Http\Controllers\Tickets;

use App\Models\Ticket;
use App\Models\Voyages;
use App\Mail\TicketMail;
use App\Models\Paiements;
use App\Models\parametres;
use App\Models\GarreTrajets;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
//use App\Models\Ticket;
//use App\Models\Paiements;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Response;


class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

     $user = Auth::user();
    $tickets = collect(); // Par défaut, vide

    if ($user->profil->name === 'Admin général') {
        $tickets = Ticket::with(['voyage.trajet', 'voyage.bus'])->get();

    } elseif ($user->profil->name === 'Admin compagnie') {
        // Obtenir les tickets liés aux trajets de sa compagnie
        $tickets = Ticket::whereHas('voyage.trajet', function ($q) use ($user) {
            $q->where('idCompagnie', $user->idCompagnie);
        })->with(['voyage.trajet', 'voyage.bus'])->get();

    } elseif (in_array($user->profil->name, ['Chef de gare', 'Réceptionniste'])) {
        // Obtenir les idTrajet liés à sa gare via la table garre_trajets
        $idTrajets = GarreTrajets::where('idGarre', $user->idGarre)->pluck('idTrajet');

        $tickets = Ticket::whereHas('voyage.trajet', function ($q) use ($idTrajets, $user) {
            $q->whereIn('id', $idTrajets)
              ->where('idCompagnie', $user->idCompagnie);
        })->with(['voyage.trajet', 'voyage.bus'])->get();
    }

    return view("back.Tickets.index", ['tickets' => $tickets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{

$user = auth()->user();

    // Autoriser uniquement les réceptionnistes
    if ($user->profil->name !== 'Réceptionniste') {
        abort(403, 'Seuls les réceptionnistes peuvent créer des tickets.');
    }

    $now = \Carbon\Carbon::now();

    $voyages = Voyages::whereRaw("
            STR_TO_DATE(CONCAT(dateDepart, ' ', heuresDepart), '%Y-%m-%d %H:%i:%s') >= ?
        ", [$now])
        ->with(['trajet.frequences', 'bus', 'tickets'])
        ->orderBy('dateDepart')
        ->orderBy('heuresDepart')
        ->get();

    return view("back.Tickets.create", compact('voyages'));
}

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreTicketRequest $request)
    // {
    //     //
    //     $data= $request->validated();
    //      $data['typeAchat'] = 'sur_place';
    //     Ticket::create($data);

    //     return redirect()->route('tickets.index')->with('success', 'Ticket enregistré avec succès.');


  public function store(StoreTicketRequest $request)
{
    DB::beginTransaction();

    try {
       // dd($request);
        $voyage = Voyages::with(['trajet.frequences', 'bus'])->findOrFail($request->idVoyage);

        // Vérification des places
        if ($voyage->idBus && $voyage->bus->nombrePlaceDispo <= 0) {
            return back()->with('error', 'Aucune place disponible pour ce voyage.');
        }

        if (!$voyage->idBus) {
            $frequence = $voyage->trajet->frequences
                ->where('heureDepart', $voyage->heuresDepart)->first();

            if (!$frequence) {
                return back()->with('error', 'Fréquence non trouvée pour ce voyage.');
            }

            $nbTickets = Ticket::where('idVoyage', $voyage->id)->count();
            if ($nbTickets >= $frequence->nombrePlaceMinimum) {
                return back()->with('error', 'Limite de places atteinte sans bus assigné.');
            }
        }

        // Création du paiement
        $paiement = Paiements::create([
            'montant' => $request->montant,
            'moyenPaiement' => $request->moyenPaiement,
            'statut' => $request->statutPaiement,
            'typeSource' => 'GUICHET',
            'telephone' => $request->telephone,
            'referenceTransaction' => $request->referenceTransaction,
        ]);
      // dd($paiement);
        // Création du ticket
        $ticket = Ticket::create([
            'dateReservation' => $request->dateReservation,
            'statut' => $request->statut,
            'modeReception' => $request->modeReception,
            'typeAchat' => 'sur_place',
           // 'modeAchat' => 'sur place',
            'name' => $request->name,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'idVoyage' => $request->idVoyage,
            'dateScan' => null,
            'idPaiement' => $paiement->id,
            'namePersonneAPrevenir' => $request->namePersonneAPrevenir,
            'numeroPersonneAPrevenir' => $request->numeroPersonneAPrevenir,
            'emailPersonneAPrevenir' => $request->emailPersonneAPrevenir,
        ]);
       // dd($ticketData);

        // Décrémenter les places
        if ($voyage->idBus) {
            $voyage->bus->decrement('nombrePlaceDispo');
        }
           $compagnie = $ticket->voyage->trajet->compagnie ?? null;

        // Générer QR Code
        $qrData = [
            'ticket_id' => $ticket->id,
            'client' => $ticket->name,
            'email' => $ticket->email,
            'montant' => $paiement->montant,
            'moyenPaiement' => $paiement->moyenPaiement,
            'reference' => $paiement->referenceTransaction,
        ];
        $idCompagnie=$compagnie->idCompagnie;

       if ($idCompagnie) {
            $theme = Parametres::where('idCompagnie', $idCompagnie)->first();
        } else {
            // No company ID provided, use the global theme.
            $theme = Parametres::whereNull('idCompagnie')->first();
        }

        // Handle the case where the theme is not found.
        if (!$theme) {
            return Response::make('Theme not found.', 404);
        }

        // 2. Determine the logo path.
        // Use the company-specific logo if it exists, otherwise use the default.
        $logoPath = $theme->logo ? public_path('storage/' . $theme->logo) : public_path('back_auth/assets/img/Movyx.png');

        // Check if the logo file actually exists to prevent errors.
        if (!file_exists($logoPath)) {
            // Fallback to the default logo if the company's logo is missing.
            $logoPath = public_path('back_auth/assets/img/Movyx.png');
        }

                // Générer le QR Code avec le logo fusionné
                // La méthode 'merge' prend le chemin de l'image, et en option, le ratio de taille (0.2 = 20%) et si la transparence est activée.
                $qrCode = QrCode::format('png')
                                ->size(200)
                                ->merge($logoPath, 0.2, true)
                                ->generate(json_encode($qrData));
                // --- FIN DE LA MODIFICATION ---

        $client = $ticket->name;
        $date = $ticket->voyage->dateDepart;
        $depart = $ticket->voyage->trajet->pointDepart;
        $arrivee = $ticket->voyage->trajet->pointArrive;


        $garres = $compagnie
            ? $compagnie->garres()
                ->where(function ($query) use ($depart, $arrivee) {
                    $query->whereRaw('LOWER(ville) = ?', [strtolower($depart)])
                          ->orWhereRaw('LOWER(ville) = ?', [strtolower($arrivee)]);
                })
                ->get(['name', 'ville', 'localisation'])
            : collect();

        $pdf = Pdf::loadView('back.pdf.ticket', [
            'ticket_id' => $ticket->id,
            'ticket' => $ticket,
            'date' => $date,
            'depart' => $depart,
            'arrivee' => $arrivee,
            'client' => $client,
            'qrCode' => $qrCode,
            'compagnie' => $compagnie,
            'garres' => $garres,
        ]);

        Log::info('Mode de réception : ' . $ticket->modeReception);
        if ($ticket->modeReception === 'email' && $ticket->email) {

             try {
      Mail::to($ticket->email)->send(new TicketMail($client, $compagnie, $date, $pdf->output(), $garres));
    } catch (\Exception $e) {
        Log::error('Erreur envoi email : ' . $e->getMessage());
    }
        }

        DB::commit();
        return redirect()->route('tickets.index')->with('success', 'Ticket et paiement enregistrés avec succès.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur ticket : ' . $e->getMessage());
        return back()->with('error', 'Erreur : ' . $e->getMessage());
    }
}



    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
      // $ticket=Ticket::all();
      $voyages=Voyages::all();
        return view("back.Tickets.create",['ticket'=>$ticket,'voyages'=>$voyages]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        //
       // dd('envoyer');

    $data = $request->validated();
    $ticket->update($data);

    return redirect()->route('tickets.index')->with('success', 'Ticket modifié avec succès.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
        $ticket->delete();

       return redirect()->route('tickets.index')->with('success', 'Ticket supprimer avec succès.');

    }
}
