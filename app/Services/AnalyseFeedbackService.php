<?php

namespace App\Services;

use App\Models\Notes;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyseFeedbackService
{
    /**
     * Analyse complète des feedbacks avec toutes les statistiques
     */
    public function analyserTendances($limit = 100)
    {
        $notes = Notes::with(['ticket.voyage'])
            ->whereNotNull('commentaire')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $motsCles = $this->getMotsCles();

        return [
            'sentiment_general' => $this->analyserSentiment($notes, $motsCles),
            'themes_recurrents' => $this->extraireThemes($notes),
            'suggestions' => $this->extraireSuggestions($notes),
            'statistiques_notes' => $this->getStatistiquesNotes(),
            'correlations' => $this->analyserCorrelations(),
            'feedbacks_recents' => $this->getFeedbacksRecents(10)
        ];
    }

    /**
     * Définition des mots-clés pour l'analyse de sentiment
     */
    private function getMotsCles()
    {
        return [
            'positifs' => [
                'bon', 'excellent', 'super', 'parfait', 'agréable', 'confortable', 'ponctuel',
                'sécurisé', 'propre', 'rapide', 'efficace', 'professionnel', 'sympathique',
                'satisfait', 'content', 'heureux', 'recommandé', 'parfait', 'idéal', 'génial'
            ],
            'negatifs' => [
                'mauvais', 'nul', 'déçu', 'retard', 'sale', 'inconfortable', 'dangereux',
                'probleme', 'mauvaise', 'horrible', 'catastrophe', 'désagréable', 'insatisfait',
                'fâché', 'colère', 'déception', 'lent', 'bruyant', 'vieux', 'casse'
            ],
            'ameliorations' => [
                'améliorer', 'changer', 'problème', 'suggestion', 'recommander', 'idéal',
                'devrait', 'serait', 'pourrait', 'besoin', 'manque', 'ajouter', 'amélioration',
                'conseil', 'avis', 'idée'
            ]
        ];
    }

    /**
     * Analyse de sentiment basée sur les mots-clés
     */
    private function analyserSentiment(Collection $notes, array $motsCles)
    {
        $positif = 0;
        $negatif = 0;
        $neutre = 0;
        $details = [];

        foreach ($notes as $note) {
            $commentaire = strtolower($this->nettoyerTexte($note->commentaire));

            $scorePositif = $this->compterOccurrences($commentaire, $motsCles['positifs']);
            $scoreNegatif = $this->compterOccurrences($commentaire, $motsCles['negatifs']);

            $details[] = [
                'id_note' => $note->id,
                'commentaire' => $note->commentaire,
                'score_positif' => $scorePositif,
                'score_negatif' => $scoreNegatif,
                'sentiment' => $this->determinerSentiment($scorePositif, $scoreNegatif)
            ];

            if ($scorePositif > $scoreNegatif) {
                $positif++;
            } elseif ($scoreNegatif > $scorePositif) {
                $negatif++;
            } else {
                $neutre++;
            }
        }

        $total = $notes->count();

        return [
            'positif' => $positif,
            'negatif' => $negatif,
            'neutre' => $neutre,
            'total' => $total,
            'pourcentage_positif' => $total > 0 ? round(($positif / $total) * 100, 2) : 0,
            'pourcentage_negatif' => $total > 0 ? round(($negatif / $total) * 100, 2) : 0,
            'details' => $details
        ];
    }

    /**
     * Extraction des thèmes récurrents des commentaires
     */
    private function extraireThemes(Collection $notes)
    {
        $themes = [
            'ponctualite' => ['retard', 'heure', 'ponctuel', 'tard', 'attente', 'horaire'],
            'confort' => ['confortable', 'siège', 'assis', 'espace', 'inconfortable', 'serré'],
            'securite' => ['dangereux', 'sécurité', 'vitesse', 'frein', 'conduite', 'prudent'],
            'proprete' => ['propre', 'sale', 'nettoyage', 'poussière', 'odeur', 'immobile'],
            'personnel' => ['chauffeur', 'accueil', 'sympathique', 'impoli', 'professionnel', 'sourire'],
            'vehicule' => ['bus', 'voiture', 'véhicule', 'moderne', 'vieux', 'neuf', 'casse']
        ];

        $occurrences = [];

        foreach ($themes as $theme => $mots) {
            $occurrences[$theme] = 0;

            foreach ($notes as $note) {
                $commentaire = strtolower($this->nettoyerTexte($note->commentaire));
                $occurrences[$theme] += $this->compterOccurrences($commentaire, $mots);
            }
        }

        // Trier par occurrence décroissante
        arsort($occurrences);

        return [
            'themes_principaux' => array_slice($occurrences, 0, 5, true),
            'total_occurrences' => array_sum($occurrences)
        ];
    }

    /**
     * Extraction des suggestions d'amélioration
     */
    private function extraireSuggestions(Collection $notes, $limit = 20)
    {
        $motsAmelioration = $this->getMotsCles()['ameliorations'];

        $suggestions = $notes->filter(function($note) use ($motsAmelioration) {
            $commentaire = strtolower($this->nettoyerTexte($note->commentaire));
            return $this->compterOccurrences($commentaire, $motsAmelioration) > 0;
        })->map(function($note) {
            return [
                'id_note' => $note->id,
                'commentaire' => $note->commentaire,
                'note_globale' => $note->note_globale,
                'date_note' => $note->dateNote->format('d/m/Y'),
                'voyage' => $note->ticket->voyage->trajet ?? 'N/A'
            ];
        })->take($limit);

        return $suggestions->values();
    }

    /**
     * Statistiques détaillées des notes
     */
    public function getStatistiquesNotes()
    {
        $stats = Notes::select([
            DB::raw('AVG(securite) as moyenne_securite'),
            DB::raw('AVG(confort) as moyenne_confort'),
            DB::raw('AVG(ponctualite) as moyenne_ponctualite'),
            DB::raw('AVG(accueil) as moyenne_accueil'),
            DB::raw('AVG(proprete) as moyenne_proprete'),
            DB::raw('AVG(note_globale) as moyenne_globale'),
            DB::raw('COUNT(*) as total_notes'),
            DB::raw('COUNT(commentaire) as total_commentaires')
        ])->first();

        $repartition = Notes::select('note_globale', DB::raw('COUNT(*) as count'))
            ->groupBy('note_globale')
            ->orderBy('note_globale', 'desc')
            ->get()
            ->pluck('count', 'note_globale');

        return [
            'moyennes_par_critere' => [
                'securite' => round($stats->moyenne_securite ?? 0, 2),
                'confort' => round($stats->moyenne_confort ?? 0, 2),
                'ponctualite' => round($stats->moyenne_ponctualite ?? 0, 2),
                'accueil' => round($stats->moyenne_accueil ?? 0, 2),
                'proprete' => round($stats->moyenne_proprete ?? 0, 2),
                'globale' => round($stats->moyenne_globale ?? 0, 2)
            ],
            'totaux' => [
                'notes' => $stats->total_notes ?? 0,
                'commentaires' => $stats->total_commentaires ?? 0
            ],
            'repartition_notes' => $repartition
        ];
    }

    /**
     * Analyse des corrélations entre critères
     */
    private function analyserCorrelations()
    {
        // Cette analyse nécessite plus de données
        // Pour l'instant, retournons des statistiques basiques
        return [
            'critere_plus_note' => $this->getCriterePlusNote(),
            'critere_moins_note' => $this->getCritereMoinsNote(),
            'evolution_temporelle' => $this->getEvolutionTemporelle()
        ];
    }

    /**
     * Récupération des feedbacks récents
     */
    private function getFeedbacksRecents($limit = 10)
    {
        return Notes::with(['ticket.voyage'])
            ->whereNotNull('commentaire')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($note) {
                return [
                    'id' => $note->id,
                    'commentaire' => $note->commentaire,
                    'note_globale' => $note->note_globale,
                    'date' => $note->dateNote->format('d/m/Y H:i'),
                    'voyage' => $note->ticket->voyage->trajet ?? 'N/A',
                    'sentiment' => $this->analyserSentimentCommentaire($note->commentaire)
                ];
            });
    }

    /**
     * Méthodes utilitaires
     */
    private function nettoyerTexte($texte)
    {
        // Supprimer la ponctuation et normaliser le texte
        return preg_replace('/[^\w\s]/u', ' ', $texte);
    }

    private function compterOccurrences($texte, $mots)
    {
        $count = 0;
        foreach ($mots as $mot) {
            $count += substr_count($texte, $mot);
        }
        return $count;
    }

    private function determinerSentiment($positif, $negatif)
    {
        if ($positif > $negatif) return 'positif';
        if ($negatif > $positif) return 'negatif';
        return 'neutre';
    }

    private function analyserSentimentCommentaire($commentaire)
    {
        $motsCles = $this->getMotsCles();
        $texte = strtolower($this->nettoyerTexte($commentaire));

        $scorePositif = $this->compterOccurrences($texte, $motsCles['positifs']);
        $scoreNegatif = $this->compterOccurrences($texte, $motsCles['negatifs']);

        return $this->determinerSentiment($scorePositif, $scoreNegatif);
    }

    private function getCriterePlusNote()
    {
        $stats = Notes::select([
            DB::raw('AVG(securite) as sec'),
            DB::raw('AVG(confort) as conf'),
            DB::raw('AVG(ponctualite) as ponct'),
            DB::raw('AVG(accueil) as acc'),
            DB::raw('AVG(proprete) as prop')
        ])->first();

        $moyennes = [
            'securite' => $stats->sec ?? 0,
            'confort' => $stats->conf ?? 0,
            'ponctualite' => $stats->ponct ?? 0,
            'accueil' => $stats->acc ?? 0,
            'proprete' => $stats->prop ?? 0
        ];

        return array_search(max($moyennes), $moyennes);
    }

    private function getCritereMoinsNote()
    {
        // Similaire à getCriterePlusNote mais avec min()
        $stats = Notes::select([
            DB::raw('AVG(securite) as sec'),
            DB::raw('AVG(confort) as conf'),
            DB::raw('AVG(ponctualite) as ponct'),
            DB::raw('AVG(accueil) as acc'),
            DB::raw('AVG(proprete) as prop')
        ])->first();

        $moyennes = [
            'securite' => $stats->sec ?? 0,
            'confort' => $stats->conf ?? 0,
            'ponctualite' => $stats->ponct ?? 0,
            'accueil' => $stats->acc ?? 0,
            'proprete' => $stats->prop ?? 0
        ];

        return array_search(min($moyennes), $moyennes);
    }

    private function getEvolutionTemporelle()
    {
        // Évolution des notes sur les 30 derniers jours
        return Notes::select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('AVG(note_globale) as moyenne_jour'),
            DB::raw('COUNT(*) as nb_notes')
        ])
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();
    }

    /**
     * Méthode pour générer un rapport complet
     */
    public function genererRapportComplet()
    {
        return [
            'date_generation' => now()->format('d/m/Y H:i'),
            'periode_analyse' => '30 derniers jours',
            'analyse_sentiment' => $this->analyserTendances(),
            'points_forts' => $this->identifierPointsForts(),
            'points_amelioration' => $this->identifierPointsAmelioration(),
            'recommandations' => $this->genererRecommandations()
        ];
    }

    private function identifierPointsForts()
    {
        $stats = $this->getStatistiquesNotes();
        $moyennes = $stats['moyennes_par_critere'];

        $pointsForts = [];
        foreach ($moyennes as $critere => $moyenne) {
            if ($moyenne >= 4.0) {
                $pointsForts[] = $critere;
            }
        }

        return $pointsForts;
    }

    private function identifierPointsAmelioration()
    {
        $stats = $this->getStatistiquesNotes();
        $moyennes = $stats['moyennes_par_critere'];

        $pointsAmelioration = [];
        foreach ($moyennes as $critere => $moyenne) {
            if ($moyenne < 3.0) {
                $pointsAmelioration[] = [
                    'critere' => $critere,
                    'moyenne' => $moyenne,
                    'priorite' => 'haute'
                ];
            } elseif ($moyenne < 3.5) {
                $pointsAmelioration[] = [
                    'critere' => $critere,
                    'moyenne' => $moyenne,
                    'priorite' => 'moyenne'
                ];
            }
        }

        return $pointsAmelioration;
    }

    private function genererRecommandations()
    {
        $pointsAmelioration = $this->identifierPointsAmelioration();
        $recommandations = [];

        foreach ($pointsAmelioration as $point) {
            switch ($point['critere']) {
                case 'ponctualite':
                    $recommandations[] = "Améliorer la ponctualité des départs et arrivées";
                    break;
                case 'confort':
                    $recommandations[] = "Investir dans des sièges plus confortables";
                    break;
                case 'proprete':
                    $recommandations[] = "Renforcer le nettoyage régulier des véhicules";
                    break;
                case 'accueil':
                    $recommandations[] = "Former le personnel à l'accueil client";
                    break;
                case 'securite':
                    $recommandations[] = "Sensibiliser à la sécurité routière";
                    break;
            }
        }

        return array_slice($recommandations, 0, 5);
    }
}
