<?php

namespace App\Http\Controllers;

use App\Models\Paiements;
use App\Models\Compagnies;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AbonementPublic;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalisationCard;
use Illuminate\Support\Facades\Storage;

class AbonementPublicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $today = Carbon::today();

    // Charger les relations "compagnie" et "personalisationCard"
    $activeSubscriptions = AbonementPublic::where('dateFin', '>=', $today)
        ->with(['compagnie.personalisationCard']) // eager loading
        ->get();

      //  dd($activeSubscriptions);

    return view('back.abonementPublic.index', compact('activeSubscriptions'));
}

public function indexe(){
    $personalisations = PersonalisationCard::with('compagnie')->get();

    return view('back.abonementPublic.allPerso', compact('personalisations'));
}
public function modifier(PersonalisationCard $perso)
{
    $user = auth()->user();
    if($user->profil->name == 'Admin général'){
        $compagnies = Compagnies::where('type', 'PUBLIC')->get();
       return view('back.abonementPublic.modifier', compact('perso', 'compagnies'));
    }
     $compagnies = Compagnies::where('type', 'PUBLIC')
                    ->where('id', $user->idCompagnie)
                    ->get();
    return view('back.abonementPublic.modifier', compact('perso', 'compagnies'));
}

public function updateCarde(Request $request, PersonalisationCard $perso)
{
    $request->validate([
        'pays' => 'required|string|max:255',
        'devise' => 'required|string|max:255',
        'numero' => 'required|string|max:255',
        'couleur_principale' => 'required|string|max:20',
        'prix' => 'required|numeric|min:0',
        'idCompagnie' => 'required|exists:compagnies,id',
    ]);

    $perso->update($request->all());

    return redirect()->route('personalisationCard.indexe')
                     ->with('success', 'La personnalisation de carte a été mise à jour avec succès.');
}

public function destroyPersonalisationCard(PersonalisationCard $personalisationCard)
{
    try {
        $personalisationCard->delete();

        return redirect()->route('personalisationCard.indexe')
                         ->with('success', 'La personnalisation de carte a été supprimée avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
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
        // ✅ Validation des données
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'duree' => 'required|integer|min:1',
            'dateNaiss' => 'required|date',
            'profession' => 'nullable|string|max:255',
            'etablissement' => 'nullable|string|max:255',
            'idCompagnie' => 'required|exists:compagnies,id',
            'Photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'montant' => 'required|numeric|min:0', // ✅ montant obligatoire
            'moyenPaiement' => 'required|string', // ex: "MOBILE", "CARTE"
            'telephone' => 'nullable|string',
            'referenceTransaction' => 'required|string|unique:paiements,referenceTransaction',
        ]);

        DB::beginTransaction();

        // ✅ Vérifier personnalisation de la carte
        $personalisationCard = PersonalisationCard::where('idCompagnie', $request->input('idCompagnie'))->first();
        if (!$personalisationCard) {
            return redirect()->back()
                ->with('error', 'Veuillez personnaliser la carte pour cette compagnie avant de créer un abonnement.');
        }

        // ✅ Gérer la photo
        $photoPath = null;
        if ($request->hasFile('Photo')) {
            $photoPath = $request->file('Photo')->store('photos', 'public');
        }

        // ✅ Calcul dates
        $dateDebut = Carbon::now();
        $dureeEnJours = (int) $request->input('duree');
        $dateFin = $dateDebut->copy()->addDays($dureeEnJours);

        // ✅ 1. Créer le paiement
        $paiement = Paiements::create([
            'montant' => $request->montant,
            'moyenPaiement' => $request->moyenPaiement,
            'statut' => 'SUCCES', // par défaut, à confirmer si tu veux gérer ECHEC aussi
            'typeSource' => 'MOBILE',
            'telephone' => $request->telephone,
            'referenceTransaction' => $request->referenceTransaction,
        ]);

        // ✅ 2. Créer l’abonnement et associer le paiement
        AbonementPublic::create([
            'duree' => $dureeEnJours,
            'nom' => $request->input('nom'),
            'prenom' => $request->input('prenom'),
            'statut' => 'actif',
            'photo' => $photoPath,
            'dateNaiss' => $request->input('dateNaiss'),
            'profession' => $request->input('profession'),
            'etablissement' => $request->input('etablissement'),
            'idCompagnie' => $personalisationCard->idCompagnie,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'idPaiement' => $paiement->id, // ✅ lien abonnement-paiement
        ]);

        DB::commit();

        return redirect()->route('abonementPublic.index')
            ->with('success', 'Abonnement public créé avec succès et paiement enregistré.');

    } catch (\Exception $e) {
        DB::rollBack();
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
        // Validation des données
        $request->validate([
            'pays' => 'required|string|max:255',
            'devise' => 'required|string|max:255',
            'numero' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'couleur_principale' => 'required|string|max:255',
            'idCompagnie' => 'required|exists:compagnies,id',
        ]);

        PersonalisationCard::create([
            'pays' => $request->input('pays'),
            'devise' => $request->input('devise'),
            'prix' => $request->input('prix'),
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
