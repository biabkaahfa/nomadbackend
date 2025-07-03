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
    public $pdfContent;
    public $garres; // ✅ Nouvelle propriété

    /**
     * Create a new message instance.
     */
    public function __construct($client, $compagnie, $date, $pdfContent, $garres = null)
    {
        $this->client = $client;
        $this->compagnie = $compagnie;
        $this->date = $date;
        $this->pdfContent = $pdfContent;
        $this->garres = $garres;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Votre Ticket - NOMAD')
            ->view('emails.ticket')
            ->with([
                'client' => $this->client,
                'compagnie' => $this->compagnie,
                'date' => $this->date,
                'garres' => $this->garres, // ✅ Passer les garres à la vue
            ])
            ->attachData($this->pdfContent, 'ticket_nomad.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
