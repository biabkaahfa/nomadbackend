<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Abonement extends Model
{
    protected $fillable = [
        'duree',
        'statut',
        'idTypeAbonement',
        'idCompagnie',
        'dateDebut',
        'dateFin'
    ];

    protected $dates = ['dateDebut', 'dateFin'];

    public function typeAbonement()
    {
        return $this->belongsTo(TypeAbonement::class, 'idTypeAbonement');
    }

    public function compagnie()
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }

    const DUREE_PAR_DEFAUT = 30;




    public function isActive()
    {
        return $this->dateFin->isFuture();
    }
    // Abonement.php

public function tickets()
{
    return $this->compagnie
        ? $this->compagnie->tickets()
        : collect();
}

public function ticketsCeMois()
{
    return $this->compagnie
        ? $this->compagnie->tickets()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->get()
        : collect();
}





    // Méthode pour renouveler un abonnement
    public function renew($duration = null)
    {
        $newAbonnement = $this->replicate();
        $newAbonnement->dateDebut = now();
        $newAbonnement->dateFin = now()->addDays($duration ?? $this->duree);
        $newAbonnement->save();

        return $newAbonnement;
    }
}
