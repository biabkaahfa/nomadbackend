<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Compagnies extends Model
{
    //
    protected $fillable=[
        'name',
        'logo',
        'type',
        'description',
        'telephone',
        'email'
    ];
    public function imageUrl(): string
    {
        return Storage::url($this->logo);
    }
    public function trajets()
{
    return $this->hasMany('App\Models\Trajets', 'idCompagnie');
}
public function garres()
{
    return $this->hasMany(\App\Models\Garres::class, 'idCompagnie');
}


}
