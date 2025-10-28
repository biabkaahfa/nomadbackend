<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'dateReservation',
        'statut',
        'idUtilisateur',
        'name',
        'telephone',
        'email',
        'typeAchat',
        'modeReception',
        'idVoyage',
        'idGarre',
        'dateScan',
        'idPaiement',
        'namePersonneAPrevenir',
        'numeroPersonneAPrevenir',
        'emailPersonneAPrevenir',
        'qr_code_version',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUtilisateur');
    }

    public function voyage()
    {
        return $this->belongsTo(Voyages::class, 'idVoyage');
    }

    public function paiement()
    {
        return $this->belongsTo(Paiements::class, 'idPaiement');
    }
     public function garre()
    {
        return $this->belongsTo(Garres::class, 'idGarre');
    }

    /**
     * Génère et sauvegarde un QR code (version SIMPLIFIÉE)
     */


    /**
     * Vérifie si le QR code est valide
     */


    // ⚠️ SUPPRIMEZ COMPLÈTEMENT CET ACCESSOR QUI CAUSE LA BOUCLE INFINIE
    // public function getQrCodeBase64Attribute($value)
    // {
    //     // CET ACCESSOR CRÉE UNE BOUCLE INFINIE - SUPPRIMEZ-LE
    //     if (!$value || !$this->hasValidQrCode()) {
    //         $this->genererQrCode();
    //         $freshTicket = $this->fresh();
    //         return $freshTicket ? $freshTicket->qr_code_base64 : null;
    //     }
    //     return $value;
    // }
}
