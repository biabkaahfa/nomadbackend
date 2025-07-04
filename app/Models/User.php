<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec le profil
     */
    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profils::class, 'idProfil');
    }

    /**
     * Relation avec la gare
     */
    public function garre(): BelongsTo
    {
        return $this->belongsTo(Garres::class, 'idGarre');
    }

    /**
     * Relation avec la compagnie
     */
    public function compagnie(): BelongsTo
    {
        return $this->belongsTo(Compagnies::class, 'idCompagnie');
    }

    /**
     * Relation avec les voyages (pour les voyageurs)
     */
    public function voyages(): HasMany
    {
        return $this->hasMany(Voyages::class, 'idVoyageur');
    }

    /**
     * Relation avec les réservations
     */
    // public function reservations(): HasMany
    // {
    //     return $this->hasMany(Reservation::class, 'idVoyageur');
    // }

    /**
     * Relation avec les tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'idVoyageur');
    }

    /**
     * Relation avec les paiements
     */
    // public function paiements(): HasMany
    // {
    //     return $this->hasMany(Paiement::class, 'idVoyageur');
    // }

    /**
     * Vérifier si l'utilisateur a un profil spécifique
     */
    public function hasProfile(string $profileName): bool
    {
        return $this->profil && $this->profil->libelle === $profileName;
    }

    /**
     * Vérifier si l'utilisateur est actif
     */
    public function isActive(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Scope pour filtrer les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope pour filtrer par profil
     */
    public function scopeWithProfile($query, string $profileName)
    {
        return $query->whereHas('profil', function ($q) use ($profileName) {
            $q->where('libelle', $profileName);
        });
    }

    /**
     * Accessor pour obtenir l'URL de l'image
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-avatar.png');
    }

    // Personnaliser l'email de reset
//     public function sendPasswordResetNotification($token)
//     {
//         $this->notify(new \App\Notifications\ResetPasswordNotification($token));
//     }
}