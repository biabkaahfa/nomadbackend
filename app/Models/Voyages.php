<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voyages extends Model
{
    //
    public $timestamps = true;

    protected $fillable=[
        'heuresDepart',
        'idTrajet',
        'dateDepart',
        'idBus'
    ];
     public  function trajet(){
        return $this->belongsTo(Trajets::class,'idTrajet');
    }
     public  function bus(){
        return $this->belongsTo(Bus::class,'idBus');
    }
    public function tickets()
{
    return $this->hasMany(Ticket::class, 'idVoyage');
}


}
