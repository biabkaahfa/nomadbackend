@extends('back.app')

@section('title', 'Tickets')


@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Liste des tickets achetés</h4>
                
                <a href="{{ route('tickets.create') }}" class="btn btn-primary float-right viewbutton">Acheter un ticket</a>
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
                    <th>Client</th>
                    <th>Mode d'achat</th>
                    <th>Trajet</th>
                    <th>Date réservation</th>
                    <th>Statut</th>
                    <th>Scan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->user->name ?? $ticket->name ?? '-' }}</td>
                    <td>{{ ucfirst($ticket->typeAchat) }}</td>
                    <td>{{ $ticket->voyage->trajet->pointDepart ?? '' }} → {{ $ticket->voyage->trajet->pointArrive ?? '' }}</td>
                    <td>{{ \Carbon\Carbon::parse($ticket->dateReservation)->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $ticket->statut === 'CONFIRME' ? 'success' : ($ticket->statut === 'ANNULE' ? 'danger' : 'secondary') }}">
                            {{ $ticket->statut }}
                        </span>
                    </td>
                    <td>{{ $ticket->dateScan ? \Carbon\Carbon::parse($ticket->dateScan)->format('d/m/Y') : 'Non scanné' }}</td>
                    <td>
                        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-warning">Modifier</a>

                        <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Supprimer ce ticket ?');">
                            @csrf
                            @method('DELETE')
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
