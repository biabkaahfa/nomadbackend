@extends('back.app')

@section('title', isset($bus) ? 'Modifier un Bus' : 'Ajouter un Bus')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($bus) ? 'Modifier' : 'Ajouter' }} un Bus
    </h3>
@endsection

@section('dashboard-content')
    <form action="{{ isset($bus) ? route('buses.update', $bus->id) : route('buses.store') }}" method="POST">
        @csrf
        @if(isset($bus))
            @method('PUT')
        @endif

        <input type="number" name="numeroBus" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;" placeholder="Numéro du bus"
            value="{{ old('numeroBus', $bus->numeroBus ?? '') }}" min="1" required>

        <input type="number" name="nombrePlaces" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;" placeholder="Nombre total de places"
            value="{{ old('nombrePlaces', $bus->nombrePlaces ?? '') }}" min="0" required>

        <input type="number" name="nombrePlaceDispo" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;" placeholder="Places disponibles"
            value="{{ old('nombrePlaceDispo', $bus->nombrePlaceDispo ?? '') }}" min="1" required>

        <select name="idCompagnie" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;" required>
    <option value="">-- Choisir une compagnie --</option>
    @foreach($compagnies as $compagnie)
        <option value="{{ $compagnie->id }}"
            {{ old('idCompagnie', $bus->idCompagnie ?? '') == $compagnie->id ? 'selected' : '' }}>
            {{ $compagnie->name }}
        </option>
    @endforeach
</select>



        <select name="status" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;" required>
            <option value="">-- Statut du bus --</option>
            <option value="Actif" {{ old('status', $bus->status ?? '') == 'Actif' ? 'selected' : '' }}>Actif</option>
            <option value="Inactifs" {{ old('status', $bus->status ?? '') == 'Inactifs' ? 'selected' : '' }}>Inactif</option>
        </select>

        <button type="submit" class="btn btn-primary">
            {{ isset($bus) ? 'Mettre à jour' : 'Ajouter' }}
        </button>

        @if(isset($bus))
            <a href="#" class="btn btn-danger ml-2"
                onclick="event.preventDefault(); if(confirm('Supprimer ce bus ?')) document.getElementById('delete-form').submit();">
                Supprimer
            </a>

            <form id="delete-form" action="{{ route('buses.destroy', $bus->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </form>
@endsection
