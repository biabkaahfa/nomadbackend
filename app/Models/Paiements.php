<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiements extends Model
{
    //
    protected $fillable=[
        'montant',
        'datePaiement',
        'moyenPaiement',
        'statut',
        'typeSource',
       'idUtilisateur',
       'referenceTransaction'
    ];
     public  function user(){
        return $this->belongsTo(User::class,'idUtilisateur');
    }
}
