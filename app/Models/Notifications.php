<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'titre',
        'contenu',
        'DateEnvoie',
        'type',
        'isRead',
        'idUtilisateur',
        'idVoyage',
        // ✅ NOUVEAUX CHAMPS
        'email_sent',
        'sms_sent',
        'push_sent',
        'email_count',
        'sms_count',
        'push_count',
    ];

    protected $casts = [
        'DateEnvoie' => 'date',
        'isRead' => 'boolean',
        // ✅ NOUVEAUX CASTS
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean',
        'push_sent' => 'boolean',
        'email_count' => 'integer',
        'sms_count' => 'integer',
        'push_count' => 'integer',
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

    // ✅ NOUVEAUX SCOPES POUR LES MODES D'ENVOI
    public function scopeWithEmail($query)
    {
        return $query->where('email_sent', true);
    }

    public function scopeWithSms($query)
    {
        return $query->where('sms_sent', true);
    }

    public function scopeWithPush($query)
    {
        return $query->where('push_sent', true);
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

    // ✅ NOUVELLES MÉTHODES POUR LES STATISTIQUES
    public function getTotalSentAttribute()
    {
        return $this->email_count + $this->sms_count + $this->push_count;
    }

    public function getModesUtilisesAttribute()
    {
        $modes = [];
        if ($this->email_sent) $modes[] = 'Email';
        if ($this->sms_sent) $modes[] = 'SMS';
        if ($this->push_sent) $modes[] = 'Push';
        return $modes;
    }

    public function getModesUtilisesStringAttribute()
    {
        return implode(' + ', $this->modes_utilises);
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
     * Méthode statique pour créer une notification avec statistiques
     */
    public static function createNotification($data, $modesEnvoi = [], $results = [])
    {
        return static::create([
            'titre' => $data['titre'],
            'contenu' => $data['contenu'],
            'DateEnvoie' => $data['DateEnvoie'] ?? now(),
            'type' => $data['type'],
            'isRead' => $data['isRead'] ?? false,
            'idUtilisateur' => $data['idUtilisateur'],
            'idVoyage' => $data['idVoyage'],
            // ✅ STATISTIQUES D'ENVOI
            'email_sent' => in_array('email', $modesEnvoi),
            'sms_sent' => in_array('sms', $modesEnvoi),
            'push_sent' => in_array('push', $modesEnvoi),
            'email_count' => $results['email'] ?? 0,
            'sms_count' => $results['sms'] ?? 0,
            'push_count' => $results['push'] ?? 0,
        ]);
    }

    /**
     * Mettre à jour les statistiques d'envoi
     */
    public function updateSendStats($modesEnvoi, $results)
    {
        $this->update([
            'email_sent' => in_array('email', $modesEnvoi),
            'sms_sent' => in_array('sms', $modesEnvoi),
            'push_sent' => in_array('push', $modesEnvoi),
            'email_count' => $results['email'] ?? 0,
            'sms_count' => $results['sms'] ?? 0,
            'push_count' => $results['push'] ?? 0,
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
