@extends('back.app')

@section('title', 'Notes de voyages')

@section('dashboard-header')
<h4 class="mt-5">Statistiques des notes de voyages</h4>
@endsection

@section('dashboard-content')

{{-- Tableau Statistique --}}
<div class="card mb-4">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Voyage</th>
                    <th>Total de notes</th>
                    <th>Note moyenne</th>
                    <th>Note max</th>
                    <th>Note min</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats as $stat)
                <tr>
                    <td>{{ $stat->voyage }}</td>
                    <td>{{ $stat->total_notes }}</td>
                    <td>{{ number_format($stat->moyenne, 2) }}/5</td>
                    <td>{{ $stat->max_note }}/5</td>
                    <td>{{ $stat->min_note }}/5</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Affichage des 5 dernières notes par voyage --}}
@foreach($latestNotes as $voyageId => $notes)
<div class="card mb-4">
    <div class="card-header">
        <strong>Voyage : {{ $notes->first()->nomVoyage ?? 'N/A' }}</strong>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Note</th>
                    <th>Commentaire</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notes as $note)
                <tr>
                    <td>{{ $note->idTicket }}</td>
                    <td>{{ $note->note }}/5</td>
                    <td>{{ $note->commentaire }}</td>
                    <td>{{ \Carbon\Carbon::parse($note->dateNote)->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

@endsection
