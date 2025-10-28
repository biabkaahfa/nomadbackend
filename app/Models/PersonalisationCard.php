<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalisationCard extends Model
{
    //
    protected $fillable = [
        'pays',
        'devise',
        'idCompagnie',
        'numero',
        'couleur_principale',
        'prix'
    ];


    //  public function compagnie(): BelongsTo
    // {
    //     return $this->belongsTo(Compagnies::class, 'idCompagnie');
    // }
 public function compagnie(): BelongsTo
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }
}
