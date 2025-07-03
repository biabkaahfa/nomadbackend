<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    //
    protected $fillable=[
        'dateReservation',
        'statut',
       'idUtilisateur',
       'name',
       'telephone',
       'email',
       'typeAchat',
       'modeReception',
        'idVoyage',
        'idGarre',
        'dateScan',
        'idPaiement',
         'namePersonneAPrevenir',
    'numeroPersonneAPrevenir',
    'emailPersonneAPrevenir',

    ];
     public  function user(){
        return $this->belongsTo(User::class,'idUtilisateur');
    }
    
     public  function voyage(){
        return $this->belongsTo(Voyages::class,'idVoyage');
    }

     public  function paiement(){
        return $this->belongsTo(Paiements::class,'idPaiement');
    }
}
