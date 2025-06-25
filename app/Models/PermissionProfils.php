<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionProfils extends Model
{
    //
    protected $fillable=[
        'idPermission',
        'idProfil'
    ];
     public  function permission(){
        return $this->belongsTo(Permissions::class,'idPermission');
    }
     public  function profil(){
        return $this->belongsTo(Profils::class,'idProfil');
    }
}
