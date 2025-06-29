@extends('back.app')
@section('title', 'Voyages')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Départs programmés</h4>
                
                <a href="{{ route('voyages.create') }}" class="btn btn-primary float-right viewbutton">Ajouter un Voyage</a>
            </div>
        </div>
    </div>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Trajet</th>
                    <th>Bus</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voyages as $voyage)
                <tr>
                    <td>{{ $voyage->dateDepart }}</td>
                    <td>{{ $voyage->heuresDepart }}</td>
                    <td>{{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}</td>
                    <td>{{ $voyage->bus->numeroBus ?? 'Non assigné' }}</td>
                  <td>
    <a href="{{ route('voyages.edit', $voyage->id) }}" class="btn btn-sm btn-primary">
        Modifier
    </a>

    <form action="{{ route('voyages.destroy', $voyage->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce voyage ?')">
            Supprimer
        </button>
    </form>
</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
