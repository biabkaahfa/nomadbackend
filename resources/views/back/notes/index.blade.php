@extends('back.app')

@section('title', 'Notes de voyages')

@section('dashboard-header')
<div class="d-flex justify-content-between align-items-center">
    <h4 class="mt-5">Statistiques des notes de voyages</h4>
    @if(auth()->user()->profil?->name === 'Admin général')
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Compagnie: {{ request('compagnie') ?: 'Toutes' }}
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['compagnie' => '']) }}">Toutes les compagnies</a></li>
            @foreach($compagnies as $compagnie)
            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['compagnie' => $compagnie->id]) }}">{{ $compagnie->name }}</a></li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection

@section('dashboard-content')

{{-- Tableau Statistique Principal --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">📊 Statistiques Globales par Trajet</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-white">
                <tr>
                    <th>Trajet</th>
                    <th>Compagnie</th>
                    <th>Total notes</th>
                    <th>Note moyenne</th>
                    <th>Note max</th>
                    <th>Note min</th>
                    <th>Détails critères</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats as $stat)
                <tr>
                    <td>
                        <strong>{{ $stat->trajet }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $stat->compagnie_nom }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $stat->total_notes }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $stat->moyenne >= 4 ? 'bg-success' : ($stat->moyenne >= 3 ? 'bg-warning' : 'bg-danger') }}">
                            {{ $stat->moyenne }}/5
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success">{{ $stat->max_note }}/5</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-danger">{{ $stat->min_note }}/5</span>
                    </td>
                    <td>
                        <small>
                            <div>🛡️ Sécurité: {{ $stat->moyenne_securite }}/5</div>
                            <div>💺 Confort: {{ $stat->moyenne_confort }}/5</div>
                            <div>⏱️ Ponctualité: {{ $stat->moyenne_ponctualite }}/5</div>
                            <div>👋 Accueil: {{ $stat->moyenne_accueil }}/5</div>
                            <div>🧹 Propreté: {{ $stat->moyenne_proprete }}/5</div>
                        </small>
                    </td>
                    <td>
                        <a href="{{ route('notes.voyages', ['trajet' => $stat->trajet]) }}" class="btn btn-sm btn-outline-primary">
                            Voir voyages
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Aucune note disponible pour le moment
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Dernières Notes par Voyage --}}
@if($latestNotes->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">📝 5 Dernières Notes par Voyage</h5>
    </div>
    <div class="card-body">
        @foreach($latestNotes as $voyageId => $notes)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>🚌 Voyage : {{ $notes->first()->nomVoyage ?? 'N/A' }}</strong>
                    <span class="badge bg-info">{{ $notes->first()->compagnie_nom }}</span>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Note Globale</th>
                            <th>Détails des notes</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notes as $note)
                        <tr>
                            <td>#{{ $note->idTicket }}</td>
                            <td>
                                <span class="badge {{ $note->note >= 4 ? 'bg-success' : ($note->note >= 3 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $note->note ?? 'N/A' }}/5
                                </span>
                            </td>
                            <td>
                                @if($note->securite || $note->confort || $note->ponctualite)
                                <small>
                                    <div>🛡️: {{ $note->securite ?? '-' }}/5</div>
                                    <div>💺: {{ $note->confort ?? '-' }}/5</div>
                                    <div>⏱️: {{ $note->ponctualite ?? '-' }}/5</div>
                                    <div>👋: {{ $note->accueil ?? '-' }}/5</div>
                                    <div>🧹: {{ $note->proprete ?? '-' }}/5</div>
                                </small>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($note->commentaire)
                                <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                      title="{{ $note->commentaire }}">
                                    {{ Str::limit($note->commentaire, 50) }}
                                </span>
                                @else
                                <span class="text-muted">Aucun commentaire</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($note->dateNote)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="alert alert-info">
    <h5>📊 Aucune note disponible</h5>
    <p class="mb-0">Les notes des voyages s'afficheront ici une fois que les passagers auront noté leurs expériences.</p>
</div>
@endif

@endsection
