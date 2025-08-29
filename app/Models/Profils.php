<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profils extends Model
{
    //
    protected $fillable=[
        'name',
        'description'
    ];
     public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'permission_profils', 'idProfil', 'idPermission');
    }
     public  function profil(){
        return $this->belongsTo(User::class,'idprofils');
    }
}
