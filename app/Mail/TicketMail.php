<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $compagnie;
    public $date;
    public $pdf;
    public $garres;
    public $gareDepart;
    public $qrCode; // ✅ NOUVEAU : Ajouter qrCode
    public $ticket_id; // ✅ NOUVEAU : Ajouter ticket_id
    public $depart; // ✅ NOUVEAU : Ajouter depart
    public $arrivee; // ✅ NOUVEAU : Ajouter arrivee

    public function __construct($client, $compagnie, $date, $pdf, $garres = [], $gareDepart = null, $qrCode = null, $ticket_id = null, $depart = null, $arrivee = null)
    {
        $this->client = $client;
        $this->compagnie = $compagnie;
        $this->date = $date;
        $this->pdf = $pdf;
        $this->garres = $garres;
        $this->gareDepart = $gareDepart;
        $this->qrCode = $qrCode; // ✅ NOUVEAU
        $this->ticket_id = $ticket_id; // ✅ NOUVEAU
        $this->depart = $depart; // ✅ NOUVEAU
        $this->arrivee = $arrivee; // ✅ NOUVEAU
    }

    public function build()
    {
        // ✅ CORRECTION : Passer TOUTES les variables à la vue
        return $this->subject('Votre ticket de voyage - ' . ($this->compagnie->name ?? 'Movyx'))
            ->view('emails.ticket')
            ->attachData($this->pdf, 'ticket-voyage.pdf', [
                'mime' => 'application/pdf',
            ])
            ->with([
                'client' => $this->client,
                'compagnie' => $this->compagnie,
                'date' => $this->date,
                'garres' => $this->garres,
                'gare_depart' => $this->gareDepart, // ✅ Note: changement de nom pour correspondre à la vue
                'qrCode' => $this->qrCode, // ✅ NOUVEAU
                'ticket_id' => $this->ticket_id, // ✅ NOUVEAU
                'depart' => $this->depart, // ✅ NOUVEAU
                'arrivee' => $this->arrivee, // ✅ NOUVEAU
            ]);
    }
}
