<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reservations extends Model
{
    //
    protected $fillable = [
        'idUtilisateur',
        'idVoyage',
        'nombrePlaces',
        'montantTotal',
        'passagers',
        'statut',
        'idPaiement',
    ];

    protected $casts = [
        'passagers' => 'array',
    ];

    public function utilisateur()
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
}
