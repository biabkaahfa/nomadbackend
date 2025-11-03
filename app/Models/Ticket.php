<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'dateReservation',
        'statut',
        'idUtilisateur',
        'modeReception',
        'typeAchat',
        'name',
        'telephone',
        'email',
        'idVoyage',
        'dateScan',
        'idPaiement',
        'namePersonneAPrevenir',
        'numeroPersonneAPrevenir',
        'emailPersonneAPrevenir',
        'localisation_gare_depart', // ✅ AJOUT
        'nom_gare_depart', // ✅ AJOUT
    ];

    protected $casts = [
        'passagers' => 'array',
    ];

    // Relations existantes
    public function voyage()
    {
        return $this->belongsTo(Voyages::class, 'idVoyage');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'idUtilisateur');
    }

    public function paiement()
    {
        return $this->belongsTo(Paiements::class, 'idPaiement');
    }

    // ✅ NOUVEAU : Accessor pour obtenir les coordonnées
    public function getCoordonneesGareDepartAttribute()
    {
        if (!$this->localisation_gare_depart) {
            return null;
        }

        $parts = explode(',', $this->localisation_gare_depart);
        if (count($parts) === 2) {
            return [
                'latitude' => floatval(trim($parts[0])),
                'longitude' => floatval(trim($parts[1])),
            ];
        }

        return null;
    }

    // ✅ NOUVEAU : Vérifier si la localisation est disponible
    public function getPossedeLocalisationAttribute()
    {
        return !empty($this->localisation_gare_depart);
    }

    // ✅ NOUVEAU : Générer le lien Google Maps
    public function getLienGoogleMapsAttribute()
    {
        if (!$this->localisation_gare_depart) {
            return null;
        }

        $coords = $this->coordonnees_gare_depart;
        if ($coords) {
            return "https://www.google.com/maps?q={$coords['latitude']},{$coords['longitude']}";
        }

        return null;
    }

    // ✅ NOUVEAU : Formater l'adresse pour l'affichage
    public function getAdresseGareDepartAttribute()
    {
        if (!$this->nom_gare_depart) {
            return null;
        }

        return "Gare {$this->nom_gare_depart}";
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'idUtilisateur');
    }

    // public function voyage()
    // {
    //     return $this->belongsTo(Voyages::class, 'idVoyage');
    // }

    // public function paiement()
    // {
    //     return $this->belongsTo(Paiements::class, 'idPaiement');
    // }
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
