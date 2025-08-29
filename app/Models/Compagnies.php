<?php

namespace App\Models;
use Carbon\Carbon;
use App\Models\AbonementPublic;
use App\Models\PersonalisationCard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compagnies extends Model
{
    //
    protected $fillable=[
        'name',
        'idAbonement',
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


public function abonementActuel()
{
    return $this->hasOne(Abonement::class, 'idCompagnie')
        // ->where('actif', true)
        ->whereDate('dateFin', '>', Carbon::now());
}

 public function abonementsPublics(): HasMany
    {
        return $this->hasMany(AbonementPublic::class, 'idCompagnie');
    }

    /**
     * Obtenir les cartes de personnalisation pour la compagnie.
     */
    public function personalisationCards(): HasMany
    {
        return $this->hasMany(PersonalisationCard::class, 'idCompagnie');
    }

public function garres()
{
    return $this->hasMany(\App\Models\Garres::class, 'idCompagnie');
}
// public function tickets()
// {
//     return $this->hasMany(Ticket::class);
// }

public function tickets()
    {
        // Au lieu de hasMany direct, nous allons filtrer les tickets
        // qui appartiennent à un voyage, dont le trajet appartient à cette compagnie.
        return Ticket::whereHas('voyage.trajet', function ($query) {
            $query->where('idCompagnie', $this->id);
        });
    }

public function buses()
{
    return $this->hasMany(Bus::class, 'idCompagnie');
}

}
