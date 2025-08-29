<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\Profil;
use App\Models\Garre;
use App\Models\Compagnie;
use App\Models\Compagnies;
use App\Models\Garres;
use App\Models\Profils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = User::with(['profil', 'garre', 'compagnie']);
           // $user=auth()->user();

            // Filtrage par recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%");
                });
            }

            // Filtrage par profil
            if ($request->filled('profil')) {
                $query->where('idProfil', $request->profil);
            }

            // Filtrage par statut
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }

            // Filtrage par gare
            if ($request->filled('garre')) {
                $query->where('idGarre', $request->garre);
            }

            // Filtrage par compagnie
            if ($request->filled('compagnie')) {
                $query->where('idCompagnie', $request->compagnie);
            }

            $users = $query->paginate(15);


        //      if ($user->profil->name === 'Admin général') {
        //     // Peut tout voir
        //     $profils = Profils::all();
        //     $garres = Garres::all();
        //     $users=User::all();
        //     $compagnies = Compagnies::all();
        // }

        // elseif ($user->profil->name === 'Admin compagnie') {
        //     // Peut créer profils en dessous de lui
        //     $profils = Profils::whereNotIn('name','Admin général')->get();

        //     // Peut affecter uniquement à sa compagnie
        //     $compagnies = Compagnies::where('id', $user->idCompagnie)->get();

        //     // Peut voir toutes les gares de sa compagnie
        //     $garres = Garres::where('idCompagnie', $user->idCompagnie)->get();
        //     $users = User::where('idCompagnie', $user->idCompagnie)->get();
        // }

        // elseif ($user->profil->name === 'Chef de gare') {
        //     // Peut créer uniquement des profils en dessous
        //     $profils = Profils::whereNotIn('name', ['Admin général','Client'])->get();
        //      //$profils = Profils::where('name', ['Réceptionniste', 'Contrôleur'])->get();

        //     // Ne peut pas affecter à une compagnie
        //      $compagnies = Compagnies::where('id', $user->idCompagnie)->get();
        //    // $compagnies = collect(); // Vide ou tu peux omettre ce champ côté vue

        //     // Peut affecter uniquement à sa gare
        //     $garres = Garres::where('id', $user->idGarre)->get();
        //      $users = User::where('idGarre', $user->idGarre)->get();
        // }


            // Récupérer les données pour les filtres (noms de modèles corrigés)
            $profils = Profils::all();
            $garres = Garres::all();
            $compagnies = Compagnies::all();

            return view('back.users.index', compact('users', 'profils', 'garres', 'compagnies'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         try {
        $user = auth()->user();

        // Tous les profils, compagnies et gares par défaut
        $profils = collect();
        $garres = collect();
        $compagnies = collect();

        if ($user->profil->name === 'Admin général') {
            // Peut tout voir
            $profils = Profils::all();
            $garres = Garres::all();

            $compagnies = Compagnies::all();
        }

        elseif ($user->profil->name === 'Admin compagnie') {
            // Peut créer profils en dessous de lui
            $profils = Profils::whereNotIn('name', ['Admin général', 'Admin compagnie'])->get();

            // Peut affecter uniquement à sa compagnie
            $compagnies = Compagnies::where('id', $user->idCompagnie)->get();

            // Peut voir toutes les gares de sa compagnie
            $garres = Garres::where('idCompagnie', $user->idCompagnie)->get();
        }

        elseif ($user->profil->name === 'Chef de gare') {
            // Peut créer uniquement des profils en dessous
            $profils = Profils::whereNotIn('name', ['Admin général', 'Admin compagnie', 'Chef de gare','Client'])->get();
             //$profils = Profils::where('name', ['Réceptionniste', 'Contrôleur'])->get();

            // Ne peut pas affecter à une compagnie
             $compagnies = Compagnies::where('id', $user->idCompagnie)->get();
           // $compagnies = collect(); // Vide ou tu peux omettre ce champ côté vue

            // Peut affecter uniquement à sa gare
            $garres = Garres::where('id', $user->idGarre)->get();
        }

        return view('back.users.create', [
            'mode' => 'create',
            'profils' => $profils,
            'garres' => $garres,
            'compagnies' => $compagnies
        ]);
    } catch (\Exception $e) {
        \Log::error('Erreur lors du chargement du formulaire de création: ' . $e->getMessage());
        abort(500, 'Erreur interne.');
    }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Hash du mot de passe
            $validated['password'] = Hash::make($validated['password']);

            // Gestion de l'upload d'image
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('users', 'public');
            }

            // Statut par défaut
            $validated['statut'] = 'actif';

            $user = User::create($validated);

            DB::commit();

            return redirect()->route('user.index')
                ->with('success', 'Utilisateur créé avec succès.');

    //         return redirect('/user')
    // ->with('success', 'Utilisateur créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());

            // Supprimer l'image uploadée en cas d'erreur
            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'utilisateur.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            $user->load(['profil', 'garre', 'compagnie']);

            return view('back.users.create', [
                'mode' => 'show',
                'user' => $user,
                'profils' => Profils::all(),
                'garres' => Garres::all(),
                'compagnies' => Compagnies::all()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage de l\'utilisateur: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        try {
            return view('back.users.create', [
                'mode' => 'edit',
                'user' => $user,
                'profils' => Profils::all(),
                'garres' => Garres::all(),
                'compagnies' => Compagnies::all()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du formulaire d\'édition: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Hash du mot de passe si fourni
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Gestion de l'upload d'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $validated['image'] = $request->file('image')->store('users', 'public');
            }

            $user->update($validated);

            DB::commit();

            return redirect()->route('user.index')
                ->with('success', 'Utilisateur mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage());

            // Supprimer la nouvelle image en cas d'erreur
            if (isset($validated['image']) && $validated['image'] !== $user->image) {
                Storage::disk('public')->delete($validated['image']);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'utilisateur.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Supprimer l'image si elle existe
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $user->delete();

            DB::commit();

            return redirect()->route('user.index')
                ->with('success', 'Utilisateur supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'utilisateur: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de l\'utilisateur.');
        }
    }

    /**
     * Activer/désactiver un utilisateur
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        try {
            $user->update([
                'statut' => $user->statut === 'actif' ? 'inactif' : 'actif'
            ]);

            $status = $user->statut === 'actif' ? 'activé' : 'désactivé';

            return redirect()->back()
                ->with('success', "Utilisateur {$status} avec succès.");

        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors du changement de statut.');
        }
    }

    /**
     * Recherche d'utilisateurs (pour AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');

            if (empty($query) || strlen($query) < 2) {
                return response()->json([]);
            }

            $users = User::where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->with('profil')
                        ->limit(10)
                        ->get();

            return response()->json($users);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la recherche'], 500);
        }
    }
}
