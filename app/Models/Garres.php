<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garres extends Model
{
    //
    protected $fillable=[
        'name',
        'localisation',
        'ville',
        'idCompagnie'
    ];
  public  function compagnie(){
        return $this->belongsTo(Compagnies::class,'idCompagnie');
    }
    public function trajets()
{
    return $this->belongsToMany(Trajets::class, 'garre_trajets', 'idGarre', 'idTrajet');
}

}
