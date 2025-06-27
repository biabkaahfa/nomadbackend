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

        <input type="number" name="nombrePlaces" class="form-control mb-3" placeholder="Nombre total de places"
            value="{{ old('nombrePlaces', $bus->nombrePlaces ?? '') }}">

        <input type="number" name="nombrePlaceDispo" class="form-control mb-3" placeholder="Places disponibles"
            value="{{ old('nombrePlaceDispo', $bus->nombrePlaceDispo ?? '') }}">

        <button class="btn btn-primary">
            {{ isset($bus) ? 'Mettre à jour' : 'Ajouter' }}
        </button>

        @if(isset($bus))
            <a href="#" class="btn btn-danger ml-2" onclick="event.preventDefault(); if(confirm('Supprimer ce bus ?')) document.getElementById('delete-form').submit();">
                Supprimer
            </a>

            <form id="delete-form" action="{{ route('buses.destroy', $bus->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </form>
@endsection
