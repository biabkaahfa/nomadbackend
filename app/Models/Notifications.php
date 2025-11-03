<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications'; // Spécifier explicitement le nom de la table

    protected $fillable = [
        'titre',
        'contenu',
        'DateEnvoie',
        'type',
        'isRead', // Ajout du champ isRead
        'idUtilisateur',
        'idVoyage',
    ];

    protected $casts = [
        'DateEnvoie' => 'date',
        'isRead' => 'boolean', // Caster en boolean
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'idUtilisateur');
    }

    /**
     * Relation avec le voyage
     */
    public function voyage()
    {
        return $this->belongsTo(Voyages::class, 'idVoyage');
    }

    /**
     * Relation avec le ticket (si elle existe dans votre base de données)
     * Note: Votre table notifications n'a pas de champ idTicket selon la migration
     * Si vous avez besoin de cette relation, vous devrez peut-être modifier la migration
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idTicket');
    }

    /**
     * Scope pour les notifications non lues
     */
    public function scopeUnread($query)
    {
        return $query->where('isRead', false);
    }

    /**
     * Scope pour les notifications lues
     */
    public function scopeRead($query)
    {
        return $query->where('isRead', true);
    }

    /**
     * Scope pour les notifications par utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('idUtilisateur', $userId);
    }

    /**
     * Scope pour les notifications récentes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Marquer la notification comme lue
     */
    public function markAsRead()
    {
        $this->update(['isRead' => true]);
    }

    /**
     * Marquer la notification comme non lue
     */
    public function markAsUnread()
    {
        $this->update(['isRead' => false]);
    }

    /**
     * Vérifier si la notification est lue
     */
    public function isUnread()
    {
        return !$this->isRead;
    }

    /**
     * Accessor pour formater la date d'envoi
     */
    public function getFormattedDateEnvoieAttribute()
    {
        return $this->DateEnvoie->format('d/m/Y');
    }

    /**
     * Accessor pour le temps écoulé depuis l'envoi
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Méthode statique pour créer une notification
     */
    public static function createNotification($data)
    {
        return static::create([
            'titre' => $data['titre'],
            'contenu' => $data['contenu'],
            'DateEnvoie' => $data['DateEnvoie'] ?? now(),
            'type' => $data['type'],
            'isRead' => $data['isRead'] ?? false,
            'idUtilisateur' => $data['idUtilisateur'],
            'idVoyage' => $data['idVoyage'],
        ]);
    }

    /**
     * Récupérer le nombre de notifications non lues pour un utilisateur
     */
    public static function getUnreadCountForUser($userId)
    {
        return static::forUser($userId)->unread()->count();
    }

    /**
     * Marquer toutes les notifications comme lues pour un utilisateur
     */
    public static function markAllAsReadForUser($userId)
    {
        return static::forUser($userId)->unread()->update(['isRead' => true]);
    }
}
