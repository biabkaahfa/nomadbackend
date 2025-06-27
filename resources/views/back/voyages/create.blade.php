@extends('back.app')

@section('title', isset($voyage) ? 'Modifier un Voyage' : 'Créer un Voyage')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($voyage) ? 'Modifier' : 'Créer' }} un Voyage
    </h3>
@endsection

@section('dashboard-content')
    <form action="{{ isset($voyage) ? route('voyages.update', $voyage->id) : route('voyages.store') }}" method="POST">
        @csrf
        @if(isset($voyage))
            @method('PUT')
        @endif

        <input type="time" name="heuresDepart" class="form-control mb-3"
            value="{{ old('heuresDepart', $voyage->heuresDepart ?? '') }}">

        <input type="date" name="dateDepart" class="form-control mb-3"
            value="{{ old('dateDepart', $voyage->dateDepart ?? '') }}">

        <input type="number" name="idTrajet" placeholder="ID Trajet" class="form-control mb-3"
            value="{{ old('idTrajet', $voyage->idTrajet ?? '') }}">

        <input type="number" name="idBus" placeholder="ID Bus (facultatif)" class="form-control mb-3"
            value="{{ old('idBus', $voyage->idBus ?? '') }}">

        <button class="btn btn-primary">
            {{ isset($voyage) ? 'Mettre à jour' : 'Valider' }}
        </button>

        @if(isset($voyage))
            <a href="#" class="btn btn-danger ml-2" onclick="event.preventDefault(); if(confirm('Supprimer ce voyage ?')) document.getElementById('delete-form').submit();">
                Supprimer
            </a>

            <form id="delete-form" action="{{ route('voyages.destroy', $voyage->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </form>
@endsection
