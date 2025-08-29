<?php

namespace App\Http\Controllers\TypeAbonements;



// namespace App\Http\Controllers\Abonements;

// namespace App\Http\Controllers\Compagnies;

// use App\Models\Compagnies;
use App\Http\Controllers\Controller;

//use App\Models\Abonement; // Capitalisé pour suivre les conventions
// use App\Models\TypeAbonement; // Capitalisé
// use App\Models\Compagnies; // Singulier et capitalisé
// use App\Http\Requests\Abonements\StoreAbonementRequest;
// use App\Http\Requests\Abonements\UpdateAbonementRequest;
 use Illuminate\Support\Facades\Auth;
//use Carbon\Carbon;

use App\Models\typeAbonement;
use App\Http\Requests\TypeAbonements\StoretypeAbonementRequest;
use App\Http\Requests\TypeAbonements\UpdatetypeAbonementRequest;
// use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TypeAbonementController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Par défaut, tous les TypeAbonement sont visibles,
        // car un TypeAbonement n'appartient pas directement à une compagnie.
        // Seuls les abonnements sont liés aux compagnies.
        $types = TypeAbonement::latest()->get();

        // Si, contre toute attente, un Admin Compagnie ne devrait voir QUE
        // les types d'abonnements qui sont effectivement utilisés par sa compagnie,
        // alors la logique serait plus complexe (voir Option 2).
        // Cependant, il est plus probable qu'ils aient besoin de voir tous les types disponibles
        // pour créer de nouveaux abonnements par exemple.

        return view('back.typeAbonement.index', compact('types'));
    }

    public function create()
    {
        return view('back.typeAbonement.create');
    }
    // public function create() // Pas besoin de type hint View ici si vous ne le voulez pas
    // {
    //     // Crée une nouvelle instance vide du modèle TypeAbonement
    //     $typeAbonement = new TypeAbonement();

    //     // Passe cette instance à la vue
    //     return view('back.typeAbonement.create', compact('typeAbonement'));
    // }
//     public function create()
// {
//     $typeAbonement = new \App\Models\TypeAbonement(); // Assurez-vous d'avoir le bon namespace pour TypeAbonement
//     return view('back.typeAbonement.create', compact('typeAbonement'));
// }

public function edit(\App\Models\TypeAbonement $typeAbonement) // Et la méthode edit() aussi
{
    return view('back.typeAbonement.create', compact('typeAbonement'));
}

    public function store(StoreTypeAbonementRequest $request)
    {
        TypeAbonement::create($request->validated());
        return redirect()->route('type-abonements.index')
                         ->with('success', 'Type d\'abonnement créé avec succès');
    }

    public function show(TypeAbonement $typeAbonement)
    {
        // return view('back.typeAbonement.show', compact('typeAbonement'));
    }

    // public function edit(TypeAbonement $typeAbonement)
    // {
    //     return view('back.typeAbonement.create', compact('typeAbonement'));
    // }

    public function update(UpdateTypeAbonementRequest $request, TypeAbonement $typeAbonement)
    {
        $typeAbonement->update($request->validated());
        return redirect()->route('type-abonements.index')
                         ->with('success', 'Type d\'abonnement mis à jour');
    }

    public function destroy(TypeAbonement $typeAbonement)
    {
        $typeAbonement->delete();
        return redirect()->route('type-abonements.index')
                         ->with('success', 'Type d\'abonnement supprimé');
    }
}
