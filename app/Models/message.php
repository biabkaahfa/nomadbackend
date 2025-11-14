<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'idAbonement',
        'type',
        'contenu',
        'estVu',
        'dateEnvoi', // Ajouté car utilisé dans la vue
        'dateLecture'
    ];

    /**
     * Les attributs qui doivent être convertis en dates.
     *
     * @var array
     */
    protected $dates = [
        'dateEnvoi',
        'dateLecture',
        'created_at',
        'updated_at'
    ];

    /**
     * Relation avec l'abonement associé
     */
    public function abonement(): BelongsTo
    {
        return $this->belongsTo(Abonement::class, 'idAbonement');
    }

    /**
     * Relation avec les tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'idAbonement');
    }

    /**
     * Tickets du mois courant
     */
    public function ticketsCeMois()
    {
        return $this->tickets()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Marquer le message comme lu
     */
    public function marquerCommeLu(): bool
    {
        return $this->update([
            'estVu' => true,
            'dateLecture' => now()
        ]);
    }

    /**
     * Scope pour les messages non lus
     */
    public function scopeNonLus($query)
    {
        return $query->where('estVu', false);
    }

    /**
     * Scope pour les messages par type
     */
    public function scopeDeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
