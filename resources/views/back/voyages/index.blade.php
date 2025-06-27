@extends('back.app')
@section('title', 'Voyages')
@section('dashboard-header')
<h4 class="mt-5">Départs programmés</h4>
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
                </tr>
            </thead>
            <tbody>
                @foreach($voyages as $voyage)
                <tr>
                    <td>{{ $voyage->dateDepart }}</td>
                    <td>{{ $voyage->heuresDepart }}</td>
                    <td>{{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}</td>
                    <td>{{ $voyage->bus->id ?? 'Non assigné' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
