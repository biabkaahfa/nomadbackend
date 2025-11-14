@extends('back.app')

@section('title', 'Tableau de bord - Chef de Gare')

@section('dashboard-header')
<!-- Header avec background gradient -->
<div class="dashboard-header">
    <div class="container-fluid py-6">
        <div class="row align-items-center">
            <div class="col">
                <div class="welcome-section">
                    <h1 class="display-6 fw-bold text-white mb-2">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        Tableau de bord - Chef de Gare
                    </h1>
                    <p class="text-white-50 mb-0 fs-5">
                        Bienvenue, <span class="fw-semibold">{{ auth()->user()->name }}</span> !
                        Supervision de la gare {{ $garre->name }}.
                    </p>
                </div>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <div class="time-display text-white text-end">
                        <div class="fs-2 fw-bold" id="liveTime">{{ now()->format('H:i') }}</div>
                        <div class="fs-6 opacity-75">{{ now()->translatedFormat('l d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
<div class="container-fluid mt-4">
    <!-- Cartes de Statistiques Spécifiques au Chef de Gare -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="stats-icon">
                    <i class="fas fa-route"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $voyages->count() }}</h3>
                    <p>Voyages du Jour</p>
                    <span class="stats-trend {{ $voyages->count() > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $voyages->count() > 0 ? 'up' : 'down' }}"></i>
                        Multi-gares
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="stats-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $ticketsVendus }}</h3>
                    <p>Tickets Vendus</p>
                    <span class="stats-trend {{ $ticketsVendus > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $ticketsVendus > 0 ? 'up' : 'down' }}"></i>
                        Aujourd'hui
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $personnels }}</h3>
                    <p>Personnels</p>
                    <span class="stats-trend {{ $personnels > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $personnels > 0 ? 'up' : 'down' }}"></i>
                        Actifs
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card info">
                <div class="stats-icon">
                    <i class="fas fa-train-station"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $voyages->unique('idTrajet')->count() }}</h3>
                    <p>Destinations</p>
                    <span class="stats-trend up">
                        <i class="fas fa-arrow-up"></i>
                        Desservies
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section principale avec tableau des voyages -->
    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="fas fa-calendar-check me-2"></i>
                                Voyages Multi-gares - {{ $garre->name }}
                            </h4>
                            <p class="text-muted mb-0 mt-1">
                                {{ now()->translatedFormat('l d F Y') }} • Supervision des départs et arrivées
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-stats">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $voyages->unique('idTrajet')->count() }} destinations
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-ticket-alt me-1"></i>
                                    {{ $ticketsVendus }} tickets
                                </span>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="refreshVoyages()">
                                <i class="fas fa-sync-alt me-1"></i>
                                Actualiser
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">
                                        <i class="fas fa-route me-2"></i>
                                        Trajet & Gares
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-clock me-2"></i>
                                        Horaires
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-bus me-2"></i>
                                        Bus
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Tickets
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        Occupation
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-users me-2"></i>
                                        Gares Actives
                                    </th>
                                    <th class="text-center pe-4">
                                        <i class="fas fa-cogs me-2"></i>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($voyages as $voyage)
                                    @php
                                        $heureDepart = \Carbon\Carbon::parse($voyage->heuresDepart);
                                        $now = \Carbon\Carbon::now();
                                        $placesDisponibles = $voyage->bus ?
                                            ($voyage->bus->nombrePlaces - $voyage->tickets->count()) : 0;
                                        $occupation = $voyage->bus ?
                                            round(($voyage->tickets->count() / $voyage->bus->nombrePlaces) * 100, 2) : 0;
                                        $garresDesservies = $voyage->trajet->garres ?? collect();
                                        $isImminent = $heureDepart->diffInMinutes($now) <= 30 && $heureDepart->isFuture();
                                        $isEnCours = $heureDepart->isPast() && $heureDepart->diffInHours($now) <= 4;
                                    @endphp
                                    <tr class="voyage-row {{ $isImminent ? 'table-warning' : '' }} {{ $isEnCours ? 'table-info' : '' }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-start">
                                                <div class="voyage-avatar me-3">
                                                    <i class="fas fa-bus"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        {{ $voyage->trajet->pointDepart }}
                                                        <i class="fas fa-arrow-right mx-2 text-muted small"></i>
                                                        {{ $voyage->trajet->pointArrive }}
                                                    </h6>
                                                    <div class="voyage-meta">
                                                        <span class="badge bg-light text-dark me-2">
                                                            <i class="fas fa-building me-1"></i>
                                                            {{ $voyage->trajet->compagnie->name ?? 'N/A' }}
                                                        </span>
                                                        <small class="text-muted">
                                                            #{{ $voyage->id }}
                                                        </small>
                                                    </div>
                                                    <div class="garres-preview mt-1">
                                                        <small class="text-muted">
                                                            <i class="fas fa-train-station me-1"></i>
                                                            {{ $garresDesservies->count() }} gares desservies
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="time-display">
                                                <div class="fw-bold fs-5 {{ $isImminent ? 'text-warning' : '' }}">
                                                    {{ $heureDepart->format('H:i') }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $heureDepart->diffForHumans() }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($voyage->bus)
                                                <span class="badge bg-info">
                                                    {{ $voyage->bus->numeroBus }}
                                                </span>
                                                <div class="text-muted small mt-1">
                                                    {{ $voyage->bus->modele }}
                                                </div>
                                            @else
                                                <span class="badge bg-warning">Non assigné</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="tickets-info">
                                                <div class="fw-semibold">
                                                    {{ $voyage->tickets->count() }}
                                                </div>
                                                <small class="text-muted">
                                                    sur {{ $voyage->bus->nombrePlaces ?? 0 }}
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="occupation-display">
                                                <span class="fw-bold {{ $occupation > 80 ? 'text-danger' : ($occupation > 50 ? 'text-warning' : 'text-success') }}">
                                                    {{ $occupation }}%
                                                </span>
                                                <div class="progress mini-progress mt-1 mx-2">
                                                    <div class="progress-bar {{ $occupation > 80 ? 'bg-danger' : ($occupation > 50 ? 'bg-warning' : 'bg-success') }}"
                                                         style="width: {{ $occupation }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="garres-count">
                                                <span class="badge bg-success-soft">
                                                    {{ $garresDesservies->count() }}
                                                </span>
                                                <button class="btn btn-sm btn-outline-primary ms-1"
                                                        onclick="showGaresDetails({{ $voyage->id }})"
                                                        title="Voir les gares">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="action-buttons">
                                                <button class="btn btn-primary-soft btn-sm me-1"
                                                        title="Voir les détails"
                                                        onclick="showVoyageDetails({{ $voyage->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning-soft btn-sm me-1"
                                                        title="Modifier"
                                                        onclick="editVoyage({{ $voyage->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-info-soft btn-sm"
                                                        title="Annonces multi-gares"
                                                        onclick="makeMultiGareAnnouncement({{ $voyage->id }})">
                                                    <i class="fas fa-bullhorn"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun voyage programmé</h5>
                                                <p class="text-muted mb-4">Aucun voyage n'est prévu pour aujourd'hui dans votre gare</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ $voyages->count() }} voyage(s) au total •
                                Dernière mise à jour : {{ now()->format('H:i:s') }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="legend">
                                <span class="legend-item me-3">
                                    <span class="legend-color bg-warning"></span>
                                    Départ imminent
                                </span>
                                <span class="legend-item me-3">
                                    <span class="legend-color bg-info"></span>
                                    En cours
                                </span>
                                <span class="legend-item">
                                    <span class="legend-color bg-secondary"></span>
                                    Terminé
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes rapides d'actions -->
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="quick-action-card">
                <div class="action-icon bg-primary">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="action-content">
                    <h5>Annonces</h5>
                    <p>Diffuser une annonce</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="openAnnouncements()">Lancer</button>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="quick-action-card">
                <div class="action-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-content">
                    <h5>Personnel</h5>
                    <p>Gérer l'équipe</p>
                    <button class="btn btn-outline-success btn-sm" onclick="managePersonnel()">Voir</button>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="quick-action-card">
                <div class="action-icon bg-warning">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="action-content">
                    <h5>Rapports</h5>
                    <p>Statistiques détaillées</p>
                    <button class="btn btn-outline-warning btn-sm" onclick="generateReports()">Générer</button>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="quick-action-card">
                <div class="action-icon bg-info">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="action-content">
                    <h5>Paramètres</h5>
                    <p>Configuration gare</p>
                    <button class="btn btn-outline-info btn-sm" onclick="configureGare()">Configurer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher les détails des gares -->
<div class="modal fade" id="garesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gares Desservies</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="garesModalContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les détails du voyage -->
<div class="modal fade" id="voyageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Voyage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="voyageModalContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Variables CSS */
    :root {
        --primary: #4672f9;
        --primary-soft: rgba(70, 114, 249, 0.1);
        --success: #28c76f;
        --success-soft: rgba(40, 199, 111, 0.1);
        --warning: #ff9f43;
        --warning-soft: rgba(255, 159, 67, 0.1);
        --danger: #ea5455;
        --danger-soft: rgba(234, 84, 85, 0.1);
        --info: #00cfe8;
        --info-soft: rgba(0, 207, 232, 0.1);
        --dark: #4b4b4b;
        --dark-soft: rgba(75, 75, 75, 0.1);
    }

    /* Header du Dashboard */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, #7367f0 100%);
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.05)"><polygon points="0,0 1000,50 1000,100 0,100"/></svg>');
        background-size: cover;
    }

    .welcome-section h1 {
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .current-time {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 1rem 1.5rem;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* Cartes de Statistiques */
    .stats-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary);
    }

    .stats-card.success::before { background: var(--success); }
    .stats-card.warning::before { background: var(--warning); }
    .stats-card.info::before { background: var(--info); }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 1.5rem;
    }

    .stats-card.success .stats-icon { background: var(--success-soft); color: var(--success); }
    .stats-card.warning .stats-icon { background: var(--warning-soft); color: var(--warning); }
    .stats-card.info .stats-icon { background: var(--info-soft); color: var(--info); }

    .stats-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .stats-content p {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .stats-trend {
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stats-trend.up { color: var(--success); }
    .stats-trend.down { color: var(--danger); }

    /* Cartes Modernes */
    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: white;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem;
    }

    .card-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
    }

    /* Table */
    .table-modern th {
        border: none;
        font-weight: 600;
        color: #495057;
        background: #f8f9fa;
        padding: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .table-modern td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f1f3f4;
    }

    .voyage-row:hover {
        background-color: #f8f9fa;
    }

    .voyage-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .voyage-meta {
        margin-top: 0.5rem;
    }

    .garres-preview {
        font-size: 0.875rem;
    }

    .mini-progress {
        height: 4px;
        border-radius: 2px;
        background: #e9ecef;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 2px;
    }

    .garres-count {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 0.25rem;
    }

    .btn-primary-soft {
        background: var(--primary-soft);
        color: var(--primary);
        border: 1px solid var(--primary-soft);
    }

    .btn-warning-soft {
        background: var(--warning-soft);
        color: var(--warning);
        border: 1px solid var(--warning-soft);
    }

    .btn-info-soft {
        background: var(--info-soft);
        color: var(--info);
        border: 1px solid var(--info-soft);
    }

    .btn-primary-soft:hover, .btn-warning-soft:hover, .btn-info-soft:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Cartes d'action rapide */
    .quick-action-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0,0,0,0.05);
        text-align: center;
        transition: all 0.3s ease;
    }

    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .action-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.5rem;
    }

    .action-content h5 {
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .action-content p {
        color: #6c757d;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    /* Légende */
    .legend {
        display: flex;
        align-items: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    /* États vides */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #6c757d;
    }

    .empty-state i {
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h5 {
        margin-bottom: 0.5rem;
        color: #495057;
    }

    /* Badges */
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 600;
    }

    .bg-success-soft {
        background: var(--success-soft);
        color: var(--success);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-stats {
            display: none;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.5rem;
        }

        .legend {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .stats-card {
            padding: 1rem;
        }

        .stats-content h3 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Mise à jour de l'heure en temps réel
    function updateTime() {
        const now = new Date();
        const timeElement = document.getElementById('liveTime');
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    setInterval(updateTime, 1000);

    // Simulation de rafraîchissement
    function refreshVoyages() {
        const btn = event.target;
        const originalText = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualisation...';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            showNotification('Voyages actualisés avec succès', 'success');
        }, 1500);
    }

    // Fonction pour afficher les détails des gares
    function showGaresDetails(voyageId) {
        // Simulation de chargement des données
        const modalContent = `
            <div class="gares-list">
                <h6 class="mb-3">Gares desservies pour ce voyage :</h6>
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-train-station text-primary me-2"></i>
                            <strong>Gare Centrale</strong>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-train-station text-primary me-2"></i>
                            <strong>Gare Nord</strong>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-train-station text-primary me-2"></i>
                            <strong>Gare Sud</strong>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Les tickets sont valables dans toutes ces gares pour cette destination
                    </small>
                </div>
            </div>
        `;

        document.getElementById('garesModalContent').innerHTML = modalContent;
        const modal = new bootstrap.Modal(document.getElementById('garesModal'));
        modal.show();
    }

    // Fonction pour afficher les détails du voyage
    function showVoyageDetails(voyageId) {
        const modalContent = `
            <div class="voyage-details-modal">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations du voyage</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-route text-primary me-2"></i>
                                <strong>Trajet :</strong> Ville A → Ville B
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-info me-2"></i>
                                <strong>Départ :</strong> 14:30
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-bus text-warning me-2"></i>
                                <strong>Bus :</strong> BUS-001
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-users text-success me-2"></i>
                                <strong>Occupation :</strong> 75%
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Statistiques</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-ticket-alt text-primary me-2"></i>
                                <strong>Tickets vendus :</strong> 45
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-chair text-success me-2"></i>
                                <strong>Places restantes :</strong> 15
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-train-station text-info me-2"></i>
                                <strong>Gares desservies :</strong> 3
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('voyageModalContent').innerHTML = modalContent;
        const modal = new bootstrap.Modal(document.getElementById('voyageModal'));
        modal.show();
    }

    function editVoyage(voyageId) {
        showNotification('Modification du voyage #' + voyageId, 'warning');
    }

    function makeMultiGareAnnouncement(voyageId) {
        showNotification('Lancement des annonces multi-gares pour le voyage #' + voyageId, 'primary');
    }

    // Fonctions des actions rapides
    function openAnnouncements() {
        showNotification('Ouverture du module d\'annonces', 'info');
    }

    function managePersonnel() {
        showNotification('Gestion du personnel', 'success');
    }

    function generateReports() {
        showNotification('Génération des rapports', 'warning');
    }

    function configureGare() {
        showNotification('Configuration de la gare', 'info');
    }

    // Fonction de notification
    function showNotification(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }

    // Animation des cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-in');
        });
    });
</script>
@endpush
