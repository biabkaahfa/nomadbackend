@extends('back.app')

@section('title', 'Créer une fréquence de trajet')


@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($frequence) ? 'Modifier' : 'Ajouter' }}  Fréquence de Trajet
    </h3>
@endsection

@section('dashboard-content')
<form action="{{ isset($frequence) ? route('frequences.update', $frequence) : route('frequences.store') }}" method="POST">
                @csrf
                @if (isset($frequence))
                    @method('PUT')
                @endif

    <!-- Sélection du trajet -->
    <label for="idTrajet">Trajet</label>
    <select name="idTrajet" class="form-control mb-3" required>
        <option value="">-- Choisir un trajet --</option>
        @foreach($trajets as $trajet)
            <option value="{{ $trajet->id }}">
                {{ $trajet->pointDepart }} → {{ $trajet->pointArrive }}
            </option>
        @endforeach
    </select>

    <!-- Jour de la semaine -->
    <label for="jourSemaine">Jour de la semaine</label>
    <select name="jourSemaine" class="form-control mb-3" required>
        <option value="">-- Choisir un jour --</option>
        <option value="LUNDI">Lundi</option>
        <option value="MARDI">Mardi</option>
        <option value="MERCREDI">Mercredi</option>
        <option value="JEUDI">Jeudi</option>
        <option value="VENDREDI">Vendredi</option>
        <option value="SAMEDI">Samedi</option>
        <option value="DIMANCHE">Dimanche</option>
        <option value="CHAQUEJOURS">ChaqueJours</option>
    </select>

    <!-- Heure de départ -->
    <label for="heureDepart">Heure de départ</label>
    <input type="time" name="heureDepart" class="form-control mb-3" required>

    <!-- Nombre minimum de places -->
    <label for="nombrePlaceMinimum" min:1>Nombre de places minimum</label>
    <input type="number" name="nombrePlaceMinimum" class="form-control mb-3" required min="1">

     <button type="submit" class="btn btn-primary">
                    {{ isset($frequence) ? 'Mettre à jour' : 'Créer la Frequence' }}
                </button>
</form>
@endsection
