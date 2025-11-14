<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Http\Request;

class profileUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function passwordUpdate(Request $request)
{
    $request->validate([
        'current_password'      => ['required'],
        'password'              => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = Auth::user();

    // Vérifie l'ancien mot de passe
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'L\'ancien mot de passe est incorrect.']);
    }

    // Met à jour le nouveau mot de passe
    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('status', 'Mot de passe mis à jour avec succès.');
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //

         return view('back.profileUser.edit', ['user' => $request->user()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            // Si une image est soumise
            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }

                $imagePath = $request->file('image')->store('users', 'public');
                $user->image = $imagePath;
            }

            $user->name  = $request->name;
            $user->email = $request->email;
            $user->save();

            return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour profil utilisateur: " . $e->getMessage());

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
         $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Supprimer l’image si elle existe
        if (!empty($user->image) && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');

    }
}
