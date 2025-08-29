<?php

namespace App\Http\Controllers;

use App\Models\Parametres;
use Illuminate\Http\Request;
use App\Http\Requests\StoreParametresRequest;
use App\Http\Requests\UpdateParametresRequest;

class ParametresController extends Controller
{
    public function index()
{
    if (auth()->user()->profil->name !== 'Admin général') {
        abort(403, 'Accès refusé.');
    }

    $globalParametres = Parametres::whereNull('idCompagnie')->get();
    $companyParametres = Parametres::whereNotNull('idCompagnie')->get();

    return view('back.parametre.index', compact('globalParametres', 'companyParametres'));
}


    public function create()
    {
        if (!auth()->user()->profil->name === 'Admin général') {
            abort(403, 'Accès refusé.');
        }

        return view('back.parametre.create');
    }

public function store(Request $request)
{
    $user = auth()->user();

    // Validation
    $data = $request->validate([
        'logo' => 'nullable|image|max:2048',
        'couleur_principale' => 'required|string|max:255',
        'couleur_secondaire' => 'required|string|max:255',
        'slogan' => 'nullable|string|max:255',
    ]);

    // Gestion du logo
    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('logos', 'public');
    }

    // Règles de création
    if ($user->profil->name === 'Admin général') {
        // L'admin général peut créer un paramètre global (idCompagnie null)
        $existeGlobal = Parametres::whereNull('idCompagnie')->exists();
        if ($existeGlobal) {
            return back()->withErrors("Les paramètres globaux existent déjà.");
        }
        $data['idCompagnie'] = null;
    } else {
        // Les autres DOIVENT avoir un idCompagnie
        $data['idCompagnie'] = $user->idCompagnie;

        $existe = Parametres::where('idCompagnie', $user->idCompagnie)->exists();
        if ($existe) {
            return back()->withErrors("Les paramètres de votre compagnie existent déjà.");
        }
    }

    Parametres::create($data);

    return redirect()->back()->with('success', 'Paramètres enregistrés.');
}



    public function show(Parametres $parametre)
    {
        return view('back.parametre.show', compact('parametre'));
    }

    public function edit(Parametres $parametre)
    {
        return view('back.parametre.edit', compact('parametre'));
    }

    public function update(Request $request, $id)
    {
        $parametres = Parametres::findOrFail($id);

        $data = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'couleur_principale' => 'required|string|max:255',
            'couleur_secondaire' => 'required|string|max:255',
            'slogan' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $parametres->update($data);

        return redirect()->back()->with('success', 'Paramètres mis à jour.');
    }

    public function destroy(Parametres $parametres)
    {
        if (!auth()->user()->profil->name === 'Admin général') {
            abort(403, 'Accès refusé.');
        }

        $parametres->delete();
        return redirect()->back()->with('success', 'Paramètres supprimés.');
    }
}
