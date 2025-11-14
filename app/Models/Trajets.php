<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trajets extends Model
{
    protected $fillable = [
        'idCompagnie',
        'pointDepart',
        'pointArrive',
        'prix',
        'prixAllerRetour',
        'status',
        'duree',
        'distance',
    ];

    // Relation avec les gares
    public function gares()
    {
        return $this->belongsToMany(Garres::class, 'garre_trajets', 'idTrajet', 'idGarre');
    }
    public function garres()
    {
        return $this->belongsToMany(Garres::class, 'garre_trajets', 'idTrajet', 'idGarre');
    }
        public function garresDepart()
    {
        // Si cette relation n'existe pas, vous devez la créer
        return $this->belongsTo(Garres::class,'garre_trajets', 'idTrajet', 'idGarre');
    }

    public function garresArrivee()
    {
        return $this->belongsTo(Garres::class, 'garre_trajets', 'idTrajet', 'idGarre');
    }

    // // Ou peut-être que la relation existe sous un autre nom :
    // public function gareDepart()
    // {
    //     return $this->belongsTo(Garres::class, 'idGarreDepart');
    // }

    // public function gareArrivee()
    // {
    //     return $this->belongsTo(Garres::class, 'idGarreArrivee');
    // }

    public function compagnie()
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }

    public function voyages()
    {
        return $this->hasMany(Voyages::class, 'idTrajet');
    }

    public function frequence()
    {
        return $this->hasOne(FrequenceTrajets::class, 'idTrajet');
    }
    public function frequences()
    {
        return $this->hasMany(FrequenceTrajets::class, 'idTrajet');
    }
}
