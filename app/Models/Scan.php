<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scan extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'idTicket',
        'idControleur',
        'dateScan',
        'estValide',
        'notes',
        'typeScan',
        'snapshotTicket',
    ];

    protected $casts = [
        'dateScan' => 'datetime',
        'estValide' => 'boolean',
        'snapshotTicket' => 'array',
    ];

    /**
     * Relation avec le ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idTicket');
    }

    /**
     * Relation avec le contrôleur
     */
    public function controleur()
    {
        return $this->belongsTo(User::class, 'idControleur');
    }

    /**
     * Scope pour les scans valides
     */
    public function scopeValide($query)
    {
        return $query->where('estValide', true);
    }

    /**
     * Scope pour les scans invalides
     */
    public function scopeInvalide($query)
    {
        return $query->where('estValide', false);
    }

    /**
     * Scope pour les scans d'aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('dateScan', today());
    }

    /**
     * Scope pour les scans de checkin
     */
    public function scopeCheckin($query)
    {
        return $query->where('typeScan', 'checkin');
    }

    /**
     * Scope pour les scans de checkout
     */
    public function scopeCheckout($query)
    {
        return $query->where('typeScan', 'checkout');
    }

    /**
     * Scope pour les scans d'un contrôleur spécifique
     */
    public function scopeParControleur($query, $controleurId)
    {
        return $query->where('idControleur', $controleurId);
    }

    /**
     * Scope pour les scans d'une période
     */
    public function scopePeriode($query, $debut, $fin)
    {
        return $query->whereBetween('dateScan', [$debut, $fin]);
    }

    /**
     * Accessor pour vérifier si le scan est récent (moins de 24h)
     */
    public function getEstRecentAttribute()
    {
        return $this->dateScan->gt(now()->subDay());
    }

    /**
     * Accessor pour le type de scan en français
     */
    public function getTypeScanLibelleAttribute()
    {
        return $this->typeScan === 'checkin' ? 'Entrée' : 'Sortie';
    }

    /**
     * Méthode pour marquer un scan comme invalide
     */
    public function marquerInvalide($raison = null)
    {
        $this->update([
            'estValide' => false,
            'notes' => $raison ?: 'Scan marqué comme invalide'
        ]);
    }

    /**
     * Méthode pour obtenir les statistiques d'un contrôleur
     */
    public static function statistiquesControleur($controleurId)
    {
        return [
            'totalScans' => self::parControleur($controleurId)->count(),
            'scansAujourdhui' => self::parControleur($controleurId)->aujourdhui()->count(),
            'scansValides' => self::parControleur($controleurId)->valide()->count(),
            'scansInvalides' => self::parControleur($controleurId)->invalide()->count(),
            'tauxReussite' => self::parControleur($controleurId)->count() > 0
                ? round((self::parControleur($controleurId)->valide()->count() / self::parControleur($controleurId)->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Méthode pour créer un nouveau scan
     */
    public static function creerScan($idTicket, $idControleur, $typeScan = 'checkin', $notes = null)
    {
        return self::create([
            'idTicket' => $idTicket,
            'idControleur' => $idControleur,
            'dateScan' => now(),
            'estValide' => true,
            'typeScan' => $typeScan,
            'notes' => $notes,
            'snapshotTicket' => self::prendreSnapshotTicket($idTicket)
        ]);
    }

    /**
     * Prendre un snapshot des données du ticket au moment du scan
     */
    private static function prendreSnapshotTicket($idTicket)
    {
        $ticket = Ticket::with(['voyage.trajet.compagnie', 'user'])->find($idTicket);

        if (!$ticket) {
            return null;
        }

        return [
            'passager' => $ticket->name,
            'telephone' => $ticket->telephone,
            'email' => $ticket->email,
            'compagnie' => $ticket->voyage->trajet->compagnie->name ?? 'Inconnue',
            'trajet' => $ticket->voyage->trajet->pointDepart . ' → ' . $ticket->voyage->trajet->pointArrive,
            'dateVoyage' => $ticket->voyage->dateDepart,
            'heureDepart' => $ticket->voyage->heuresDepart,
            'gare' => $ticket->idGarre,
            'statut' => $ticket->statut,
        ];
    }
}
