<?php

namespace App\Http\Controllers\Abonements;

use App\Http\Controllers\Controller;
use App\Models\Abonement;
use App\Models\TypeAbonement;
use App\Models\Compagnies;
use App\Http\Requests\Abonements\StoreAbonementRequest;
use App\Http\Requests\Abonements\UpdateAbonementRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class AbonementController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $abonnements = Abonement::with(['typeAbonement', 'compagnie'])
            ->when($user->profil->name === 'Admin compagnie', function ($query) use ($user) {
                if (!$user->idCompagnie) {
                    abort(403, 'Votre compte administrateur de compagnie n\'est pas associé à une compagnie.');
                }
                $query->where('idCompagnie', $user->idCompagnie);
            })
            ->latest()
            ->get();

        return view('back.abonements.index', compact('abonnements'));
    }

    public function create()
    {
        // Seul l'Admin général peut créer des abonnements pour n'importe quelle compagnie.
        // Les Admin compagnie peuvent créer des abonnements, mais uniquement pour leur propre compagnie (logique dans store).
        // Si vous voulez que SEUL l'Admin général puisse accéder au formulaire de création, ajoutez :
        // if (!Auth::user()->isAdmin()) {
        //     abort(403, 'Accès non autorisé. Seul un administrateur général peut créer des abonnements.');
        // }

        $typesAbonement = TypeAbonement::all();
        $compagnies = Compagnies::all(); // Toutes les compagnies pour l'Admin général

        // Si l'utilisateur est Admin compagnie, il ne voit que sa compagnie
        if (Auth::user()->profil->name === 'Admin compagnie') {
            if (!Auth::user()->compagnie) {
                abort(403, 'Votre compte administrateur de compagnie n\'est pas associé à une compagnie.');
            }
            $compagnies = Compagnies::where('id', Auth::user()->compagnie->id)->get();
        }

        return view('back.abonements.create', compact('typesAbonement', 'compagnies'));
    }

    public function store(StoreAbonementRequest $request)
{
    $data = $request->validated();
    $user = Auth::user();

    if (!$user->isAdmin()) {
        if (!$user->compagnie) {
            abort(403, 'Vous n\'êtes associé à aucune compagnie pour créer un abonnement.');
        }
        $data['idCompagnie'] = $user->compagnie->id;
    }

    // Définir une durée par défaut si elle n'est pas fournie
    $duree = isset($data['duree']) ? (int) $data['duree'] : Abonement::DUREE_PAR_DEFAUT;

    $abonnement = Abonement::create([
        'idTypeAbonement' => $data['idTypeAbonement'],
        'idCompagnie' => $data['idCompagnie'],
        'duree' => $duree,
        'dateDebut' => Carbon::now(),
        'dateFin' => Carbon::now()->addDays($duree)
    ]);

    return redirect()->route('abonnements.index')
                     ->with('success', 'Abonnement créé avec succès');
}


    public function show(Abonement $abonement)
    {
        $user = Auth::user();
        // Un Admin général peut voir tous les abonnements.
        // Un Admin compagnie peut voir seulement les abonnements de sa compagnie.
        if (!$user->isAdmin() && ($user->profil->name === 'Admin compagnie' && $abonement->idCompagnie !== $user->compagnie->id)) {
            abort(403, 'Accès non autorisé. Vous ne pouvez voir que les abonnements de votre compagnie.');
        }

        return view('back.abonements.show', compact('abonement'));
    }

    public function edit(Abonement $abonnement)
    {
        // Seul l'Admin général peut éditer un abonnement.
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Accès non autorisé. Seul un administrateur général peut modifier un abonnement.');
        }

        $typesAbonement = TypeAbonement::all();
        $compagnies = Compagnies::all();

        return view('back.abonements.create', compact('abonnement', 'typesAbonement', 'compagnies'));
    }

    public function update(UpdateAbonementRequest $request, Abonement $abonnement)
    {
        // Seul l'Admin général peut mettre à jour un abonnement.
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Accès non autorisé. Seul un administrateur général peut mettre à jour un abonnement.');
        }

        $data = $request->validated();

        if (isset($data['duree'])) {
            $abonnement->dateFin = Carbon::parse($abonnement->dateDebut)->addDays((int)$data['duree']);
            $abonnement->duree = (int)$data['duree'];
        }

        $abonnement->idTypeAbonement = $data['idTypeAbonement'];

        // L'idCompagnie ne peut être modifié que par un Admin général
        if (Auth::user()->isAdmin() && isset($data['idCompagnie'])) {
            $abonnement->idCompagnie = $data['idCompagnie'];
        }

        $abonnement->save();

        return redirect()->route('abonnements.index')
                         ->with('success', 'Abonnement mis à jour avec succès');
    }

   public function destroy(Abonement $abonnement)
{
    // Seul l'Admin général peut supprimer un abonnement.
    if (!Auth::user()->isAdmin()) {
        abort(403, 'Accès non autorisé. Seul un administrateur général peut supprimer un abonnement.');
    }

    // --- Ajoutez cette ligne pour le débogage ---
    \Log::info('Tentative de suppression de l\'abonnement ID: ' . $abonnement->id);
    //dd('Abonnement prêt à être supprimé. ID: ' . $abonnement->id);
    // --- Fin du débogage ---

    $abonnement->delete();

    return to_route('abonnements.index')
                         ->with('success', 'Abonnement supprimé avec succès');
}

    public function renew(Abonement $abonement)
    {
        // Seul l'Admin général peut renouveler un abonnement.
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Accès non autorisé. Seul un administrateur général peut renouveler un abonnement.');
        }

        $newAbonnement = $abonement->replicate();
        $newAbonnement->dateDebut = Carbon::now(); // Correction de dateDbut à dateDebut
        $newAbonnement->dateFin = Carbon::now()->addDays($abonement->duree);
        $newAbonnement->save();

        return redirect()->route('abonnements.index')
                         ->with('success', 'Abonnement renouvelé avec succès');
    }
}
