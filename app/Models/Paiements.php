<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiements extends Model
{
    //
     //
    public $timestamps = true;
    protected $fillable=[
        'montant',
        'datePaiement',
        'moyenPaiement',
        'statut',
        'typeSource',
        'telephone',
       'idUtilisateur',
       'referenceTransaction'
    ];
     public  function user(){
        return $this->belongsTo(User::class,'idUtilisateur');
    }
    public function ticket()
{
    return $this->belongsTo(\App\Models\Ticket::class, 'idTicket');
}

}
