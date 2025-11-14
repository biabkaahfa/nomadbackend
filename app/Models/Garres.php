<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garres extends Model
{
    protected $fillable = [
        'name',
        'localisation',
        'ville',
        'idCompagnie'
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }

    public function trajets()
    {
        return $this->belongsToMany(Trajets::class, 'garre_trajets', 'idGarre', 'idTrajet');
    }

    // ✅ Méthode pour obtenir la latitude (UNE SEULE FOIS)
    public function getLatitudeAttribute()
    {
        if ($this->localisation) {
            $parts = explode(',', $this->localisation);
            return count($parts) === 2 ? floatval(trim($parts[0])) : null;
        }
        return null;
    }

    // ✅ Méthode pour obtenir la longitude (UNE SEULE FOIS)
    public function getLongitudeAttribute()
    {
        if ($this->localisation) {
            $parts = explode(',', $this->localisation);
            return count($parts) === 2 ? floatval(trim($parts[1])) : null;
        }
        return null;
    }

    // ✅ Vérifier si la gare a des coordonnées (UNE SEULE FOIS)
    public function hasCoordinates()
    {
        return $this->localisation &&
               $this->latitude !== null &&
               $this->longitude !== null;
    }

    // ✅ Formater les coordonnées pour l'affichage (UNE SEULE FOIS)
    public function getFormattedCoordinatesAttribute()
    {
        if ($this->hasCoordinates()) {
            return "Lat: {$this->latitude}, Lng: {$this->longitude}";
        }
        return 'Non définies';
    }
}
