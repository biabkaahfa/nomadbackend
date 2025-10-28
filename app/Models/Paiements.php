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
        // Correction : utiliser la bonne clé étrangère
        // Supposons que votre table tickets a une colonne 'idPaiement' ou 'paiement_id'
        return $this->belongsTo(Ticket::class, 'id', 'idPaiement');
        // OU si c'est l'inverse :
        // return $this->hasOne(Ticket::class, 'idPaiement', 'id');
    }

}
