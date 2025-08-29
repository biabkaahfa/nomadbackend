@extends('back.app')

@section('title', isset($personalisationCard) ? 'Modifier la Personnalisation' : 'Créer une Personnalisation de Carte')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">
                    {{-- Le titre est dynamique selon la présence de l'objet $personalisationCard --}}
                    {{ isset($personalisationCard) ? 'Modifier la Personnalisation' : 'Créer une Personnalisation' }}
                </h4>
                {{-- Lien vers la liste des personnalisations de cartes. La route a été corrigée pour plus de cohérence. --}}
                <a href="{{ route('personalisationCard.index') }}" class="btn btn-secondary float-right viewbutton">
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-sm-12">

            {{-- Section pour afficher les messages de succès ou d'erreur. --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Le formulaire principal pour la création ou la modification --}}
            <div class="card">
                <div class="card-body">
                    {{--
                        L'action du formulaire est dynamique :
                        - Si $personalisationCard existe, l'action est la route 'update' avec l'ID de l'objet.
                        - Sinon, l'action est la route 'store' pour la création.
                    --}}
                    <form action="{{ isset($personalisationCard) ? route('personalisationCard.update', $personalisationCard) : route('personalisationCard.store') }}" method="POST">
                        @csrf
                        {{-- Si nous sommes en mode modification, nous ajoutons la directive @method('PUT') --}}
                        @if(isset($personalisationCard))
                            @method('PUT')
                        @endif

                        {{-- Champs du formulaire. old() est utilisé pour pré-remplir après une erreur de validation. --}}
                        <div class="form-group mb-3">
                            <label for="pays">Pays</label>
                            <input type="text" name="pays" id="pays" class="form-control" value="{{ old('pays', $personalisationCard->pays ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="devise">Devise</label>
                            <input type="text" name="devise" id="devise" class="form-control" value="{{ old('devise', $personalisationCard->devise ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="numero">Numéro</label>
                            <input type="text" name="numero" id="numero" class="form-control" value="{{ old('numero', $personalisationCard->numero ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="couleur_principale">Couleur principale</label>
                            <input type="color" name="couleur_principale" id="couleur_principale" class="form-control form-control-color" value="{{ old('couleur_principale', $personalisationCard->couleur_principale ?? '#000000') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="idCompagnie">Compagnie associée</label>
                            <select name="idCompagnie" id="idCompagnie" class="form-control" required>
                                <option value="">-- Sélectionner une compagnie --</option>
                                {{-- Boucle sur les compagnies pour créer les options du select --}}
                                @foreach ($compagnies as $compagnie)
                                    <option value="{{ $compagnie->id }}" {{ old('idCompagnie', $personalisationCard->idCompagnie ?? '') == $compagnie->id ? 'selected' : '' }}>
                                        {{ $compagnie->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Boutons du formulaire --}}
                        <button type="submit" class="btn btn-primary">
                            {{-- Le texte du bouton est aussi dynamique --}}
                            {{ isset($personalisationCard) ? 'Modifier' : 'Créer' }}
                        </button>
                        {{-- Lien d'annulation, avec la route corrigée --}}
                        <a href="{{ route('personalisationCard.index') }}" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
