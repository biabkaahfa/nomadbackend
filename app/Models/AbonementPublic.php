<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbonementPublic extends Model
{
    protected $fillable = [
        'idUser',
        'idCompagnie',
        'duree',
        'nom',
        'prenom',
        'profession',
        'etablissement',
        'photo',
        'statut',
        'duree',
        'dateNaiss',
        'dateDebut',
        'dateFin',
        'idPaiement'
    ];

    const DUREE_MINIMUM = 30; // Minimum 1 mois

    protected $dates = ['dateDebut', 'dateFin'];

    /**
     * Scope pour les abonnements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('dateFin', '>=', Carbon::today())
                    ->where('statut', 'actif');
    }

    /**
     * Vérifie si l'abonnement est actif
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->statut === 'actif' &&
               Carbon::today()->between($this->dateDebut, $this->dateFin);
    }

    /**
     * Relation avec les QR codes temporaires
     */
    public function qrTemporaires(): HasMany
    {
        return $this->hasMany(QrTemporaireAbonnement::class, 'idAbonementPublic');
    }

    /**
     * Relation avec le paiement
     */
    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiements::class, 'idPaiement');
    }

    /**
     * Relation avec la compagnie
     */
    public function compagnie(): BelongsTo
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }

    /**
     * QR code temporaire actif
     */
    public function qrTemporaireActif()
    {
        return $this->hasOne(QrTemporaireAbonnement::class, 'idAbonementPublic')
                    ->valide()
                    ->latest();
    }
}

