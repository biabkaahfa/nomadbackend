@extends('back.app')

@section('title', isset($voyage) ? 'Modifier un Voyage' : 'Créer un Voyage')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($voyage) ? 'Modifier' : 'Créer' }} un Voyage
    </h3>
@endsection

@section('dashboard-content')

    {{-- ✅ Affichage des erreurs --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ Formulaire --}}
    <form action="{{ isset($voyage) ? route('voyages.update', $voyage->id) : route('voyages.store') }}" method="POST">
        @csrf
        @if(isset($voyage))
            @method('PUT')
        @endif

        {{-- 🕐 Heure de départ --}}
       <input type="time" name="heuresDepart" required class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;"
    value="{{ old('heuresDepart', isset($voyage) ? substr($voyage->heuresDepart, 0, 5) : '') }}">


        {{-- 📅 Date de départ --}}
        <input type="date" name="dateDepart" required class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;"
            min="{{ date('Y-m-d') }}"
            value="{{ old('dateDepart', $voyage->dateDepart ?? '') }}">

        {{-- 🚏 Trajet --}}
        <select name="idTrajet" required class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;">
            <option value="">-- Choisir un trajet --</option>
            @foreach($trajets as $trajet)
                <option value="{{ $trajet->id }}"
                    {{ old('idTrajet', $voyage->idTrajet ?? '') == $trajet->id ? 'selected' : '' }}>
                    {{ $trajet->pointDepart }} → {{ $trajet->pointArrive }}
                </option>
            @endforeach
        </select>

        {{-- 🚌 Bus --}}
        <select name="idBus" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;">
            <option value="">-- Choisir un bus --</option>
            @foreach($buses as $bus)
                <option value="{{ $bus->id }}"
                    {{ old('idBus', $voyage->idBus ?? '') == $bus->id ? 'selected' : '' }}>
                    Bus N°{{ $bus->numeroBus }}
                </option>
            @endforeach
        </select>

        {{-- ✅ Bouton --}}
        <button type="submit" class="btn btn-primary">
            {{ isset($voyage) ? 'Mettre à jour' : 'Valider' }}
        </button>
    </form>
@endsection
