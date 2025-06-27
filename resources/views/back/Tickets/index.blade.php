@extends('back.app')
@section('title', 'Tickets')
@section('dashboard-header')
<h4 class="mt-5">Liste des tickets achetés</h4>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Trajet</th>
                    <th>Date réservation</th>
                    <th>Statut</th>
                    <th>Scan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->user->name ?? '-' }}</td>
                    <td>{{ $ticket->voyage->trajet->pointDepart ?? '' }} → {{ $ticket->voyage->trajet->pointArrive ?? '' }}</td>
                    <td>{{ $ticket->dateReservation }}</td>
                    <td>{{ $ticket->statut }}</td>
                    <td>{{ $ticket->dateScan ?? 'Non scanné' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
