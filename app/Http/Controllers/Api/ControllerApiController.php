<?php

namespace App\Http\Controllers\Api;

use Log;
use Schema;
use App\Models\Scan;
use App\Models\Garre;
use App\Models\Garres;
use App\Models\Ticket;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ControllerApiController extends Controller
{
    /**
     * Récupérer le profil du contrôleur connecté
     */
    public function getProfile(Request $request)
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = JWTAuth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Charger les relations nécessaires
            $user->load(['profil', 'compagnie', 'garre']);

            // Vérifier que l'utilisateur est bien un contrôleur
            if (!$user->profil || $user->profil->name !== 'Contrôleur') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Accès réservé aux contrôleurs'
                ], 403);
            }

            // Vérifier que le contrôleur est actif
            if ($user->statut !== 'actif') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Votre compte contrôleur est inactif'
                ], 403);
            }

            // Structure des données de réponse
            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'statut' => $user->statut,
                'role' => $user->profil->name,
                'image' => $user->image_url,
                'compagnie' => $user->compagnie ? [
                    'id' => $user->compagnie->id,
                    'name' => $user->compagnie->name,
                    'type' => $user->compagnie->type,
                    'logo' => $user->compagnie->logo,
                ] : null,
                'gare' => $user->garre ? [
                    'id' => $user->garre->id,
                    'name' => $user->garre->name,
                    'ville' => $user->garre->ville,
                    'localisation' => $user->garre->localisation,
                ] : null,
                'created_at' => $user->created_at,
                'member_since' => $user->created_at->format('d/m/Y'),
            ];

            // Statistiques du contrôleur
            $stats = $this->getControllerStats($user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Profil contrôleur récupéré avec succès',
                'data' => $profileData,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération du profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques du contrôleur
     */
    public function getControllerStats($userId)
    {
        try {
            // Vérifier si la table scans existe et a des données
            if (!Schema::hasTable('scans')) {
                return $this->getDefaultStats();
            }

            return [
                'total_scans' => Scan::where('idControleur', $userId)->count(),
                'scans_today' => Scan::where('idControleur', $userId)
                                    ->whereDate('dateScan', today())
                                    ->count(),
                'scans_this_week' => Scan::where('idControleur', $userId)
                                        ->whereBetween('dateScan', [now()->startOfWeek(), now()->endOfWeek()])
                                        ->count(),
                'scans_this_month' => Scan::where('idControleur', $userId)
                                         ->whereMonth('dateScan', now()->month)
                                         ->whereYear('dateScan', now()->year)
                                         ->count(),
                'valid_scans' => Scan::where('idControleur', $userId)
                                    ->where('estValide', true)
                                    ->count(),
                'invalid_scans' => Scan::where('idControleur', $userId)
                                     ->where('estValide', false)
                                     ->count(),
                'efficiency_rate' => $this->calculateEfficiencyRate($userId),
            ];
        } catch (\Exception $e) {
            Log::error("Erreur dans getControllerStats: " . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    /**
     * Calculer le taux d'efficacité
     */
    private function calculateEfficiencyRate($userId)
    {
        try {
            $totalScans = Scan::where('idControleur', $userId)->count();
            $validScans = Scan::where('idControleur', $userId)->where('estValide', true)->count();

            if ($totalScans > 0) {
                return round(($validScans / $totalScans) * 100, 2);
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Statistiques par défaut en cas d'erreur
     */
    private function getDefaultStats()
    {
        return [
            'total_scans' => 0,
            'scans_today' => 0,
            'scans_this_week' => 0,
            'scans_this_month' => 0,
            'valid_scans' => 0,
            'invalid_scans' => 0,
            'efficiency_rate' => 0,
        ];
    }

    /**
     * Mettre à jour le profil du contrôleur
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = JWTAuth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Validation des données
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'telephone' => 'sometimes|string|max:12',
                'image' => 'sometimes|string',
            ]);

            // Mettre à jour les champs autorisés
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }

            if (isset($validated['telephone'])) {
                $user->telephone = $validated['telephone'];
            }

            if (isset($validated['image'])) {
                $user->image = $validated['image'];
            }

            $user->save();

            // Recharger les relations
            $user->load(['profil', 'compagnie', 'garre']);

            return response()->json([
                'status' => 'success',
                'message' => 'Profil mis à jour avec succès',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telephone' => $user->telephone,
                    'image' => $user->image_url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Changer le mot de passe du contrôleur
     */
    public function changePassword(Request $request)
    {
        try {
            $user = JWTAuth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            // Vérifier l'ancien mot de passe
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mot de passe actuel incorrect'
                ], 422);
            }

            // Mettre à jour le mot de passe
            $user->password = Hash::make($validated['new_password']);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Mot de passe modifié avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du changement de mot de passe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les scans récents du contrôleur
     */
    /**
 * Récupérer les scans récents du contrôleur
 */
public function getRecentScans(Request $request)
{
    try {
        $user = JWTAuth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $limit = min(max($limit, 1), 50);
        $page = max($page, 1);

        // CORRECTION : Supprimer la relation 'gare' qui n'existe pas dans scans
        $scans = Scan::with(['ticket', 'ticket.voyage'])
            ->where('idControleur', $user->id)
            ->orderBy('dateScan', 'DESC')
            ->paginate($limit, ['*'], 'page', $page);

        $formattedScans = $scans->map(function ($scan) {
            // CORRECTION : Extraire la raison du snapshotTicket
            $snapshot = $scan->snapshotTicket ? json_decode($scan->snapshotTicket, true) : [];
            $validationReason = $snapshot['validation_reason'] ?? null;

            return [
                'id' => $scan->id,
                'ticket_number' => $scan->ticket->id,
                'passenger_name' => $scan->ticket->name ?? 'Passager inconnu',
                'scan_time' => $scan->dateScan,
                'is_valid' => (bool) $scan->estValide,
                'invalid_reason' => !$scan->estValide ? $validationReason : null, // CORRECTION ICI
                'gare_scan' => $scan->ticket->gare->name ?? 'N/A', // Récupérer depuis le ticket
                'scan_location' => $scan->scan_latitude ? [
                    'latitude' => $scan->scan_latitude,
                    'longitude' => $scan->scan_longitude
                ] : null,
                'ticket_details' => $scan->ticket->voyage ? [
                    'trajet' => $scan->ticket->voyage->trajet_complet ?? 'N/A',
                    'date_voyage' => $scan->ticket->voyage->date_depart ?? 'N/A',
                ] : null
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Scans récents récupérés avec succès',
            'data' => $formattedScans,
            'meta' => [
                'current_page' => $scans->currentPage(),
                'last_page' => $scans->lastPage(),
                'per_page' => $scans->perPage(),
                'total' => $scans->total(),
            ]
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur récupération scans récents: " . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la récupération des scans récents'
        ], 500);
    }
}

    /**
     * Récupérer les statistiques de scans
     */
  /**
 * Récupérer les statistiques de scans
 */
public function getScanStats()
{
    try {
        $user = JWTAuth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        // CORRECTION : Utiliser directement les calculs sans appeler getControllerStats
        $today = now()->format('Y-m-d');
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');

        $stats = [
            'scans_today' => Scan::where('idControleur', $user->id)
                                ->whereDate('dateScan', $today)
                                ->count(),

            'total_scans' => Scan::where('idControleur', $user->id)->count(),

            'scans_this_week' => Scan::where('idControleur', $user->id)
                                   ->whereDate('dateScan', '>=', $weekStart)
                                   ->count(),

            'scans_this_month' => Scan::where('idControleur', $user->id)
                                    ->whereDate('dateScan', '>=', $monthStart)
                                    ->count(),

            'valid_scans' => Scan::where('idControleur', $user->id)
                               ->where('estValide', true)
                               ->count(),

            'invalid_scans' => Scan::where('idControleur', $user->id)
                                 ->where('estValide', false)
                                 ->count(),

            'incidents' => Scan::where('idControleur', $user->id)
                             ->where('estValide', false)
                             ->count(),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Statistiques récupérées avec succès',
            'data' => $stats
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur récupération stats: " . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la récupération des statistiques'
        ], 500);
    }
}

    /**
     * Scanner un ticket avec l'ID du ticket
     */
  public function scanTicket(Request $request)
{
    DB::beginTransaction();

    try {
        $user = JWTAuth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        // Validation des données
        $validated = $request->validate([
            'ticket_id' => 'required|integer|exists:tickets,id',
            'scan_latitude' => 'sometimes|numeric',
            'scan_longitude' => 'sometimes|numeric',
        ]);

        // Charger les relations
        $ticket = Ticket::with([
            'voyage',
            'voyage.trajet',
            'voyage.compagnie'
        ])->find($validated['ticket_id']);

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket non trouvé',
                'data' => [
                    'is_valid' => false,
                    'reason' => 'ID ticket invalide'
                ]
            ], 404);
        }

        // Vérifier si le ticket a déjà été scanné (dateScan remplie)
        if ($ticket->dateScan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket déjà scanné',
                'data' => [
                    'is_valid' => false,
                    'reason' => 'Ticket déjà utilisé',
                    'previous_scan' => [
                        'scanned_at' => $ticket->dateScan,
                        'gare_scan' => $ticket->gare->name ?? 'Inconnue'
                    ]
                ]
            ], 409);
        }

        // Vérifications complètes du ticket
        $validationResult = $this->validateTicketForController($ticket, $user);

        // 1. METTRE À JOUR LE TICKET
        $ticket->dateScan = now();
        $ticket->idGarre = $user->idGarre; // La gare du contrôleur
        $ticket->statut = 'UTILISE'; // Changer le statut
        $ticket->save();

        // 2. CRÉER L'HISTORIQUE DANS SCANS
        $scan = new Scan();
        $scan->idTicket = $ticket->id;
        $scan->idControleur = $user->id;
        $scan->dateScan = now();
        $scan->estValide = $validationResult['is_valid'];
        $scan->typeScan = 'checkin';

        // Sauvegarder un snapshot du ticket avec la raison
        $scan->snapshotTicket = json_encode([
            'passenger_name' => $ticket->name,
            'passenger_phone' => $ticket->telephone,
            'voyage_id' => $ticket->idVoyage,
            'trajet' => $ticket->voyage->trajet_complet ?? 'N/A',
            'compagnie' => $ticket->voyage->compagnie->name ?? 'N/A',
            'scan_time' => now()->toDateTimeString(),
            'validation_reason' => $validationResult['reason'] ?? 'Ticket valide', // Stocker la raison ici
            'is_valid' => $validationResult['is_valid']
        ]);

        if (isset($validated['scan_latitude']) && isset($validated['scan_longitude'])) {
            $scan->scan_latitude = $validated['scan_latitude'];
            $scan->scan_longitude = $validated['scan_longitude'];
        }

        $scan->save();

        DB::commit();

        // Préparer la réponse
        $responseData = [
            'is_valid' => $validationResult['is_valid'],
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->id,
            'passenger_name' => $ticket->name ?? 'Non spécifié',
            'scan_time' => $ticket->dateScan,
            'controller_id' => $user->id,
            'controller_name' => $user->name,
            'gare_id' => $ticket->idGarre,
            'gare_name' => $user->garre->name ?? 'Inconnue',
            'new_status' => $ticket->statut,
            'reason' => $validationResult['reason'] ?? 'Ticket valide'
        ];

        if (!$validationResult['is_valid']) {
            $responseData['invalid_reason'] = $validationResult['reason'];
        }

        return response()->json([
            'status' => 'success',
            'message' => $validationResult['is_valid'] ? 'Ticket scanné avec succès' : 'Ticket invalide',
            'data' => $responseData
        ]);

    } catch (ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Données de scan invalides',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur lors du scan: " . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors du scan du ticket: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Validation complète du ticket pour le contrôleur
 */
private function validateTicketForController(Ticket $ticket, $user)
{
    // 1. Vérifier si le ticket a déjà été scanné
    if ($ticket->dateScan) {
        return [
            'is_valid' => false,
            'reason' => 'Ticket déjà scanné'
        ];
    }

    // 2. Vérifier le statut du ticket
    if ($ticket->statut !== 'CONFIRME') {
        return [
            'is_valid' => false,
            'reason' => 'Ticket ' . strtolower($ticket->statut)
        ];
    }

    // 3. Vérifier que le ticket appartient à la même compagnie
    if ($ticket->voyage && $ticket->voyage->compagnie) {
        if ($user->idCompagnie !== $ticket->voyage->compagnie->id) {
            return [
                'is_valid' => false,
                'reason' => 'Ticket non valide pour votre compagnie'
            ];
        }
    } else {
        return [
            'is_valid' => false,
            'reason' => 'Informations du voyage manquantes'
        ];
    }

    // 4. Vérifier que la gare du contrôleur ravitaille le trajet
    $gareController = Garres::find($user->idGarre);

    if (!$gareController) {
        return [
            'is_valid' => false,
            'reason' => 'Gare du contrôleur non trouvée'
        ];
    }

    // Vérifier si la gare ravitaille le trajet désiré
    $isValidGare = $this->checkGareRavitailleTrajet($gareController, $ticket->voyage);

    if (!$isValidGare) {
        return [
            'is_valid' => false,
            'reason' => 'Votre gare ne ravitaille pas ce trajet'
        ];
    }

    // 5. Vérifier la date du voyage
    if ($ticket->voyage->date_depart) {
        $voyageDate = \Carbon\Carbon::parse($ticket->voyage->date_depart);
        if (!now()->isSameDay($voyageDate)) {
            return [
                'is_valid' => false,
                'reason' => 'Ticket non valide pour aujourd\'hui'
            ];
        }
    }

    return [
        'is_valid' => true,
        'reason' => 'Ticket valide'
    ];
}

/**
 * Vérifier si la gare ravitaille le trajet désiré
 */
private function checkGareRavitailleTrajet($gareController, $voyage)
{
    // Si le voyage a un trajet avec des points définis
    if ($voyage->trajet) {
        $trajet = $voyage->trajet;

        // Vérifier si la gare correspond au point de départ
        if ($trajet->pointDepart && $gareController->ville) {
            if (str_contains(strtolower($trajet->pointDepart), strtolower($gareController->ville))) {
                return true;
            }
        }

        // Vérifier si la gare correspond au point d'arrivée
        if ($trajet->pointArrive && $gareController->ville) {
            if (str_contains(strtolower($trajet->pointArrive), strtolower($gareController->ville))) {
                return true;
            }
        }

        // Vérifier par le nom de la gare
        if ($trajet->pointDepart && $gareController->name) {
            if (str_contains(strtolower($trajet->pointDepart), strtolower($gareController->name))) {
                return true;
            }
        }

        if ($trajet->pointArrive && $gareController->name) {
            if (str_contains(strtolower($trajet->pointArrive), strtolower($gareController->name))) {
                return true;
            }
        }
    }

    // Logique de secours : accepter si même compagnie
    return true;
}

    /**
     * Validation complète du ticket pour le contrôleur
     */





    /**
     * Rechercher un ticket par critères (pour le scan manuel)
     */
    /**
 * Rechercher un ticket par critères (pour le scan manuel)
 */
public function searchTicket(Request $request)
{
    try {
        $user = JWTAuth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $validated = $request->validate([
            'ticket_id' => 'sometimes|integer',
            'passenger_name' => 'sometimes|string',
            'passenger_phone' => 'sometimes|string',
        ]);

        // CORRECTION : Relations correctes
        $query = Ticket::with(['voyage', 'voyage.compagnie', 'voyage.trajet'])
            ->where('statut', 'CONFIRME')
            ->whereNull('dateScan'); // Uniquement les tickets non scannés

        if (isset($validated['ticket_id'])) {
            $query->where('id', $validated['ticket_id']);
        }

        if (isset($validated['passenger_name'])) {
            $query->where('name', 'like', '%' . $validated['passenger_name'] . '%');
        }

        if (isset($validated['passenger_phone'])) {
            $query->where('telephone', 'like', '%' . $validated['passenger_phone'] . '%');
        }

        $tickets = $query->limit(10)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Tickets trouvés',
            'data' => $tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'passenger_name' => $ticket->name,
                    'passenger_phone' => $ticket->telephone,
                    'voyage' => $ticket->voyage ? [
                        'trajet' => $ticket->voyage->trajet_complet ?? 'N/A',
                        'date_depart' => $ticket->voyage->date_depart,
                        'compagnie' => $ticket->voyage->compagnie->name ?? 'N/A'
                    ] : null,
                    'statut' => $ticket->statut,
                    'is_scanned' => !is_null($ticket->dateScan),
                ];
            })
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur recherche ticket: " . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la recherche du ticket'
        ], 500);
    }
}

    /**
     * Récupérer l'historique des scans d'un ticket
     */
    /**
 * Récupérer l'historique des scans d'un ticket
 */
public function getTicketScanHistory(Request $request, $ticketId)
{
    try {
        $user = JWTAuth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        // CORRECTION : Supprimer la relation 'gare' qui n'existe pas
        $scans = Scan::with(['controleur'])
            ->where('idTicket', $ticketId)
            ->orderBy('dateScan', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Historique des scans récupéré',
            'data' => $scans->map(function ($scan) {
                // CORRECTION : Extraire la raison du snapshotTicket
                $snapshot = $scan->snapshotTicket ? json_decode($scan->snapshotTicket, true) : [];
                $validationReason = $snapshot['validation_reason'] ?? null;

                return [
                    'scan_id' => $scan->id,
                    'scan_time' => $scan->dateScan,
                    'controller_name' => $scan->controleur->name ?? 'Inconnu',
                    'gare_name' => $scan->ticket->gare->name ?? 'N/A', // Récupérer depuis le ticket
                    'is_valid' => (bool) $scan->estValide,
                    'invalid_reason' => !$scan->estValide ? $validationReason : null, // CORRECTION ICI
                    'location' => $scan->scan_latitude ? [
                        'latitude' => $scan->scan_latitude,
                        'longitude' => $scan->scan_longitude
                    ] : null
                ];
            })
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur historique scans ticket: " . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la récupération de l\'historique'
        ], 500);
    }
}
}
