@extends('back.app')

@section('title', 'Affectation des trajets aux gares')

@section('dashboard-header')
    <h3 class="page-title mt-5">Affecter les trajets aux gares</h3>
@endsection

@section('dashboard-content')
    <form method="POST" action="{{ route('affectation.store') }}">
        @csrf

        @foreach ($garres as $garre)
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ $garre->name }}</h5>
                </div>
                <div class="card-body">
                    @foreach ($trajets as $trajet)
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="affectations[{{ $garre->id }}][]"
                                   value="{{ $trajet->id }}"
                                   id="garre{{ $garre->id }}-trajet{{ $trajet->id }}"
                                   {{ in_array($trajet->id, $garre->trajets->pluck('id')->toArray()) ? 'checked' : '' }}>
                            <label class="form-check-label" for="garre{{ $garre->id }}-trajet{{ $trajet->id }}">
                                {{ $trajet->pointDepart }} → {{ $trajet->pointArrive }} ({{ $trajet->prix }} FCFA)
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Enregistrer les affectations</button>
    </form>
@endsection
