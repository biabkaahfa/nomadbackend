<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trajets extends Model
{
    //
    protected $fillable=[
        'pointDepart',
        'pointArrive',
        "prix",
        "status",
        "idCompagnie"
    ];
     public  function compagnie(){
        return $this->belongsTo(Compagnies::class,'idCompagnie');
    }
    public function garres()
{
    return $this->belongsToMany(Garres::class, 'garre_trajets', 'idTrajet', 'idGarre');
}
// public function frequences() {
//     return $this->hasMany(FrequenceTrajet::class, 'idTrajet');
// }
public function frequences()
{
    return $this->hasMany(FrequenceTrajets::class, 'idTrajet'); // ✅ Bon nom
}
public function garresDepart()
{
    return $this->belongsTo(\App\Models\Garres::class, 'idGareDepart');
}

public function garresArrivee()
{
    return $this->belongsTo(\App\Models\Garres::class, 'idGareArrivee');
}


    
}
