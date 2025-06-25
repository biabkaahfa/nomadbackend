<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    //
    protected $fillable=[
        'idTicket',
        'note',
        'commentaire',
        'dateNote'
    ];
     public  function ticket(){
        return $this->belongsTo(Tickets::class,'idTicket');
    }
}
