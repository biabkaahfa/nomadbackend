@extends('back.app')

@section('title', isset($voyage) ? 'Modifier un Voyage' : 'Créer un Voyage')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($voyage) ? 'Modifier' : 'Créer' }} un Voyage
    </h3>
@endsection

@section('dashboard-content')
@php
    $actionUrl = isset($voyage) ? route('voyages.update', $voyage->id) : route('voyages.store');
@endphp

<div class="alert alert-info">
    Méthode : <strong>POST</strong><br>
    Action du formulaire : <strong>{{ $actionUrl }}</strong><br>
    Formulaire de type : <strong>{{ isset($voyage) ? 'UPDATE' : 'CREATE' }}</strong>
</div>

    <form action="{{ isset($voyage) ? route('voyages.update', $voyage->id) : route('voyages.store') }}" method="POST">
        @csrf
      @if(isset($voyage))
    <input type="text" class="form-control mb-3" readonly value="PUT => update()" />
    @method('PUT')
@endif


        <input type="time" name="heuresDepart" class="form-control mb-3"
            value="{{ old('heuresDepart', $voyage->heuresDepart ?? '') }}">

        <input type="date" name="dateDepart" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;"
    min="{{ date('Y-m-d') }}"
    value="{{ old('dateDepart', $voyage->dateDepart ?? '') }}">

       <select name="idTrajet" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" required>
    <option value="">-- Choisir un trajet --</option>
    @foreach ($trajets as $trajet)
        <option value="{{ $trajet->id }}"
            {{ old('idTrajet', $voyage->idTrajet ?? '') == $trajet->id ? 'selected' : '' }}>
            {{ $trajet->pointDepart }} → {{ $trajet->pointArrive }}
        </option>
    @endforeach
</select>
@error('idTrajet')
    <p class="text-danger mt-1">{{ $message }}</p>
@enderror


         <select name="idBus" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;">
    <option value="">-- Choisir un bus --</option>
    @foreach($buses as $bus)
        <option value="{{ $bus->id }}"
            {{ old('idBus', $voyage->idBus ?? '') == $bus->id ? 'selected' : '' }}>
            Bus N°{{ $bus->numeroBus }}
        </option>
    @endforeach
</select>

        <button type="submit" class="btn btn-primary">
            {{ isset($voyage) ? 'Mettre à jour' : 'Valider' }}
        </button>
{{--
       <a href="{{ route('voyages.update', $voyage->id) }}" class="btn btn-sm btn-primary">
        Modifier
    </a> --}}


    </form>
@endsection
