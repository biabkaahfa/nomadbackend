@extends('back.app')

@section('title', isset($trajet) ? 'Modifier un Trajet' : 'Ajouter un Trajet')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($trajet) ? 'Modifier' : 'Ajouter' }} un Trajet

    </h3>
@endsection

@section('dashboard-content')
    <form action="{{ isset($trajet) ? route('trajets.update', $trajet->id) : route('trajets.store') }}" method="POST">
        @csrf
        @if(isset($trajet))
            @method('PUT')
        @endif

        <input type="text" name="pointDepart" placeholder="Départ" class="form-control mb-3"
            value="{{ old('pointDepart', $trajet->pointDepart ?? '') }}">

        <input type="text" name="pointArrive" placeholder="Arrivée" class="form-control mb-3"
            value="{{ old('pointArrive', $trajet->pointArrive ?? '') }}">

        <input type="number" name="prix" placeholder="Prix" class="form-control mb-3"
            value="{{ old('prix', $trajet->prix ?? '') }}">



        <select name="status" class="form-control mb-3">
            <option value="ACTIF" {{ (old('status', $trajet->status ?? '') == 'ACTIF') ? 'selected' : '' }}>ACTIF</option>
            <option value="INACTIF" {{ (old('status', $trajet->status ?? '') == 'INACTIF') ? 'selected' : '' }}>INACTIF</option>
        </select>

          <input type="number" name="distance" placeholder="distance" class="form-control mb-3"
            value="{{ old('distance', $trajet->distance ?? '') }}">

         <select name="idCompagnie" class="form-control mb-3" required>
    <option value="">-- Choisir une compagnie --</option>
    @foreach($compagnies as $compagnie)
        <option value="{{ $compagnie->id }}"
            {{ old('idCompagnie', $bus->idCompagnie ?? '') == $compagnie->id ? 'selected' : '' }}>
            {{ $compagnie->name }}
        </option>
    @endforeach
</select>
          <select name="idFrequence" class="form-control mb-3" required>
    <option value="">-- Choisir une fréquence --</option>
    @foreach($frequences as $frequence)
        <option value="{{ $frequence->id }}"
            {{ old('idFrequence', $trajet->idFrequence ?? '') == $frequence->id ? 'selected' : '' }}>
            {{ $frequence->jourSemaine }} à {{ \Carbon\Carbon::parse($frequence->heureDepart)->format('H:i') }}
        </option>
    @endforeach
</select>

@error('idFrequence')
    <p class="text-danger mt-1">{{ $message }}</p>
@enderror


        <button class="btn btn-primary">
            {{ isset($trajet) ? 'Mettre à jour' : 'Créer' }}
        </button>
{{--
        @if(isset($trajet))
            <a href="#" class="btn btn-danger ml-2" onclick="event.preventDefault(); if(confirm('Supprimer ce trajet ?')) document.getElementById('delete-form').submit();">
                Supprimer
            </a>

            <form id="delete-form" action="{{ route('trajets.destroy', $trajet->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif --}}
    </form>
@endsection
