<?php

namespace App\Http\Controllers;

use App\Models\Compagnies;
use Illuminate\Support\Carbon;
use App\Models\AbonementPublic;
use App\Models\PersonalisationCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbonementPublicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère la date d'aujourd'hui
        $today = Carbon::today();

        // Récupère les abonnements où la date de fin est supérieure ou égale à aujourd'hui
        $activeSubscriptions = AbonementPublic::where('dateFin', '>=', $today)
                                                ->get();

        // Retourne la vue avec les abonnements actifs
        return view('back.abonementPublic.index', compact('activeSubscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Récupère uniquement les compagnies de type 'PUBLIC'
        $compagnies = Compagnies::where('type', 'PUBLIC')->get();

        return view('back.abonementPublic.create', compact('compagnies'));
    }
    public function createPerso()
    {
        // Récupère uniquement les compagnies de type 'PUBLIC'
        $compagnies = Compagnies::where('type', 'PUBLIC')->get();

        return view('back.abonementPublic.personaliserCard', compact('compagnies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation des données avec Request
            $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'duree' => 'required|integer|min:1',
                'dateNaiss' => 'required|date',
                'profession' => 'nullable|string|max:255',
                'etablissement' => 'nullable|string|max:255',
                'idCompagnie' => 'required|exists:compagnies,id',
                'Photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation pour le fichier photo
            ]);

            // Vérifier si une personnalisation existe déjà pour cette compagnie
            $personalisationCard = PersonalisationCard::where('idCompagnie', $request->input('idCompagnie'))->first();

            if (!$personalisationCard) {
                return redirect()->back()
                                 ->with('error', 'Veuillez personnaliser la carte pour cette compagnie avant de créer un abonnement.');
            }

            // Gérer le téléchargement de la photo
            $photoPath = null;
            if ($request->hasFile('Photo')) {
                $photoPath = $request->file('Photo')->store('photos', 'public');
            }

            // Calcul de la date de début et de fin
            $dateDebut = Carbon::now();
            $dureeEnJours = (int) $request->input('duree');
            $dateFin = $dateDebut->copy()->addDays($dureeEnJours);

            // Création de l'abonnement public
            AbonementPublic::create([
                'duree' => $dureeEnJours,
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'statut' => 'actif',
                'Photo' => $photoPath, // Enregistre le chemin de la photo
                'dateNaiss' => $request->input('dateNaiss'),
                'profession' => $request->input('profession'),
                'etablissement' => $request->input('etablissement'),
                'idCompagnie' => $personalisationCard->idCompagnie,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
            ]);

            return redirect()->route('abonementPublic.index')
                             ->with('success', 'Abonnement public créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Erreur lors de la création de l\'abonnement : ' . $e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function storePersonalisationCard(Request $request)
    {
        try {
            // Validation des données pour la personnalisation de la carte
            $request->validate([
                'pays' => 'required|string|max:255',
                'devise' => 'required|string|max:255',
                'numero' => 'required|string|max:255',
                'couleur_principale' => 'required|string|max:255',
                'idCompagnie' => 'required|exists:compagnies,id',
            ]);

            PersonalisationCard::create([
                'pays' => $request->input('pays'),
                'devise' => $request->input('devise'),
                'numero' => $request->input('numero'),
                'couleur_principale' => $request->input('couleur_principale'),
                'idCompagnie' => $request->input('idCompagnie'),
            ]);

            return redirect()->route('abonementPublic.index')
                             ->with('success', 'Personnalisation de la carte créée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Erreur lors de la création de la personnalisation : ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AbonementPublic $abonementPublic)
    {
        // Récupère la carte de personnalisation associée à la compagnie de l'abonnement
        $personalisationCard = PersonalisationCard::where('idCompagnie', $abonementPublic->idCompagnie)->first();

        // Récupère uniquement les compagnies de type 'PUBLIC' pour le formulaire
        $compagnies = Compagnies::where('type', 'PUBLIC')->get();

        return view('back.abonementPublic.create', compact('abonementPublic', 'personalisationCard', 'compagnies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AbonementPublic $abonementPublic)
    {
        try {
            // Validation des données avec Request
            $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'duree' => 'required|integer|min:1',
                'dateNaiss' => 'required|date',
                'profession' => 'nullable|string|max:255',
                'etablissement' => 'nullable|string|max:255',
                'idCompagnie' => 'required|exists:compagnies,id',
                'Photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation pour le fichier photo
            ]);

            // Gérer le téléchargement de la nouvelle photo
            $photoPath = $abonementPublic->Photo; // Conserve le chemin actuel par défaut
            if ($request->hasFile('Photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($abonementPublic->Photo && Storage::disk('public')->exists($abonementPublic->Photo)) {
                    Storage::disk('public')->delete($abonementPublic->Photo);
                }
                // Stocker la nouvelle photo
                $photoPath = $request->file('Photo')->store('photos', 'public');
            }

            // Mettre à jour l'abonnement public
            $abonementPublic->update([
                'duree' => $request->input('duree'),
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'Photo' => $photoPath, // Met à jour le chemin de la photo
                'dateNaiss' => $request->input('dateNaiss'),
                'profession' => $request->input('profession'),
                'etablissement' => $request->input('etablissement'),
                'idCompagnie' => $request->input('idCompagnie'),
                // Recalcul de la date de fin en cas de changement de durée
                'dateFin' => Carbon::parse($abonementPublic->dateDebut)->addDays($request->input('duree')),
            ]);

            // Gérer la mise à jour de la PersonalisationCard
            // On cherche la carte de personnalisation par l'id de la compagnie
            $personalisationCard = PersonalisationCard::where('idCompagnie', $request->input('idCompagnie'))->first();

            if ($personalisationCard) {
                $personalisationCard->update([
                    'pays' => $request->input('pays'),
                    'devise' => $request->input('devise'),
                    'numero' => $request->input('numero'),
                    'couleur_principale' => $request->input('couleur_principale'),
                ]);
            }

            return redirect()->route('abonementPublic.index')
                             ->with('success', 'Abonnement et personnalisation mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function editPersonalisationCard(PersonalisationCard $personalisationCard)
    {
        // Récupère uniquement les compagnies de type 'PUBLIC'
        $compagnies = Compagnies::where('type', 'PUBLIC')->get();

        return view('back.abonementPublic.personaliserCard', compact('personalisationCard', 'compagnies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePersonalisationCard(Request $request, PersonalisationCard $personalisationCard)
    {
        try {
            // Validation des données pour la personnalisation de la carte
            $request->validate([
                'pays' => 'required|string|max:255',
                'devise' => 'required|string|max:255',
                'numero' => 'required|string|max:255',
                'couleur_principale' => 'required|string|max:255',
                'idCompagnie' => 'required|exists:compagnies,id',
            ]);

            $personalisationCard->update([
                'pays' => $request->input('pays'),
                'devise' => $request->input('devise'),
                'numero' => $request->input('numero'),
                'couleur_principale' => $request->input('couleur_principale'),
                'idCompagnie' => $request->input('idCompagnie'),
            ]);

            return redirect()->route('abonementPublic.index')
                             ->with('success', 'Personnalisation de la carte mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Erreur lors de la mise à jour de la personnalisation : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AbonementPublic $abonementPublic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AbonementPublic $abonementPublic)
    {
        //
    }
}
