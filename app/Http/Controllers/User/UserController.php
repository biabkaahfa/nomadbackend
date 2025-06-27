<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\Profil;
use App\Models\Garre;
use App\Models\Compagnie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = User::with(['profil', 'garre', 'compagnie']);

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
        
      

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
       

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Hash du mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('users', 'public');
        }

        $user = User::create($validated);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load(['profil', 'garre', 'compagnie', 'reservations.voyage', 'tickets']);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
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

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Supprimer l'image si elle existe
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Activer/désactiver un utilisateur
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update([
            'statut' => $user->statut === 'actif' ? 'inactif' : 'actif'
        ]);

        $status = $user->statut === 'actif' ? 'activé' : 'désactivé';
        
        return redirect()
            ->back()
            ->with('success', "Utilisateur {$status} avec succès.");
    }

  
    /**
     * Recherche d'utilisateurs (pour AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->with('profil')
                    ->limit(10)
                    ->get();

        return response()->json($users);
    }

   
}