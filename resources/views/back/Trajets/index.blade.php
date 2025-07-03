@extends('back.app')
@section('title', 'Trajets')

@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <h4 class="card-title mt-5">Listes des Trajets</h4>
        <a href="{{ route('trajets.create') }}" class="btn btn-primary float-right">Ajouter un Trajet</a>
        <a href="{{ route('affectation.create') }}" class="btn btn-primary float-left">Affecter un trajet a une garre</a>
    </div>
</div>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Compagnie</th>
                    <th>Départ</th>
                    <th>Arrivée</th>
                    <th>Prix</th>
                    <th>Statut</th>
                     <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trajets as $trajet)
                <tr>
                    <td>{{ $trajet->compagnie->name ?? '-' }}</td>
                    <td>{{ $trajet->pointDepart }}</td>
                    <td>{{ $trajet->pointArrive }}</td>
                    <td>{{ $trajet->prix }} F</td>
                    <td>{{ $trajet->status }}</td>
                    <td>
                        <a href="{{ route('trajets.edit', $trajet) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form action="{{ route('trajets.destroy', $trajet) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
