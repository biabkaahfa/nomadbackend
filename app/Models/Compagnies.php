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
        'description',
        'telephone',
        'email'
    ];
    public function imageUrl(): string
    {
        return Storage::url($this->logo);
    }
}
