<?php

namespace App\Http\Controllers\Notes;

use App\Models\Notes;
use App\Models\Ticket;
use App\Models\Trajets;
use App\Models\Voyages;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // CORRECTION : Changer cette ligne
use App\Http\Requests\StoreNotesRequest;
use App\Http\Requests\UpdateNotesRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateTicketRequest;

class NotesController extends Controller
{
    /**
     * Récupérer les notes de l'utilisateur mobile
     */
    public function getUserNotes()
    {
        try {
            $user = Auth::user();

            $notes = Notes::with(['ticket.voyage.trajet'])
                ->whereHas('ticket', function($query) use ($user) {
                    $query->where('idUtilisateur', $user->id);
                })
                ->orderBy('dateNote', 'desc')
                ->get()
                ->map(function($note) {
                    return [
                        'id' => $note->id,
                        'idTicket' => $note->idTicket,
                        'note_globale' => $note->note_globale,
                        'securite' => $note->securite,
                        'confort' => $note->confort,
                        'ponctualite' => $note->ponctualite,
                        'accueil' => $note->accueil,
                        'proprete' => $note->proprete,
                        'commentaire' => $note->commentaire,
                        'dateNote' => $note->dateNote,
                        'canEdit' => false,
                        'ticket' => [
                            'voyage' => [
                                'trajet' => [
                                    'pointDepart' => $note->ticket->voyage->trajet->pointDepart ?? 'N/A',
                                    'pointArrive' => $note->ticket->voyage->trajet->pointArrive ?? 'N/A',
                                ]
                            ]
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'notes' => $notes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des notes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle note (mobile)
     */
    public function createUserNote(Request $request) // CORRECTION : Maintenant ça utilise la bonne classe
    {
        try {
            $validator = Validator::make($request->all(), [
                'idTicket' => 'required|exists:tickets,id',
                'securite' => 'required|integer|min:1|max:5',
                'confort' => 'required|integer|min:1|max:5',
                'ponctualite' => 'required|integer|min:1|max:5',
                'accueil' => 'required|integer|min:1|max:5',
                'proprete' => 'required|integer|min:1|max:5',
                'commentaire' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Vérifier que le ticket appartient à l'utilisateur
            $ticket = Ticket::where('id', $request->idTicket)
                ->where('idUtilisateur', $user->id)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket non trouvé ou non autorisé'
                ], 404);
            }

            // Vérifier si une note existe déjà pour ce ticket
            $existingNote = Notes::where('idTicket', $request->idTicket)->first();
            if ($existingNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une note existe déjà pour ce ticket'
                ], 409);
            }

            // Calculer la note globale
            $noteGlobale = round((
                $request->securite +
                $request->confort +
                $request->ponctualite +
                $request->accueil +
                $request->proprete
            ) / 5);

            // Créer la note
            $note = Notes::create([
                'idTicket' => $request->idTicket,
                'securite' => $request->securite,
                'confort' => $request->confort,
                'ponctualite' => $request->ponctualite,
                'accueil' => $request->accueil,
                'proprete' => $request->proprete,
                'note_globale' => $noteGlobale,
                'commentaire' => $request->commentaire,
                'dateNote' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Note enregistrée avec succès',
                'note' => $note
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserRateableTickets()
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();

            $tickets = Ticket::with(['voyage.trajet'])
                ->where('idUtilisateur', $user->id)
                ->where('statut', 'UTILISE')
                ->whereHas('voyage', function($query) use ($now) {
                    $query->where('dateDepart', '<', $now->format('Y-m-d'))
                          ->orWhere(function($q) use ($now) {
                              $q->where('dateDepart', $now->format('Y-m-d'))
                                ->where('heuresDepart', '<', $now->format('H:i:s'));
                          });
                })
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('notes')
                          ->whereColumn('notes.idTicket', 'tickets.id');
                })
                ->get()
                ->map(function($ticket) {
                    return [
                        'id' => $ticket->id,
                        'statut' => $ticket->statut,
                        'voyage' => [
                            'dateDepart' => $ticket->voyage->dateDepart,
                            'heuresDepart' => $ticket->voyage->heuresDepart,
                            'trajet' => [
                                'pointDepart' => $ticket->voyage->trajet->pointDepart ?? 'N/A',
                                'pointArrive' => $ticket->voyage->trajet->pointArrive ?? 'N/A',
                            ]
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'tickets' => $tickets
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des tickets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer les dernières notes avec les nouvelles colonnes
        $latestNotes = $this->getLatestNotes($user);

        // Récupérer les statistiques avec la nouvelle structure
        $stats = $this->getStatistics($user);

        // Récupérer les compagnies (pour l'admin général)
        $compagnies = $this->getCompagnies($user);

        return view('back.notes.index', [
            'latestNotes' => $latestNotes,
            'stats' => $stats,
            'compagnies' => $compagnies,
            'user' => $user,
        ]);
    }

    /**
     * Récupérer les dernières notes
     */
    private function getLatestNotes($user)
    {
        $notesQuery = DB::table('notes as n')
            ->join('tickets as t', 'n.idTicket', '=', 't.id')
            ->join('voyages as v', 't.idVoyage', '=', 'v.id')
            ->join('trajets as tr', 'v.idTrajet', '=', 'tr.id')
            ->leftJoin('compagnies as c', 'tr.idCompagnie', '=', 'c.id');

        // Filtre par compagnie si pas admin général
        if ($user->profil?->name !== 'Admin général') {
            $notesQuery->where('tr.idCompagnie', $user->idCompagnie);
        }

        return $notesQuery
            ->select(
                'n.*',
                'n.note_globale as note', // Utiliser note_globale comme note principale
                'v.id as idVoyage',
                'v.dateDepart',
                'tr.pointDepart',
                'tr.pointArrive',
                'c.name as compagnie_nom',
                'tr.idCompagnie'
            )
            ->orderBy('n.dateNote', 'desc')
            ->get()
            ->map(function ($note) {
                // Calculer la note globale si elle n'existe pas
                if (empty($note->note) && (!empty($note->securite) || !empty($note->confort) || !empty($note->ponctualite))) {
                    $notesArray = [
                        $note->securite,
                        $note->confort,
                        $note->ponctualite,
                        $note->accueil,
                        $note->proprete
                    ];

                    $notesFiltrees = array_filter($notesArray, function($n) {
                        return !is_null($n);
                    });

                    $note->note = count($notesFiltrees) > 0 ? round(array_sum($notesFiltrees) / count($notesFiltrees)) : null;
                }

                $note->nomVoyage = $note->pointDepart . ' → ' . $note->pointArrive . ' le ' . Carbon::parse($note->dateDepart)->format('d/m/Y');
                $note->compagnie_nom = $note->compagnie_nom ?? 'Compagnie inconnue';

                return $note;
            })
            ->groupBy('idVoyage')
            ->map(function ($group) {
                return $group->take(5); // 5 dernières notes par voyage
            });
    }

    /**
     * Récupérer les statistiques
     */
    private function getStatistics($user)
    {
        $statsQuery = DB::table('notes as n')
            ->join('tickets as t', 'n.idTicket', '=', 't.id')
            ->join('voyages as v', 't.idVoyage', '=', 'v.id')
            ->join('trajets as tr', 'v.idTrajet', '=', 'tr.id')
            ->leftJoin('compagnies as c', 'tr.idCompagnie', '=', 'c.id');

        // Filtre par compagnie si pas admin général
        if ($user->profil?->name !== 'Admin général') {
            $statsQuery->where('tr.idCompagnie', $user->idCompagnie);
        }

        return $statsQuery
            ->select(
                DB::raw("CONCAT(tr.pointDepart, ' → ', tr.pointArrive) as trajet"),
                'c.name as compagnie_nom',
                'tr.idCompagnie',
                DB::raw('COUNT(n.id) as total_notes'),
                DB::raw('COALESCE(AVG(n.note_globale),
                    AVG(
                        (COALESCE(n.securite, 0) + COALESCE(n.confort, 0) + COALESCE(n.ponctualite, 0) +
                         COALESCE(n.accueil, 0) + COALESCE(n.proprete, 0)) /
                        NULLIF(
                            (CASE WHEN n.securite IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.confort IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.ponctualite IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.accueil IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.proprete IS NOT NULL THEN 1 ELSE 0 END), 0)
                    )
                ) as moyenne'),
                DB::raw('COALESCE(MAX(n.note_globale),
                    MAX(
                        (COALESCE(n.securite, 0) + COALESCE(n.confort, 0) + COALESCE(n.ponctualite, 0) +
                         COALESCE(n.accueil, 0) + COALESCE(n.proprete, 0)) /
                        NULLIF(
                            (CASE WHEN n.securite IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.confort IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.ponctualite IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.accueil IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.proprete IS NOT NULL THEN 1 ELSE 0 END), 0)
                    )
                ) as max_note'),
                DB::raw('COALESCE(MIN(n.note_globale),
                    MIN(
                        (COALESCE(n.securite, 0) + COALESCE(n.confort, 0) + COALESCE(n.ponctualite, 0) +
                         COALESCE(n.accueil, 0) + COALESCE(n.proprete, 0)) /
                        NULLIF(
                            (CASE WHEN n.securite IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.confort IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.ponctualite IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.accueil IS NOT NULL THEN 1 ELSE 0 END +
                             CASE WHEN n.proprete IS NOT NULL THEN 1 ELSE 0 END), 0)
                    )
                ) as min_note'),
                // Statistiques par critère
                DB::raw('AVG(n.securite) as moyenne_securite'),
                DB::raw('AVG(n.confort) as moyenne_confort'),
                DB::raw('AVG(n.ponctualite) as moyenne_ponctualite'),
                DB::raw('AVG(n.accueil) as moyenne_accueil'),
                DB::raw('AVG(n.proprete) as moyenne_proprete')
            )
            ->groupBy('tr.id', 'tr.pointDepart', 'tr.pointArrive', 'c.name', 'tr.idCompagnie')
            ->get()
            ->map(function ($stat) {
                // Formater les notes
                $stat->moyenne = $stat->moyenne ? number_format($stat->moyenne, 2) : 'N/A';
                $stat->max_note = $stat->max_note ? number_format($stat->max_note, 1) : 'N/A';
                $stat->min_note = $stat->min_note ? number_format($stat->min_note, 1) : 'N/A';

                // Formater les moyennes par critère
                $stat->moyenne_securite = $stat->moyenne_securite ? number_format($stat->moyenne_securite, 2) : 'N/A';
                $stat->moyenne_confort = $stat->moyenne_confort ? number_format($stat->moyenne_confort, 2) : 'N/A';
                $stat->moyenne_ponctualite = $stat->moyenne_ponctualite ? number_format($stat->moyenne_ponctualite, 2) : 'N/A';
                $stat->moyenne_accueil = $stat->moyenne_accueil ? number_format($stat->moyenne_accueil, 2) : 'N/A';
                $stat->moyenne_proprete = $stat->moyenne_proprete ? number_format($stat->moyenne_proprete, 2) : 'N/A';

                $stat->compagnie_nom = $stat->compagnie_nom ?? 'Compagnie inconnue';

                return $stat;
            });
    }

    /**
     * Récupérer la liste des compagnies (pour l'admin général)
     */
    private function getCompagnies($user)
    {
        if ($user->profil?->name === 'Admin général') {
            return DB::table('compagnies')
                ->select('id', 'name')
                ->get();
        }

        return collect();
    }

    /**
     * Afficher les détails d'un voyage spécifique
     */
    public function showVoyage($voyageId)
    {
        $user = Auth::user();

        $voyage = Voyages::with(['trajet.compagnie', 'tickets.notes'])
            ->findOrFail($voyageId);

        // Vérifier les permissions
        if ($user->profil?->name !== 'Admin général' && $voyage->trajet->idCompagnie !== $user->idCompagnie) {
            abort(403, 'Accès non autorisé');
        }

        $notes = Notes::whereHas('ticket', function($query) use ($voyageId) {
                $query->where('idVoyage', $voyageId);
            })
            ->with('ticket.user')
            ->orderBy('dateNote', 'desc')
            ->get();

        // Calculer les recommandations
        $recommandations = $this->generateRecommandations($notes);

        return view('back.notes.voyage-details', [
            'voyage' => $voyage,
            'notes' => $notes,
            'recommandations' => $recommandations,
        ]);
    }

    /**
     * Générer des recommandations basées sur les notes
     */
    private function generateRecommandations($notes)
    {
        if ($notes->isEmpty()) {
            return ['Aucune donnée suffisante pour générer des recommandations'];
        }

        $recommandations = [];

        // Analyser les critères les plus bas
        $moyennes = [
            'Sécurité' => $notes->avg('securite'),
            'Confort' => $notes->avg('confort'),
            'Ponctualité' => $notes->avg('ponctualite'),
            'Accueil' => $notes->avg('accueil'),
            'Propreté' => $notes->avg('proprete'),
        ];

        $moyennes = array_filter($moyennes); // Retirer les null

        if (!empty($moyennes)) {
            $criterePlusBas = array_search(min($moyennes), $moyennes);
            $notePlusBasse = min($moyennes);

            if ($notePlusBasse < 3) {
                $recommandations[] = "Priorité: Améliorer la {$criterePlusBas} (note: {$notePlusBasse}/5)";
            }

            // Recommandations spécifiques
            foreach ($moyennes as $critere => $moyenne) {
                if ($moyenne < 3.5) {
                    switch ($critere) {
                        case 'Ponctualité':
                            $recommandations[] = "• Optimiser les horaires de départ pour améliorer la ponctualité";
                            break;
                        case 'Confort':
                            $recommandations[] = "• Vérifier l'état des sièges et le confort des véhicules";
                            break;
                        case 'Propreté':
                            $recommandations[] = "• Renforcer le nettoyage régulier des véhicules";
                            break;
                        case 'Accueil':
                            $recommandations[] = "• Former le personnel à l'accueil client";
                            break;
                        case 'Sécurité':
                            $recommandations[] = "• Sensibiliser les chauffeurs à la sécurité routière";
                            break;
                    }
                }
            }
        }

        // Analyser les commentaires pour les tendances
        $commentaires = $notes->pluck('commentaire')->filter();
        if ($commentaires->isNotEmpty()) {
            $recommandations[] = "• Analyser les " . $commentaires->count() . " commentaires pour insights détaillés";
        }

        return $recommandations;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notes $notes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notes $notes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotesRequest $request, Notes $notes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notes $notes)
    {
        //
    }
}
