<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservations extends Model
{
    use HasFactory;

    protected $fillable = [
        'idUtilisateur',
        'idVoyage',
        'idVoyageRetour',
        'nombrePlaces',
        'montantTotal',
        'passagers',
        'statut',
        'idPaiement'
    ];

    protected $casts = [
        'passagers' => 'array', // ✅ Assure que passagers est converti en array
        'montantTotal' => 'decimal:2',
    ];

    // ✅ CORRECTION : Relation avec le voyage aller
    public function voyage()
    {
        return $this->belongsTo(Voyages::class, 'idVoyage');
    }

    // ✅ CORRECTION : Relation avec le voyage retour
    public function voyageRetour()
    {
        return $this->belongsTo(Voyages::class, 'idVoyageRetour');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'idUtilisateur');
    }

    // ✅ Relation avec le paiement
    public function paiement()
    {
        return $this->belongsTo(Paiements::class, 'idPaiement');
    }
}
