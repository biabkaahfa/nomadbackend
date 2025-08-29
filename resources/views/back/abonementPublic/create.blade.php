@extends('back.app')

@section('title', 'Créer un Abonnement Public')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                {{-- Afficher le titre dynamiquement --}}
                <h4 class="card-title float-left mt-2">{{ isset($abonementPublic) ? 'Modifier l\'Abonnement Public' : 'Créer un Abonnement Public' }}</h4>
                <a href="{{ route('abonementPublic.index') }}" class="btn btn-secondary float-right viewbutton">
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-sm-12">

            {{-- ✅ Messages de succès ou erreur --}}
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

            {{-- ✅ Formulaire de création / modification --}}
            <div class="card">
                <div class="card-body">
                    {{-- L'attribut enctype est essentiel pour les formulaires de téléchargement de fichiers --}}
                    <form action="{{ isset($abonementPublic) ? route('abonementPublic.update', $abonementPublic->id) : route('abonementPublic.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($abonementPublic))
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label for="idCompagnie">Compagnie associée</label>
                            <select name="idCompagnie" id="idCompagnie" class="form-control" required>
                                <option value="">-- Sélectionner une compagnie --</option>
                                @foreach ($compagnies as $compagnie)
                                    <option value="{{ $compagnie->id }}" {{ (old('idCompagnie', $abonementPublic->idCompagnie ?? '') == $compagnie->id) ? 'selected' : '' }}>
                                        {{ $compagnie->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="nom">Nom</label>
                            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom', $abonementPublic->nom ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="prenom">Prénom</label>
                            <input type="text" name="prenom" id="prenom" class="form-control" value="{{ old('prenom', $abonementPublic->prenom ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="profession">Profession</label>
                            <input type="text" name="profession" id="profession" class="form-control" value="{{ old('profession', $abonementPublic->profession ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="etablissement">Établissement</label>
                            <input type="text" name="etablissement" id="etablissement" class="form-control" value="{{ old('etablissement', $abonementPublic->etablissement ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="dateNaiss">Date de naissance</label>
                            <input type="date" name="dateNaiss" id="dateNaiss" class="form-control" value="{{ old('dateNaiss', $abonementPublic->dateNaiss ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="duree">Durée (en jours)</label>
                            <input type="number" name="duree" id="duree" class="form-control" value="{{ old('duree', $abonementPublic->duree ?? 30) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="Photo">Fichier Photo</label>
                            {{-- Changement du type d'input pour permettre le téléchargement de fichier --}}
                            <input type="file" name="Photo" id="Photo" class="form-control">
                            @if(isset($abonementPublic) && $abonementPublic->Photo)
                                <small class="form-text text-muted mt-2">Photo actuelle:</small>
                                <img src="{{ asset('storage/' . $abonementPublic->Photo) }}" alt="Photo actuelle" class="img-thumbnail mt-2" style="max-width: 150px;">
                            @endif
                        </div>

                        {{-- Le champ idUser n'est plus nécessaire ici car il est géré dans le contrôleur --}}

                        <button type="submit" class="btn btn-primary">{{ isset($abonementPublic) ? 'Mettre à jour' : 'Créer l\'abonnement' }}</button>
                        <a href="{{ route('abonementPublic.index') }}" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
