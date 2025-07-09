@extends('back.app')

@section('title', 'Voyages')

@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <div class="mt-5 d-flex justify-content-between align-items-center">
            <h4 class="card-title">Départs programmés</h4>
            <a href="{{ route('voyages.create') }}" class="btn btn-primary">Ajouter un Voyage</a>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')

{{-- 🔍 Barre de recherche et tri --}}
<form method="GET" action="{{ route('voyages.index') }}" class="mb-4 d-flex justify-content-between align-items-end">
    <div class="form-group mb-0">
        <label>Rechercher un trajet</label>
        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ex : Ouaga, Bobo" style="font-size: 1.3rem; padding: 1rem; height: auto;">
    </div>

    <div class="form-group mb-0 ml-3">
        <label>Trier par date de départ</label>
        <select name="sort" class="form-control" onchange="this.form.submit()" style="font-size: 1.3rem; padding: 1rem; height: auto;">
            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Date croissante</option>
            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Date décroissante</option>
        </select>
    </div>

    <div class="ml-3">
        <button type="submit" class="btn btn-outline-secondary">Rechercher</button>
    </div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="thead-light">
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Trajet</th>
                    <th>Bus</th>
                    <th>Occupation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voyages as $voyage)
                    @php
                        $placesTotal = $voyage->bus
                            ? $voyage->bus->placesDisponible
                            : $voyage->trajet->frequences
                                ->where('heureDepart', $voyage->heuresDepart)
                                ->first()?->nombrePlaceMinimum ?? 0;

                        $ticketsVendus = $voyage->tickets->count();
                        $taux = $placesTotal > 0 ? round(($ticketsVendus / $placesTotal) * 100) : 0;

                        $datetimeVoyage = \Carbon\Carbon::parse($voyage->dateDepart . ' ' . $voyage->heuresDepart);
                        $voyagePasse = $datetimeVoyage->lt(now());
                    @endphp
                    <tr class="{{ $voyagePasse ? 'table-danger' : '' }}">
                        <td>{{ $datetimeVoyage->format('d/m/Y') }}</td>
                        <td>{{ $datetimeVoyage->format('H:i') }}</td>
                        <td>{{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}</td>
                        <td>{{ $voyage->bus->numeroBus ?? 'Non assigné' }}</td>
                        <td style="min-width: 200px;">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar
                                    {{ $voyagePasse ? 'bg-danger' : 'bg-success' }}"
                                    role="progressbar"
                                    style="width: {{ $taux }}%;"
                                    aria-valuenow="{{ $taux }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $taux }}%
                                </div>
                            </div>
                            <small>{{ $ticketsVendus }} / {{ $placesTotal }} places</small><br>
                            <span class="badge {{ $voyagePasse ? 'bg-danger' : 'bg-success' }}">
                                {{ $voyagePasse ? 'Voyage terminé' : 'À venir' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('voyages.edit', $voyage->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                            @if (is_null($voyage->bus?->numeroBus))
                            <a href="{{ route('voyages.affectation', $voyage->id) }}" class="btn btn-sm btn-primary">Affectation</a>
                            @endif

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

                @if($voyages->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center text-muted">Aucun voyage programmé</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
