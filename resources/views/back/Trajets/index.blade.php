@extends('back.app')
@section('title', 'Trajets')
@section('dashboard-header')
<h4 class="mt-5">Liste des trajets</h4>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
