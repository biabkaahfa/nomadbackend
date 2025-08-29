<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class typeAbonement extends Model
{
    //
    protected $fillable=[
        'nom',
        'taux',
        'prix',
        'maxticket'
    ];
}
