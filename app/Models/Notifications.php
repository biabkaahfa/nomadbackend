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
        'idUtilisateur'
    ];
     public  function user(){
        return $this->belongsTo(User::class,'idUtilisateur');
    }
}
