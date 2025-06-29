<?php

namespace App\Models;
use App\Models\Voyages;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    //
    protected $fillable=[
        'numeroBus',
        'nombrePlaces',
         'nombrePlaceDispo',
        
        "idCompagnie",
        "status",
       // 'idVoyage', 
       
    ];
     public  function voyage(){
        return $this->belongsTo(Voyages::class,'idVoyage');
    }
    public  function compagnie(){
        return $this->belongsTo(Compagnies::class,'idCompagnie');
    }
}
