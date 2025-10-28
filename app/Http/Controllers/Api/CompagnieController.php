<?php

namespace App\Http\Controllers\Api;

use App\Models\Trajets;
use App\Models\Voyages;
use App\Models\FrequenceTrajets;
use App\Models\Ticket;
use App\Models\Bus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CompagnieController extends Controller
{
    public function compagniesDisponibles(Request $request)
    {
        $request->validate([
            'depart' => 'required|string',
            'arrive' => 'required|string',
            'date' => 'required|date',
            'retour' => 'nullable|date|after_or_equal:date',
            'places' => 'required|integer|min:1',
        ]);

        $depart = $request->depart;
        $arrive = $request->arrive;
        $dateAller = $request->date;
        $dateRetour = $request->retour;
        $placesDemandées = $request->places;

        $now = Carbon::now();

        // 🔹 Fonction pour vérifier la disponibilité d'un voyage
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

        // 🔹 Fonction pour chercher les trajets disponibles
        $chercherTrajets = function ($depart, $arrive, $date, $estRetour = false) use ($placesDemandées, $verifierDisponibilite) {
            $trajets = Trajets::with(['compagnie', 'voyages'])
                ->where('pointDepart', $depart)
                ->where('pointArrive', $arrive)
                ->where('status', 'actif')
                ->get();

            $resultats = [];

            foreach ($trajets as $trajet) {
                $voyages = $trajet->voyages()
                    ->where('dateDepart', $date)
                    ->get();

                $horairesDisponibles = [];

                foreach ($voyages as $voyage) {
                    if ($verifierDisponibilite($voyage, $trajet, $placesDemandées)) {
                        $horairesDisponibles[] = [
                            'idVoyage' => $voyage->id,
                            'heure' => $voyage->heuresDepart,
                            'idBus' => $voyage->idBus,
                            'dateHeureDepart' => Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart)->toDateTimeString(),
                        ];
                    }
                }

                // Trier par heure
                usort($horairesDisponibles, function($a, $b) {
                    return strcmp($a['heure'], $b['heure']);
                });

                if (!empty($horairesDisponibles)) {
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
                        ],
                        'date' => $date,
                        'horaires' => $horairesDisponibles
                    ];
                }
            }

            return $resultats;
        };

        // 🔹 Chercher les trajets pour l'aller
        $aller = $chercherTrajets($depart, $arrive, $dateAller, false);

        // 🔹 STRUCTURE AMÉLIORÉE : Paires fixes aller-retour par bus
        if ($dateRetour) {
            $retourBrut = $chercherTrajets($arrive, $depart, $dateRetour, true);

            $resultatsAvecPaires = [];

            foreach ($aller as $compagnieAller) {
                $compagnieId = $compagnieAller['compagnie']['id'];

                // Trouver la compagnie correspondante dans le retour
                $compagnieRetour = collect($retourBrut)->firstWhere('compagnie.id', $compagnieId);

                if ($compagnieRetour) {
                    $pairesAllerRetour = [];

                    // 🔥 CRÉER DES PAIRES FIXES ALLER-RETOUR
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
                                    'paire_id' => $horaireAller['idVoyage'] . '_' . $horaireRetour['idVoyage'], // ID unique pour la paire
                                    'aller' => $horaireAller,
                                    'retour' => $horaireRetour,
                                    'prix_total_paire' =>  $compagnieRetour['trajet']['prixAllerRetour'],
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
                            'date_retour' => $dateRetour
                        ];
                    }
                }
            }

            // 🔹 Réponse structurée avec paires fixes
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
                        }, $resultatsAvecPaires))
                    ]
                ]
            ]);
        }

        // 🔹 Réponse pour aller simple (structure originale)
        return response()->json([
            'status' => 'success',
            'data' => [
                'type_recherche' => 'aller_simple',
                'compagnies' => $aller,
                'metadata' => [
                    'exclusion_voyages_passes' => true,
                    'nombre_compagnies' => count($aller),
                ]
            ]
        ]);
    }
     
}
