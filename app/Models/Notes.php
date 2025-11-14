<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notes extends Model
{
    use HasFactory;

    protected $fillable = [
        'idTicket',
        'securite',
        'confort',
        'ponctualite',
        'accueil',
        'proprete',
        'note_globale',
        'commentaire',
        'dateNote'
    ];

    protected $casts = [
        'dateNote' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idTicket');
    }

    // Calcul automatique de la note globale
    public function calculerNoteGlobale()
    {
        $notes = [
            $this->securite,
            $this->confort,
            $this->ponctualite,
            $this->accueil,
            $this->proprete
        ];

        // Filtrer les notes non nulles
        $notesFiltrees = array_filter($notes, function($note) {
            return !is_null($note);
        });

        if (count($notesFiltrees) > 0) {
            return round(array_sum($notesFiltrees) / count($notesFiltrees));
        }

        return null;
    }

    // Accessor pour la note globale
    public function getNoteGlobaleAttribute($value)
    {
        if (is_null($value)) {
            return $this->calculerNoteGlobale();
        }
        return $value;
    }

    // Validation des notes
    public static function validateNote($note)
    {
        return !is_null($note) && $note >= 1 && $note <= 5;
    }
}
