<?php

namespace App\Http\Controllers\Api;

use App\Models\Paiements;
use App\Models\Compagnies;

use Illuminate\Http\Request;
//AbonnementPublic
use Illuminate\Support\Carbon;
use App\Models\AbonementPublic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AbonnementPublicControllerApiController extends Controller
{
    //

    public function getCompagniesPublics()
    {
        try {
            // Récupère les compagnies publiques AVEC leur personalisationCard
            $compagnies = Compagnies::where('type', 'PUBLIC')
                ->with('personalisationCard')
                ->get();

            // Vérifie si les compagnies ont une personalisationCard
            $compagnies->each(function ($compagnie) {
                if (!$compagnie->personalisationCard) {
                    // Optionnel: créer une personalisationCard par défaut si elle n'existe pas
                    // $compagnie->personalisationCard = new \App\Models\PersonalisationCard([
                    //     'pays' => 'Sénégal',
                    //     'devise' => 'FCFA',
                    //     'numero' => 'N/A',
                    //     'couleur_principale' => '#0066CC',
                    //     'prix' => 0.0
                    // ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Liste des compagnies récupérée avec succès.',
                'data' => $compagnies
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des compagnies.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
public function confirmerPaiementAbonnement(Request $request)
{
    DB::beginTransaction();

    try {
        // Validation des données AVEC DURÉE
        $validatedData = $request->validate([
            'idCompagnie' => 'required|exists:compagnies,id',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'profession' => 'required|string|max:255',
            'etablissement' => 'nullable|string|max:255',
            'dateNaiss' => 'required|date',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'duree' => 'required|integer|min:1', // NOUVEAU: Durée en jours
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'referenceTransaction' => 'required|string|unique:paiements,referenceTransaction',
            'montant' => 'required|numeric|min:0',
            'telephone' => 'required|string|max:20',
            'moyenPaiement' => 'required|string|in:OM,MOOV',
        ]);

        // Récupération de la compagnie avec sa personalisation
        $compagnie = Compagnies::with('personalisationCard')
            ->findOrFail($validatedData['idCompagnie']);

        $prixMensuel = $compagnie->personalisationCard->prix ?? null;

        // Vérification du tarif
        if (!$prixMensuel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette compagnie n\'a pas de tarif défini pour la carte personnalisée.'
            ], 400);
        }

        // NOUVEAU: Calcul du prix attendu basé sur la durée
        $dureeMois = $validatedData['duree'] / 30; // Convertir jours en mois
        $prixAttendu = $prixMensuel * $dureeMois;

        // Vérification du montant payé (avec marge d'erreur de 1 FCFA)
        if (abs($validatedData['montant'] - $prixAttendu) > 1) {
            return response()->json([
                'status' => 'error',
                'message' => "Le montant payé ({$validatedData['montant']} FCFA) ne correspond pas au prix attendu (" . round($prixAttendu, 0) . " FCFA) pour {$dureeMois} mois."
            ], 400);
        }

        // Upload de la photo si présente
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('abonnements', 'public');

            // Vérification que l'upload a réussi
            if (!$photoPath) {
                throw new \Exception('Échec de l\'upload de la photo');
            }
        }

        // Création du paiement
        $paiement = Paiements::create([
            'montant' => $validatedData['montant'],
            'moyenPaiement' => $validatedData['moyenPaiement'],
            'statut' => 'SUCCES',
            'typeSource' => 'MOBILE',
            'telephone' => $validatedData['telephone'],
            'referenceTransaction' => $validatedData['referenceTransaction'],
            'datePaiement' => now(),
        ]);

        // SUPPRIMER: Plus besoin de calculer la durée, elle est fournie
        // $duree = 30; // Ancien code
        // if ($validatedData['dateDebut'] && $validatedData['dateFin']) {
        //     $dateDebut = Carbon::parse($validatedData['dateDebut']);
        //     $dateFin = Carbon::parse($validatedData['dateFin']);
        //     $duree = $dateFin->diffInDays($dateDebut);
        // }

        // Création de l'abonnement AVEC LA DURÉE FOURNIE
        $abonnement = AbonementPublic::create([
            'idUser' => auth()->id(),
            'idCompagnie' => $compagnie->id,
            'idPaiement' => $paiement->id,
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'profession' => $validatedData['profession'],
            'etablissement' => $validatedData['etablissement'] ?? null,
            'photo' => $photoPath,
            'statut' => 'actif',
            'duree' => $validatedData['duree'], // Utiliser la durée fournie
            'dateNaiss' => $validatedData['dateNaiss'],
            'dateDebut' => $validatedData['dateDebut'],
            'dateFin' => $validatedData['dateFin'],
            'dateCreation' => now(),
        ]);

        // Chargement des relations pour la réponse
        $abonnement->load(['compagnie', 'compagnie.personalisationCard']);

        DB::commit();

        // Préparation de la réponse AVEC DÉTAILS DU CALCUL
        $response = [
            'status' => 'success',
            'message' => 'Paiement confirmé et abonnement créé avec succès.',
            'data' => [
                'abonnement' => $abonnement,
                'paiement' => $paiement,
                'details_calcul' => [ // NOUVEAU: Détails du calcul
                    'prix_mensuel' => $prixMensuel,
                    'duree_jours' => $validatedData['duree'],
                    'duree_mois' => round($dureeMois, 1),
                    'prix_calcule' => $prixAttendu,
                    'prix_paye' => $validatedData['montant']
                ]
            ]
        ];

        // Ajout de l'URL de la photo si elle existe
        if ($photoPath) {
            $response['data']['photo_url'] = asset('storage/'.$photoPath);
        }

        return response()->json($response, 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur de validation des données.',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Compagnie non trouvée.',
        ], 404);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur paiement abonnement : ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors du traitement du paiement.',
            'error_details' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur',
        ], 500);
    }
}

    /**
     * Méthode pour supprimer une image en cas d'échec de la transaction
     */
    private function deleteUploadedFile($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    /**
     * Méthode pour formater les dates
     */
    private function formatDate($date)
    {
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception('Format de date invalide: ' . $date);
        }
    }


 public function getAbonnementActif(Request $request)
    {
        $idUser = auth()->id();
        $today = Carbon::today();

        try {
            $abonnement = AbonementPublic::with(['compagnie.personalisationCard'])
                ->where('idUser', $idUser)
                ->where('dateDebut', '<=', $today)
                ->where('dateFin', '>=', $today)
                ->orderBy('dateFin', 'desc') // si plusieurs, on prend le + récent
                ->first();

            if (!$abonnement) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Aucun abonnement actif trouvé.',
                    'data' => null,
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Abonnement actif récupéré avec succès.',
                'data' => $abonnement,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération de l\'abonnement actif.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les abonnements expirés de l'utilisateur connecté
     */
    public function getAbonnementsExpires(Request $request)
    {
        $idUser = auth()->id();
        $today = Carbon::today();

        try {
            $abonnements = AbonementPublic::with(['compagnie.personalisationCard'])
                ->where('idUser', $idUser)
                ->where('dateFin', '<', $today)
                ->orderBy('dateFin', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Liste des abonnements expirés récupérée avec succès.',
                'data' => $abonnements,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des abonnements expirés.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

}
