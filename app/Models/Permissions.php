<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    //
    protected $fillable=[
        'name'
    ];
    public function profils()
{
    return $this->belongsToMany(Profils::class, 'permission_profils', 'idPermission', 'idProfil');
}

}
