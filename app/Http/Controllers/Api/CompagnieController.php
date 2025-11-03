<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Bus;
use App\Models\Ticket;
use App\Models\Trajets;
use App\Models\Voyages;
use Illuminate\Http\Request;
use App\Models\FrequenceTrajets;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CompagnieController extends Controller
{
    // public function compagniesDisponibles(Request $request)
    // {
    //     $request->validate([
    //         'depart' => 'required|string',
    //         'arrive' => 'required|string',
    //         'date' => 'required|date',
    //         'retour' => 'nullable|date|after_or_equal:date',
    //         'places' => 'required|integer|min:1',
    //     ]);

    //     $depart = $request->depart;
    //     $arrive = $request->arrive;
    //     $dateAller = $request->date;
    //     $dateRetour = $request->retour;
    //     $placesDemandées = $request->places;

    //     $now = Carbon::now();

    //     // 🔹 Fonction pour vérifier la disponibilité d'un voyage
    //     $verifierDisponibilite = function ($voyage, $trajet, $placesDemandées) {
    //         $dateHeureDepart = Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart);

    //         // Vérifier si le voyage est dans le futur
    //         if ($dateHeureDepart->lt(Carbon::now())) {
    //             return false;
    //         }

    //         // Vérifier les places disponibles
    //         if ($voyage->idBus) {
    //             $bus = Bus::find($voyage->idBus);
    //             return $bus && $bus->nombrePlaceDispo >= $placesDemandées;
    //         } else {
    //             $frequence = FrequenceTrajets::where('idTrajet', $trajet->id)->first();
    //             $ticketsVendus = Ticket::where('idVoyage', $voyage->id)->count();
    //             $placesRestantes = $frequence ? ($frequence->nombrePlaceMinimum - $ticketsVendus) : 0;
    //             return $placesRestantes >= $placesDemandées;
    //         }
    //     };

    //     // 🔹 Fonction pour chercher les trajets disponibles
    //     $chercherTrajets = function ($depart, $arrive, $date, $estRetour = false) use ($placesDemandées, $verifierDisponibilite) {
    //         $trajets = Trajets::with(['compagnie', 'voyages'])
    //             ->where('pointDepart', $depart)
    //             ->where('pointArrive', $arrive)
    //             ->where('status', 'actif')
    //             ->get();

    //         $resultats = [];

    //         foreach ($trajets as $trajet) {
    //             $voyages = $trajet->voyages()
    //                 ->where('dateDepart', $date)
    //                 ->get();

    //             $horairesDisponibles = [];

    //             foreach ($voyages as $voyage) {
    //                 if ($verifierDisponibilite($voyage, $trajet, $placesDemandées)) {
    //                     $horairesDisponibles[] = [
    //                         'idVoyage' => $voyage->id,
    //                         'heure' => $voyage->heuresDepart,
    //                         'idBus' => $voyage->idBus,
    //                         'dateHeureDepart' => Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart)->toDateTimeString(),
    //                     ];
    //                 }
    //             }

    //             // Trier par heure
    //             usort($horairesDisponibles, function($a, $b) {
    //                 return strcmp($a['heure'], $b['heure']);
    //             });

    //             if (!empty($horairesDisponibles)) {
    //                 $resultats[] = [
    //                     'compagnie' => [
    //                         'id' => $trajet->compagnie->id,
    //                         'name' => $trajet->compagnie->name,
    //                         'logo' => $trajet->compagnie->logo,
    //                         'telephone' => $trajet->compagnie->telephone,
    //                         'email' => $trajet->compagnie->email,
    //                     ],
    //                     'trajet' => [
    //                         'id' => $trajet->id,
    //                         'pointDepart' => $trajet->pointDepart,
    //                         'pointArrive' => $trajet->pointArrive,
    //                         'prix' => $estRetour ? $trajet->prixAllerRetour : $trajet->prix,
    //                         'prixAllerSimple' => $trajet->prix,
    //                         'prixAllerRetour' => $trajet->prixAllerRetour,
    //                         'type' => $estRetour ? 'Retour' : 'Aller',
    //                     ],
    //                     'date' => $date,
    //                     'horaires' => $horairesDisponibles
    //                 ];
    //             }
    //         }

    //         return $resultats;
    //     };

    //     // 🔹 Chercher les trajets pour l'aller
    //     $aller = $chercherTrajets($depart, $arrive, $dateAller, false);

    //     // 🔹 STRUCTURE AMÉLIORÉE : Paires fixes aller-retour par bus
    //     if ($dateRetour) {
    //         $retourBrut = $chercherTrajets($arrive, $depart, $dateRetour, true);

    //         $resultatsAvecPaires = [];

    //         foreach ($aller as $compagnieAller) {
    //             $compagnieId = $compagnieAller['compagnie']['id'];

    //             // Trouver la compagnie correspondante dans le retour
    //             $compagnieRetour = collect($retourBrut)->firstWhere('compagnie.id', $compagnieId);

    //             if ($compagnieRetour) {
    //                 $pairesAllerRetour = [];

    //                 // 🔥 CRÉER DES PAIRES FIXES ALLER-RETOUR
    //                 foreach ($compagnieAller['horaires'] as $horaireAller) {
    //                     foreach ($compagnieRetour['horaires'] as $horaireRetour) {
    //                         // Si même jour, vérifier l'écart horaire
    //                         $estCompatible = true;
    //                         if ($dateAller === $dateRetour) {
    //                             $heureAller = Carbon::parse($horaireAller['heure']);
    //                             $heureRetour = Carbon::parse($horaireRetour['heure']);
    //                             $differenceHeures = $heureRetour->diffInHours($heureAller, false);

    //                             if ($differenceHeures < 2) {
    //                                 $estCompatible = false;
    //                             }
    //                         }

    //                         if ($estCompatible) {
    //                             $pairesAllerRetour[] = [
    //                                 'paire_id' => $horaireAller['idVoyage'] . '_' . $horaireRetour['idVoyage'], // ID unique pour la paire
    //                                 'aller' => $horaireAller,
    //                                 'retour' => $horaireRetour,
    //                                 'prix_total_paire' =>  $compagnieRetour['trajet']['prixAllerRetour'],
    //                                 'bus_aller' => $horaireAller['idBus'],
    //                                 'bus_retour' => $horaireRetour['idBus']
    //                             ];
    //                         }
    //                     }
    //                 }

    //                 if (!empty($pairesAllerRetour)) {
    //                     $resultatsAvecPaires[] = [
    //                         'compagnie' => $compagnieAller['compagnie'],
    //                         'trajet_aller' => $compagnieAller['trajet'],
    //                         'trajet_retour' => $compagnieRetour['trajet'],
    //                         'paires_aller_retour' => $pairesAllerRetour,
    //                         'date_aller' => $dateAller,
    //                         'date_retour' => $dateRetour
    //                     ];
    //                 }
    //             }
    //         }

    //         // 🔹 Réponse structurée avec paires fixes
    //         return response()->json([
    //             'status' => 'success',
    //             'data' => [
    //                 'type_recherche' => 'aller_retour_paires_fixes',
    //                 'compagnies' => $resultatsAvecPaires,
    //                 'metadata' => [
    //                     'exclusion_voyages_passes' => true,
    //                     'filtre_meme_compagnie' => true,
    //                     'ecart_horaire_minimum' => ($dateAller === $dateRetour) ? '2 heures' : 'non applicable',
    //                     'nombre_compagnies' => count($resultatsAvecPaires),
    //                     'nombre_paires_total' => array_sum(array_map(function($c) {
    //                         return count($c['paires_aller_retour']);
    //                     }, $resultatsAvecPaires))
    //                 ]
    //             ]
    //         ]);
    //     }

    //     // 🔹 Réponse pour aller simple (structure originale)
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => [
    //             'type_recherche' => 'aller_simple',
    //             'compagnies' => $aller,
    //             'metadata' => [
    //                 'exclusion_voyages_passes' => true,
    //                 'nombre_compagnies' => count($aller),
    //             ]
    //         ]
    //     ]);
    // }

    public function compagniesDisponibles(Request $request)
    {
        // Validation des données
        $request->validate([
            'depart' => 'required|string',
            'arrive' => 'required|string',
            'date' => 'required|date',
            'retour' => 'nullable|date|after_or_equal:date',
            'places' => 'required|integer|min:1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $depart = $request->depart;
            $arrive = $request->arrive;
            $dateAller = $request->date;
            $dateRetour = $request->retour;
            $placesDemandées = $request->places;
            $userLat = $request->latitude;
            $userLng = $request->longitude;

            Log::info('Recherche de compagnies', [
                'depart' => $depart,
                'arrive' => $arrive,
                'date_aller' => $dateAller,
                'date_retour' => $dateRetour,
                'places' => $placesDemandées,
                'latitude' => $userLat,
                'longitude' => $userLng
            ]);

            // Fonction pour calculer la distance entre deux points GPS
            $calculerDistance = function($lat1, $lng1, $lat2, $lng2) {
                if (!$lat1 || !$lng1 || !$lat2 || !$lng2) {
                    return null;
                }

                $earthRadius = 6371; // Rayon de la Terre en km

                $dLat = deg2rad($lat2 - $lat1);
                $dLng = deg2rad($lng2 - $lng1);

                $a = sin($dLat/2) * sin($dLat/2) +
                     cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                     sin($dLng/2) * sin($dLng/2);

                $c = 2 * atan2(sqrt($a), sqrt(1-$a));

                return $earthRadius * $c;
            };

            // Fonction pour vérifier la disponibilité d'un voyage
            $verifierDisponibilite = function ($voyage, $trajet, $placesDemandées) {
                $dateHeureDepart = Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart);

                // Vérifier si le voyage est dans le futur
                if ($dateHeureDepart->lt(Carbon::now())) {
                    return false;
                }

                // Vérifier les places disponibles
                if ($voyage->idBus) {
                    $bus = Bus::find($voyage->idBus);
                    return $bus && $bus->nombrePlaceDispo >= $placesDemandées;
                } else {
                    $frequence = FrequenceTrajets::where('idTrajet', $trajet->id)->first();
                    $ticketsVendus = Ticket::where('idVoyage', $voyage->id)->count();
                    $placesRestantes = $frequence ? ($frequence->nombrePlaceMinimum - $ticketsVendus) : 0;
                    return $placesRestantes >= $placesDemandées;
                }
            };

            // Fonction pour chercher les trajets disponibles
            $chercherTrajets = function ($depart, $arrive, $date, $estRetour = false)
                use ($placesDemandées, $verifierDisponibilite, $userLat, $userLng, $calculerDistance) {

                Log::info("Recherche trajets: $depart -> $arrive, date: $date");

                // Charger les trajets avec les relations nécessaires
                $trajets = Trajets::with(['compagnie', 'voyages'])
                    ->where('pointDepart', $depart)
                    ->where('pointArrive', $arrive)
                    ->where('status', 'actif')
                    ->get();

                Log::info("Nombre de trajets trouvés: " . $trajets->count());

                $resultats = [];

                foreach ($trajets as $trajet) {
                    Log::info("Traitement trajet ID: " . $trajet->id . " - " . $trajet->compagnie->name);

                    $voyages = $trajet->voyages()
                        ->where('dateDepart', $date)
                        ->get();

                    Log::info("Nombre de voyages pour ce trajet: " . $voyages->count());

                    $horairesDisponibles = [];
                    $garesAvecHoraires = [];

                    foreach ($voyages as $voyage) {
                        if ($verifierDisponibilite($voyage, $trajet, $placesDemandées)) {
                            $horaireData = [
                                'idVoyage' => $voyage->id,
                                'heure' => $voyage->heuresDepart,
                                'idBus' => $voyage->idBus,
                                'dateHeureDepart' => Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart)->toDateTimeString(),
                            ];

                            $horairesDisponibles[] = $horaireData;

                            // Gestion des gares (si la relation existe)
                            try {
                                if (method_exists($trajet, 'gares')) {
                                    $trajet->load('gares');
                                    foreach ($trajet->gares as $gare) {
                                        $gareKey = $gare->id;
                                        if (!isset($garesAvecHoraires[$gareKey])) {
                                            $garesAvecHoraires[$gareKey] = [
                                                'gare' => [
                                                    'id' => $gare->id,
                                                    'nom' => $gare->name,
                                                    'ville' => $gare->ville,
                                                ],
                                                'horaires' => []
                                            ];
                                        }
                                        $garesAvecHoraires[$gareKey]['horaires'][] = $horaireData;
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::warning("Erreur lors du chargement des gares: " . $e->getMessage());
                                // Continuer sans les gares
                            }
                        }
                    }

                    // Si pas de gares spécifiques, utiliser le point de départ
                    if (empty($garesAvecHoraires)) {
                        $garesAvecHoraires['default'] = [
                            'gare' => [
                                'nom' => $trajet->pointDepart,
                                'ville' => $trajet->pointDepart,
                            ],
                            'horaires' => $horairesDisponibles
                        ];
                    }

                    // Trier les horaires par heure
                    foreach ($garesAvecHoraires as &$gareData) {
                        usort($gareData['horaires'], function($a, $b) {
                            return strcmp($a['heure'], $b['heure']);
                        });
                    }

                    usort($horairesDisponibles, function($a, $b) {
                        return strcmp($a['heure'], $b['heure']);
                    });

                    if (!empty($horairesDisponibles)) {
                        // Calcul de la distance si position utilisateur disponible
                        $distanceUtilisateur = null;
                        $garePlusProche = null;

                        if ($userLat && $userLng) {
                            try {
                                if (method_exists($trajet, 'gares')) {
                                    $trajet->load('gares');
                                    foreach ($trajet->gares as $gare) {
                                        if ($gare->hasCoordinates()) {
                                            $distance = $calculerDistance(
                                                $userLat, $userLng,
                                                $gare->latitude, $gare->longitude
                                            );

                                            if ($distance !== null && ($distanceUtilisateur === null || $distance < $distanceUtilisateur)) {
                                                $distanceUtilisateur = $distance;
                                                $garePlusProche = [
                                                    'gare' => [
                                                        'id' => $gare->id,
                                                        'nom' => $gare->name,
                                                        'ville' => $gare->ville,
                                                    ],
                                                    'distance_km' => round($distance, 1)
                                                ];
                                            }
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::warning("Erreur calcul distance: " . $e->getMessage());
                            }
                        }

                        $resultats[] = [
                            'compagnie' => [
                                'id' => $trajet->compagnie->id,
                                'name' => $trajet->compagnie->name,
                                'logo' => $trajet->compagnie->logo,
                                'telephone' => $trajet->compagnie->telephone,
                                'email' => $trajet->compagnie->email,
                            ],
                            'trajet' => [
                                'id' => $trajet->id,
                                'pointDepart' => $trajet->pointDepart,
                                'pointArrive' => $trajet->pointArrive,
                                'prix' => $estRetour ? $trajet->prixAllerRetour : $trajet->prix,
                                'prixAllerSimple' => $trajet->prix,
                                'prixAllerRetour' => $trajet->prixAllerRetour,
                                'type' => $estRetour ? 'Retour' : 'Aller',
                                'gares_depart' => array_values($garesAvecHoraires),
                            ],
                            'date' => $date,
                            'horaires' => $horairesDisponibles,
                            'distance_utilisateur_km' => $distanceUtilisateur ? round($distanceUtilisateur, 1) : null,
                            'gare_plus_proche' => $garePlusProche,
                        ];

                        Log::info("Trajet ajouté: " . $trajet->compagnie->name . " - " . count($horairesDisponibles) . " horaires");
                    }
                }

                Log::info("Nombre de résultats trouvés: " . count($resultats));
                return $resultats;
            };

            // Chercher les trajets pour l'aller
            $aller = $chercherTrajets($depart, $arrive, $dateAller, false);

            // Structure pour aller-retour
            if ($dateRetour) {
                Log::info("Recherche aller-retour activée");
                $retourBrut = $chercherTrajets($arrive, $depart, $dateRetour, true);

                $resultatsAvecPaires = [];

                foreach ($aller as $compagnieAller) {
                    $compagnieId = $compagnieAller['compagnie']['id'];

                    // Trouver la compagnie correspondante dans le retour
                    $compagnieRetour = collect($retourBrut)->firstWhere('compagnie.id', $compagnieId);

                    if ($compagnieRetour) {
                        $pairesAllerRetour = [];

                        foreach ($compagnieAller['horaires'] as $horaireAller) {
                            foreach ($compagnieRetour['horaires'] as $horaireRetour) {
                                // Si même jour, vérifier l'écart horaire
                                $estCompatible = true;
                                if ($dateAller === $dateRetour) {
                                    $heureAller = Carbon::parse($horaireAller['heure']);
                                    $heureRetour = Carbon::parse($horaireRetour['heure']);
                                    $differenceHeures = $heureRetour->diffInHours($heureAller, false);

                                    if ($differenceHeures < 2) {
                                        $estCompatible = false;
                                    }
                                }

                                if ($estCompatible) {
                                    $pairesAllerRetour[] = [
                                        'paire_id' => $horaireAller['idVoyage'] . '_' . $horaireRetour['idVoyage'],
                                        'aller' => $horaireAller,
                                        'retour' => $horaireRetour,
                                        'prix_total_paire' => $compagnieRetour['trajet']['prixAllerRetour'],
                                        'bus_aller' => $horaireAller['idBus'],
                                        'bus_retour' => $horaireRetour['idBus']
                                    ];
                                }
                            }
                        }

                        if (!empty($pairesAllerRetour)) {
                            $resultatsAvecPaires[] = [
                                'compagnie' => $compagnieAller['compagnie'],
                                'trajet_aller' => $compagnieAller['trajet'],
                                'trajet_retour' => $compagnieRetour['trajet'],
                                'paires_aller_retour' => $pairesAllerRetour,
                                'date_aller' => $dateAller,
                                'date_retour' => $dateRetour,
                                'distance_utilisateur_km_aller' => $compagnieAller['distance_utilisateur_km'],
                                'distance_utilisateur_km_retour' => $compagnieRetour['distance_utilisateur_km'],
                                'gare_plus_proche_aller' => $compagnieAller['gare_plus_proche'],
                                'gare_plus_proche_retour' => $compagnieRetour['gare_plus_proche'],
                            ];
                        }
                    }
                }

                Log::info("Aller-retour: " . count($resultatsAvecPaires) . " compagnies avec paires");

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'type_recherche' => 'aller_retour_paires_fixes',
                        'compagnies' => $resultatsAvecPaires,
                        'metadata' => [
                            'exclusion_voyages_passes' => true,
                            'filtre_meme_compagnie' => true,
                            'ecart_horaire_minimum' => ($dateAller === $dateRetour) ? '2 heures' : 'non applicable',
                            'nombre_compagnies' => count($resultatsAvecPaires),
                            'nombre_paires_total' => array_sum(array_map(function($c) {
                                return count($c['paires_aller_retour']);
                            }, $resultatsAvecPaires)),
                            'timestamp' => now()->toDateTimeString(),
                        ]
                    ]
                ], 200, [], JSON_UNESCAPED_UNICODE);

            } else {
                // Réponse pour aller simple
                Log::info("Aller simple: " . count($aller) . " compagnies trouvées");

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'type_recherche' => 'aller_simple',
                        'compagnies' => $aller,
                        'metadata' => [
                            'exclusion_voyages_passes' => true,
                            'nombre_compagnies' => count($aller),
                            'timestamp' => now()->toDateTimeString(),
                        ]
                    ]
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }

        } catch (\Exception $e) {
            Log::error('Erreur dans compagniesDisponibles: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur serveur lors de la recherche',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }
}
