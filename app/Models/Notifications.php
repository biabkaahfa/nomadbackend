<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    //
    protected $fillable=[
        'titre',
        'contenu',
        'DateEnvoie',
        'type',
        'idUtilisateur',
        'idVoyage',
        
    
    ];
     public  function user(){
        return $this->belongsTo(User::class,'idUtilisateur');
    }
      public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idTicket');
    }
    public function voyage()
{
    return $this->belongsTo(Voyages::class, 'idVoyage');
}

}
