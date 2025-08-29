@extends('back.app')

@section('title', 'Tableau de bord')

@section('dashboard-header')
<div class="container-fluid py-4">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-tachometer-alt me-3"></i>
                Tableau de bord - Bienvenue, {{ auth()->user()->name }} !
            </h1>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
@php $profil = strtolower($profil); @endphp

<!-- Messages d'alerte -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Admin Général --}}
@if($profil === 'admin général')
    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-building fa-2x mb-2 text-primary"></i>
                <h4>{{ $compagnies->count() }}</h4>
                <p class="mb-0">Compagnies</p>
                <small class="text-muted">enregistrées</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-route fa-2x mb-2 text-success"></i>
                <h4>{{ $voyagesEffectues }}</h4>
                <p class="mb-0">Voyages</p>
                <small class="text-muted">effectués</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-calendar-day fa-2x mb-2 text-warning"></i>
                <h4>{{ $voyagesJour }}</h4>
                <p class="mb-0">Voyages</p>
                <small class="text-muted">aujourd'hui</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <i class="fas fa-wallet fa-2x mb-2 text-info"></i>
                <h4>{{ number_format($gains, 0, ',', ' ') }} F</h4>
                <p class="mb-0">Gains totaux</p>
                <small class="text-muted">revenus cumulés</small>
            </div>
        </div>
    </div>

    <!-- Tableau des compagnies -->
    <div class="card card-glass mb-5">
        <div class="card-header bg-white d-flex align-items-center">
            <h4 class="mb-0 text-primary">
                <i class="fas fa-list-alt me-2"></i>
                Aperçu des Compagnies
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern">
    <thead>
        <tr>
            <th><i class="fas fa-building me-2"></i>Nom</th>
            <th class="text-center"><i class="fas fa-route me-2"></i>Trajets</th>
            <th class="text-center"><i class="fas fa-train-station me-2"></i>Gares</th>
            <th class="text-center"><i class="fas fa-bus me-2"></i>Buses</th>
        </tr>
    </thead>
    <tbody>
        @forelse($compagnies as $compagnie)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-building text-primary me-2"></i>
                        <strong>{{ $compagnie->name }}</strong>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-primary-soft rounded-pill">
                        {{ $compagnie->trajetsCount }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge bg-info-soft rounded-pill">
                        {{ $compagnie->garresCount }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge bg-success-soft rounded-pill">
                        {{ $compagnie->busesCount }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <div class="empty-state">
                        <i class="fas fa-building fa-4x"></i>
                        <h5 class="mt-3 mb-2">Aucune compagnie enregistrée</h5>
                        <a href="{{ route('compagnies.create') }}" class="btn btn-primary-soft">
                            <i class="fas fa-plus me-2"></i>
                            Créer une compagnie
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
            </div>
        </div>
    </div>

{{-- Admin Compagnie --}}
@elseif($profil === 'admin compagnie')
    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-bus fa-2x mb-2 text-primary"></i>
                <h4>{{ $buses->count() }}</h4>
                <p class="mb-0">Buses</p>
                <small class="text-muted">enregistrés</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-train-station fa-2x mb-2 text-success"></i>
                <h4>{{ $garres->count() }}</h4>
                <p class="mb-0">Gares</p>
                <small class="text-muted">gérées</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-calendar-day fa-2x mb-2 text-warning"></i>
                <h4>{{ $voyagesJour }}</h4>
                <p class="mb-0">Voyages</p>
                <small class="text-muted">aujourd'hui</small>
            </div>
        </div>
    </div>

    <!-- Voyages du jour -->
    <div class="card card-glass mb-5">
        <div class="card-header bg-white d-flex align-items-center">
            <h4 class="mb-0 text-primary">
                <i class="fas fa-calendar-alt me-2"></i>
                Voyages du Jour par Gare
            </h4>
        </div>
        <div class="card-body">
            @forelse($garres as $garre)
                <div class="garre-section mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-train-station text-primary me-2"></i>
                        <h5 class="mb-0">{{ $garre->name }}</h5>
                    </div>

                    <div class="voyages-list">
                        @forelse($garre->trajets->flatMap->voyages->where('dateDepart', today()) as $voyage)
                            <div class="voyage-card">
                                <div class="voyage-route">
                                    <div class="route-point departure">
                                        <i class="fas fa-map-marker-alt text-success me-1"></i>
                                        <span>{{ $voyage->trajet->pointDepart }}</span>
                                    </div>
                                    <div class="route-line">
                                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                    </div>
                                    <div class="route-point arrival">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        <span>{{ $voyage->trajet->pointArrive }}</span>
                                    </div>
                                </div>
                                <div class="voyage-details">
                                    <div class="detail-item">
                                        <i class="fas fa-clock me-1"></i>
                                        <span>{{ \Carbon\Carbon::parse($voyage->heuresDepart)->format('H:i') }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-ticket-alt me-1"></i>
                                        <span>{{ $voyage->tickets->count() }} tickets</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-bus me-1"></i>
                                        <span>{{ $voyage->bus->numeroBus ?? 'Non assigné' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state-sm">
                                <i class="fas fa-route"></i>
                                <p class="text-muted">Aucun voyage pour cette gare aujourd'hui</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-train-station fa-4x"></i>
                    <h5 class="mt-3 mb-2">Aucune gare assignée</h5>
                    <p>Votre compagnie ne gère actuellement aucune gare</p>
                </div>
            @endforelse
        </div>
    </div>

{{-- Chef de Gare --}}
@elseif($profil === 'chef de gare')
    <div class="card card-glass mb-5">
        <div class="card-header bg-white d-flex align-items-center">
            <h4 class="mb-0 text-primary">
                <i class="fas fa-calendar-check me-2"></i>
                Voyages du Jour - {{ auth()->user()->garre->name }}
            </h4>
            <div class="ms-auto">
                <span class="badge bg-dark">
                    <i class="fas fa-users me-1"></i>
                    {{ $personnels }} personnel actif
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th><i class="fas fa-route me-2"></i>Trajet</th>
                            <th class="text-center"><i class="fas fa-clock me-2"></i>Heure</th>
                            <th class="text-center"><i class="fas fa-bus me-2"></i>Bus</th>
                            <th class="text-center"><i class="fas fa-ticket-alt me-2"></i>Tickets</th>
                            <th class="text-center"><i class="fas fa-cog me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($voyages as $voyage)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-route text-primary me-2"></i>
                                        <div>
                                            {{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}
                                            <div class="small text-muted">
                                                #{{ $voyage->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        {{ \Carbon\Carbon::parse($voyage->heuresDepart)->format('H:i') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($voyage->bus)
                                        <span class="badge bg-info">
                                            {{ $voyage->bus->numeroBus }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning">Non assigné</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $voyage->tickets->count() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="#" class="btn btn-success-soft btn-sm" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-warning-soft btn-sm" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times fa-4x"></i>
                                        <h5 class="mt-3 mb-2">Aucun voyage prévu</h5>
                                        <p>Pas de voyage programmé pour aujourd'hui</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- Réceptionniste --}}
@elseif($profil === 'réceptionniste')
    <div class="card card-glass mb-5">
        <div class="card-header bg-white d-flex align-items-center">
            <h4 class="mb-0 text-primary">
                <i class="fas fa-ticket-alt me-2"></i>
                Voyages disponibles - {{ auth()->user()->garre->name }}
            </h4>
        </div>
        <div class="card-body">
            <div class="voyage-cards-grid">
                @forelse($voyages as $voyage)
                    <div class="voyage-card">
                        <div class="voyage-header">
                            <h5>
                                <i class="fas fa-route me-2 text-primary"></i>
                                {{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}
                            </h5>
                            <span class="badge bg-primary">
                                {{ \Carbon\Carbon::parse($voyage->dateDepart)->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="voyage-details">
                            <div class="detail-item">
                                <i class="fas fa-clock me-2"></i>
                                <span>{{ \Carbon\Carbon::parse($voyage->heuresDepart)->format('H:i') }}</span>
                            </div>

                            <div class="detail-item">
                                <i class="fas fa-bus me-2"></i>
                                <span>{{ $voyage->bus->numeroBus ?? 'Non assigné' }}</span>
                            </div>

                            <div class="detail-item">
                                <i class="fas fa-ticket-alt me-2"></i>
                                <span>{{ $voyage->tickets->count() }} vendus</span>
                            </div>
                        </div>

                        <div class="voyage-actions">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-ticket-alt me-1"></i>
                                Vendre ticket
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-ticket-alt fa-4x"></i>
                        <h5 class="mt-3 mb-2">Aucun voyage disponible</h5>
                        <p>Pas de voyage disponible pour la vente de tickets</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
    /* Cartes de statistiques */
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-card i {
        color: inherit;
    }

    .stats-card h4 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0.5rem 0;
        color: #2c3e50;
    }

    .stats-card p {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        color: #6c757d;
    }

    .stats-card small {
        font-size: 0.75rem;
        color: #adb5bd;
    }

    /* Cartes de voyage */
    .voyage-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .voyage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .voyage-route {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .voyage-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }

    /* Badges */
    .badge-compagnie {
        background-color: rgba(70, 114, 249, 0.1);
        color: #4672f9;
        padding: 0.35em 0.65em;
        border-radius: 6px;
        font-size: 0.8rem;
    }

    .bg-primary-soft {
        background-color: rgba(70, 114, 249, 0.1);
        color: #4672f9;
    }

    .bg-success-soft {
        background-color: rgba(40, 199, 111, 0.1);
        color: #28c76f;
    }

    .bg-warning-soft {
        background-color: rgba(255, 171, 0, 0.1);
        color: #ffab00;
    }

    /* Boutons d'action */
    .action-buttons .btn {
        margin: 0 2px;
        padding: 0.25rem 0.5rem;
    }

    /* États vides */
    .empty-state {
        text-align: center;
        padding: 2rem 0;
    }

    .empty-state i {
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .empty-state-sm {
        text-align: center;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    /* Grille responsive */
    .voyage-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    @media (max-width: 768px) {
        .voyage-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
