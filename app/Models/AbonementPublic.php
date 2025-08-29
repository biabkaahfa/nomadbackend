<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbonementPublic extends Model
{
    //
     protected $fillable = [
        'idUser',
        'idCompagnie',
        'duree',
        'nom',
        'prenom',
        'profession',
        'etablissement',
        'Photo',
        'statut',
        'duree',
        'dateNaiss',
        'dateDebut',
        'dateFin'
    ];
    const DUREE_PAR_DEFAUT = 30;

// Dans le modèle AbonementPublic.php
public function scopeActive($query)
{
    return $query->where('dateFin', '>=', Carbon::today());
}
    public function compagnie(): BelongsTo
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }

    protected $dates = ['dateDebut', 'dateFin'];
}
