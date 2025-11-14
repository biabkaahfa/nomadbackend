<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // ✅ AJOUT: Cast pour le JSON des tokens FCM
            'fcm_tokens' => 'array',
        ];
    }

    // ... vos méthodes JWT existantes ...

    /**
     * ✅ AJOUT: Ajouter un token FCM à l'utilisateur
     */
    public function addFcmToken(string $token): void
    {
        $tokens = $this->fcm_tokens ?? [];

        if (!in_array($token, $tokens)) {
            $tokens[] = $token;
            $this->update(['fcm_tokens' => $tokens]);
        }
    }

    /**
     * ✅ AJOUT: Supprimer un token FCM
     */
    public function removeFcmToken(string $token): void
    {
        $tokens = $this->fcm_tokens ?? [];
        $tokens = array_diff($tokens, [$token]);

        $this->update(['fcm_tokens' => array_values($tokens)]);
    }

    /**
     * ✅ AJOUT: Vérifier si l'utilisateur a des tokens FCM
     */
    public function hasFcmTokens(): bool
    {
        return !empty($this->fcm_tokens);
    }

    /**
     * ✅ AJOUT: Nettoyer les tokens invalides
     */
    public function cleanFcmTokens(array $validTokens): void
    {
        $currentTokens = $this->fcm_tokens ?? [];
        $cleanedTokens = array_intersect($currentTokens, $validTokens);

        $this->update(['fcm_tokens' => array_values($cleanedTokens)]);
    }

       /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relation avec le profil
     */
    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profils::class, 'idProfil');
    }
     public function isAdmin(): bool
    {
        return $this->profil->name === 'Admin général';
    }
     /**
     * Récupère le dernier abonnement actif de la compagnie de l'utilisateur
     */
    public function abonnementActif()
    {
        return $this->hasOne(Abonement::class, 'idCompagnie', 'idCompagnie')
            ->where('statut', 'actif')
            ->where('dateFin', '>=', now())
            ->latest('dateFin');
    }

    /**
     * Récupère tous les abonnements de la compagnie
     */
    public function abonnements()
    {
        return $this->hasMany(Abonement::class, 'idCompagnie', 'idCompagnie')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Vérifie si la compagnie de l'utilisateur a un abonnement actif
     */
    public function hasAbonnementActif(): bool
    {
        return $this->abonnementActif()->exists();
    }

    /**
     * Récupère le type d'abonnement actuel
     */
    public function typeAbonementActif()
    {
        return $this->abonnementActif()->with('typeAbonement');
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
     public  function profils(){
        return $this->belongsTo(Profils::class,'idprofils');
    }

    /**
     * Accessor pour obtenir l'URL de l'image
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-avatar.png');
    }



    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}

