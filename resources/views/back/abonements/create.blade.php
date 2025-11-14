@extends('back.app')

@section('title', isset($abonnement) ? 'Modifier un Abonnement' : 'Ajouter un Abonnement')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($abonnement) ? 'Modifier' : 'Ajouter' }} un Abonnement
    </h3>
@endsection

@section('dashboard-content')
    <form action="{{ isset($abonnement) ? route('abonnements.update', $abonnement) : route('abonnements.store') }}" method="POST">
    @csrf
    @if(isset($abonnement))
        @method('PUT')
    @endif

        <div class="form-group mb-3">
            <label for="idTypeAbonement">Type d'abonnement</label>
            <select name="idTypeAbonement" id="idTypeAbonement" class="form-control @error('idTypeAbonement') is-invalid @enderror" style="font-size: 1.3rem; padding: 1rem; height:auto;" required>
                <option value="">-- Choisir un type d'abonnement --</option>
                @foreach($typesAbonement as $type)
                    <option value="{{ $type->id }}"
                        {{ (old('idTypeAbonement', $abonnement->idTypeAbonement ?? '') == $type->id) ? 'selected' : '' }}>
                        {{ $type->nom }} ({{ $type->duree }} jours - {{ $type->prix }} €)
                    </option>
                @endforeach
            </select>
            @error('idTypeAbonement')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="duree">Durée personnalisée (en jours)</label>
            <input type="number" name="duree" id="duree" class="form-control @error('duree') is-invalid @enderror" style="font-size: 1.3rem; padding: 1rem; height:auto;"
                placeholder="Laissez vide pour la durée par défaut du type d'abonnement"
                value="{{ old('duree', $abonnement->duree ?? '') }}" min="1">
            @error('duree')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        @if(auth()->user()->isAdmin()) {{-- Utilisez la méthode isAdmin() de votre modèle User --}}
            <div class="form-group mb-3">
                <label for="idCompagnie">Compagnie</label>
                <select name="idCompagnie" id="idCompagnie" class="form-control @error('idCompagnie') is-invalid @enderror" style="font-size: 1.3rem; padding: 1rem; height:auto;" required>
                    <option value="">-- Choisir une compagnie --</option>
                    @foreach($compagnies as $compagnie)
                        <option value="{{ $compagnie->id }}"
                            {{ (old('idCompagnie', $abonnement->idCompagnie ?? '') == $compagnie->id) ? 'selected' : '' }}>
                            {{ $compagnie->name }}
                        </option>
                    @endforeach
                </select>
                @error('idCompagnie')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        @else {{-- Pour l'Admin Compagnie, la compagnie est cachée et pré-remplie --}}
            <input type="hidden" name="idCompagnie" value="{{ auth()->user()->compagnie->id ?? '' }}">
        @endif

        <button type="submit" class="btn btn-primary">
            {{ isset($abonnement) ? 'Mettre à jour' : 'Ajouter' }}
        </button>
        </form>

       @if(isset($abonnement))
    <form id="delete-form" action="{{ route('abonnements.destroy', $abonnement) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <button class="btn btn-danger mt-2"
        onclick="event.preventDefault(); if(confirm('Supprimer cet abonnement ?')) document.getElementById('delete-form').submit();">
        Supprimer
    </button>
@endif

@endsection
