<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voyages extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'heuresDepart',
        'idTrajet',
        'dateDepart',
        'idBus'
    ];

    public function trajet()
    {
        return $this->belongsTo(Trajets::class, 'idTrajet');
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'idBus');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'idVoyage');
    }

    /**
     * Relation avec la compagnie via le trajet
     */
    public function compagnie()
    {
        return $this->hasOneThrough(
            Compagnies::class,
            Trajets::class,
            'id', // Clé étrangère sur Trajets
            'id', // Clé étrangère sur Compagnies
            'idTrajet', // Clé locale sur Voyages
            'idCompagnie' // Clé intermédiaire sur Trajets
        );
    }

    /**
     * Accesseur pour le point de départ
     */
    public function getPointDepartAttribute()
    {
        return $this->trajet ? $this->trajet->pointDepart : null;
    }

    /**
     * Accesseur pour le point d'arrivée
     */
    public function getPointArriveeAttribute()
    {
        return $this->trajet ? $this->trajet->pointArrive : null;
    }

    /**
     * Accesseur pour le trajet complet
     */
    public function getTrajetCompletAttribute()
    {
        if ($this->trajet) {
            return $this->trajet->pointDepart . ' → ' . $this->trajet->pointArrive;
        }
        return 'Trajet non défini';
    }

    /**
     * Scope pour les voyages d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('dateDepart', today());
    }

    /**
     * Vérifier si le voyage est pour aujourd'hui
     */
    public function isToday()
    {
        return $this->dateDepart && $this->dateDepart->isToday();
    }
}
