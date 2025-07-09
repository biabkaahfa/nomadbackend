@extends('back.app')

@section('title', isset($voyage) ? 'Modifier un Voyage' : 'Créer un Voyage')

@section('dashboard-header')
    <h3 class="page-title mt-5">
       Affectation  de Bus au Voyage
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
    <form action="{{ route('voyages.affecterBus', $voyage->id) }}" method="POST">
    {{-- @csrf
    @method('PUT') --}}


        @csrf
        @if(isset($voyage))
            @method('PUT')
        @endif

        {{--  Heure de départ --}}
      <input type="time" class="form-control mb-3" value="{{ old('heuresDepart', isset($voyage) ? substr($voyage->heuresDepart, 0, 5) : '') }}" style="font-size: 1.3rem; padding: 1rem; height: auto;" disabled>
<input type="hidden" name="heuresDepart" value="{{ old('heuresDepart', isset($voyage) ? substr($voyage->heuresDepart, 0, 5) : '') }}">



        {{-- Date de départ --}}
     <input type="date" class="form-control mb-3" value="{{ old('dateDepart', $voyage->dateDepart ?? '') }}" disabled style="font-size: 1.3rem; padding: 1rem; height: auto;">
<input type="hidden" name="dateDepart" value="{{ old('dateDepart', $voyage->dateDepart ?? '') }}">


         {{-- Trajet
        <select name="idTrajet" required class="form-control mb-3" readonly>
            <option value="">-- Choisir un trajet --</option>
            @foreach($trajets as $trajet)
                <option value="{{ $trajet->id }}"
                    {{ old('idTrajet', $voyage->idTrajet ?? '') == $trajet->id ? 'selected' : '' }}>
                    {{ $trajet->pointDepart }} → {{ $trajet->pointArrive }}
                </option>
            @endforeach
        </select> --}}

        {{-- 🚌 Bus --}}
        <select class="form-control mb-3" disabled style="font-size: 1.3rem; padding: 1rem; height: auto;">
    <option value="">-- Choisir un trajet --</option>
    @foreach($trajets as $trajet)
        <option value="{{ $trajet->id }}"
            {{ old('idTrajet', $voyage->idTrajet ?? '') == $trajet->id ? 'selected' : '' }}>
            {{ $trajet->pointDepart }} → {{ $trajet->pointArrive }}
        </option>
    @endforeach
</select>
<input type="hidden" name="idTrajet" value="{{ old('idTrajet', $voyage->idTrajet ?? '') }}">

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
            Affecter
        </button>
    </form>
@endsection
