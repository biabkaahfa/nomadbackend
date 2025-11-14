<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAbonement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'libelle',
        'prix_mensuel',
        'limite_notifications',
        'acces_notes',
        'commission_sur_place',
        'commission_en_ligne',
        'type_compagnie',
        'description',
        'est_actif',
    ];

    protected $casts = [
        'prix_mensuel' => 'decimal:2',
        'commission_sur_place' => 'decimal:2',
        'commission_en_ligne' => 'decimal:2',
        'acces_notes' => 'boolean',
        'est_actif' => 'boolean',
    ];

    // Relations
    public function abonnements()
    {
        return $this->hasMany(Abonement::class, 'idTypeAbonement');
    }

    // Scopes
    public function scopeForPrivee($query)
    {
        return $query->where('type_compagnie', 'privee');
    }

    public function scopeForPublique($query)
    {
        return $query->where('type_compagnie', 'publique');
    }

    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    // Méthodes utilitaires
    public function estFreemium(): bool
    {
        return $this->nom === 'freemium';
    }

    public function estStandard(): bool
    {
        return $this->nom === 'standard';
    }

    public function estPremium(): bool
    {
        return $this->nom === 'premium';
    }

    public function estPublic(): bool
    {
        return $this->nom === 'public';
    }

    public function notificationsIllimitees(): bool
    {
        return $this->limite_notifications === -1;
    }

    public function calculerCommissionSurPlace(int $nombreTickets = 1): float
    {
        return $this->commission_sur_place * $nombreTickets;
    }

    public function calculerCommissionEnLigne(float $montantTicket): float
    {
        // Puisque commission_en_ligne est maintenant un décimal (FCFA) et non un pourcentage
        // On retourne directement la valeur
        return $this->commission_en_ligne;
    }

    // Nouvelle méthode pour calculer le total des commissions pour plusieurs tickets en ligne
    public function calculerTotalCommissionEnLigne(int $nombreTickets = 1): float
    {
        return $this->commission_en_ligne * $nombreTickets;
    }

    // Méthode pour obtenir le libellé formaté du type de compagnie
    public function getTypeCompagnieFormattedAttribute(): string
    {
        return $this->type_compagnie === 'privee' ? 'Privée' : 'Publique';
    }

    // Méthode pour vérifier si l'abonnement est gratuit
    public function estGratuit(): bool
    {
        return $this->prix_mensuel == 0;
    }

    // Accessor pour le prix formaté
    public function getPrixMensuelFormattedAttribute(): string
    {
        return number_format($this->prix_mensuel, 0, ',', ' ') . ' FCFA';
    }

    // Accessor pour la commission sur place formatée
    public function getCommissionSurPlaceFormattedAttribute(): string
    {
        if ($this->commission_sur_place > 0) {
            return number_format($this->commission_sur_place, 0, ',', ' ') . ' FCFA';
        }
        return 'Gratuit';
    }

    // Accessor pour la commission en ligne formatée
    public function getCommissionEnLigneFormattedAttribute(): string
    {
        if ($this->commission_en_ligne > 0) {
            return number_format($this->commission_en_ligne, 0, ',', ' ') . ' FCFA';
        }
        return 'Gratuit';
    }

    // Méthode pour obtenir le statut formaté
    public function getStatutFormattedAttribute(): string
    {
        return $this->est_actif ? 'Actif' : 'Inactif';
    }

    // Méthode pour obtenir la limite de notifications formatée
    public function getLimiteNotificationsFormattedAttribute(): string
    {
        if ($this->notificationsIllimitees()) {
            return 'Illimité';
        }
        return $this->limite_notifications . '/jour';
    }
}
