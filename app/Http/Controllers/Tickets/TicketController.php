<?php

//namespace App\Http\Controllers;
namespace App\Http\Controllers\Tickets;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Voyages;
use Illuminate\Support\Carbon;
use App\Models\Paiements;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\TicketMail;
//use App\Models\Ticket;
//use App\Models\Paiements;
use Illuminate\Support\Facades\Log;


class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //
        $ticket=Ticket::all();
        return view("back.Tickets.index",['tickets'=>$ticket]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{


$now = Carbon::now(); // Date et heure actuelles

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

        // Générer QR Code
        $qrData = [
            'ticket_id' => $ticket->id,
            'client' => $ticket->name,
            'email' => $ticket->email,
            'montant' => $paiement->montant,
            'moyenPaiement' => $paiement->moyenPaiement,
            'reference' => $paiement->referenceTransaction,
        ];
        $qrCode = QrCode::format('png')->size(200)->generate(json_encode($qrData));

        $client = $ticket->name;
        $date = $ticket->voyage->dateDepart;
        $depart = $ticket->voyage->trajet->pointDepart;
        $arrivee = $ticket->voyage->trajet->pointArrive;
        $compagnie = $ticket->voyage->trajet->compagnie ?? null;

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
