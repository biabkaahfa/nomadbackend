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
    
}
