<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class parametres extends Model
{
    //
     protected $fillable = [
        'idCompagnie',
        'logo',
        'couleur_principale',
        'couleur_secondaire',
        'slogan'
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }
}
