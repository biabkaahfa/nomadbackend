<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrequenceTrajets extends Model
{
    //
    protected $fillable =[
        'idTrajet',
        'heureDepart',
        'nombrePlaceMinimum',
        'jourSemaine'
    ];
     public  function trajet(){
        return $this->belongsTo(Trajets::class,'idTrajet');
    }
    // app/Models/FrequenceTrajets.php

public function getLibelleAttribute()
{
    return $this->jourSemaine . ' à ' . \Carbon\Carbon::parse($this->heureDepart)->format('H:i');
}

}
