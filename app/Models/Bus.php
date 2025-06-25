<?php

namespace App\Models;
use App\Models\Voyages;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    //
    protected $fillable=[
        'nombrePlaces',
       // 'idVoyage', 
        'nombrePlaceDispo'
    ];
     public  function voyage(){
        return $this->belongsTo(Voyages::class,'idVoyage');
    }
}
