<div class="container-fluid mt-4">
    <!-- Cartes de Statistiques Principales -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="stats-card premium">
                <div class="stats-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $compagnies->count() }}</h3>
                    <p>Compagnies</p>
                    <span class="stats-trend up">
                        <i class="fas fa-arrow-up"></i>
                        {{ $compagnies->count() > 0 ? 'Actives' : 'Aucune' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="stats-card success">
                <div class="stats-icon">
                    <i class="fas fa-route"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $voyagesEffectues }}</h3>
                    <p>Voyages Effectués</p>
                    <span class="stats-trend up">
                        <i class="fas fa-arrow-up"></i>
                        Total
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="stats-card warning">
                <div class="stats-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $voyagesJour }}</h3>
                    <p>Voyages Aujourd'hui</p>
                    <span class="stats-trend {{ $voyagesJour > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $voyagesJour > 0 ? 'up' : 'down' }}"></i>
                        Journée
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="stats-card info">
                <div class="stats-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ number_format($gains/1000, 1) }}K</h3>
                    <p>Gains Totaux</p>
                    <span class="stats-trend up">
                        <i class="fas fa-arrow-up"></i>
                        Cumulés
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="stats-card dark">
                <div class="stats-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ number_format($totalTickets/1000, 1) }}K</h3>
                    <p>Tickets Vendus</p>
                    <span class="stats-trend up">
                        <i class="fas fa-arrow-up"></i>
                        Total
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="stats-card primary">
                <div class="stats-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $ticketsAujourdhui }}</h3>
                    <p>Tickets Aujourd'hui</p>
                    <span class="stats-trend {{ $ticketsAujourdhui > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $ticketsAujourdhui > 0 ? 'up' : 'down' }}"></i>
                        Ventes du jour
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Tableaux -->
    <div class="row">
        <!-- Compagnies -->
        <div class="col-lg-6 mb-4">
            <div class="card card-modern">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-list-alt me-2"></i>
                        Aperçu des Compagnies
                    </h4>
                    <div class="card-actions">
                        <a href="{{ route('compagnies.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Nouvelle Compagnie
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-modern">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Compagnie</th>
                                    <th class="text-center">Trajets</th>
                                    <th class="text-center">Gares</th>
                                    <th class="text-center">Buses</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($compagnies as $compagnie)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="company-avatar">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $compagnie->name }}</strong>
                                                    <div class="text-muted small">{{ $compagnie->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-soft">
                                                {{ $compagnie->trajets_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-soft">
                                                {{ $compagnie->garres_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-soft">
                                                {{ $compagnie->buses_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="status-badge active">Active</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <i class="fas fa-building fa-3x"></i>
                                                <h5>Aucune compagnie</h5>
                                                <p>Commencez par créer une compagnie</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Trajets -->
        <div class="col-lg-6 mb-4">
            <div class="card card-modern">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-trophy me-2"></i>
                        Top 5 des Trajets
                    </h4>
                </div>
                <div class="card-body">
                    <div class="top-trajets">
                        @forelse($topTrajets as $index => $trajet)
                            <div class="trajet-item">
                                <div class="trajet-rank">
                                    <span class="rank-number">{{ $index + 1 }}</span>
                                </div>
                                <div class="trajet-info">
                                    <h6>{{ $trajet->trajet->pointDepart }} → {{ $trajet->trajet->pointArrive }}</h6>
                                    <div class="trajet-stats">
                                        <span class="voyages-count">
                                            <i class="fas fa-route me-1"></i>
                                            {{ $trajet->total }} voyages
                                        </span>
                                        <span class="compagnie">
                                            {{ $trajet->trajet->compagnie->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="trajet-chart">
                                    <div class="mini-chart">
                                        <div class="chart-bar" style="height: {{ ($trajet->total / max($topTrajets->max('total'), 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-route fa-3x"></i>
                                <h5>Aucun trajet</h5>
                                <p>Les statistiques apparaîtront ici</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card card-modern">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Voyages par Mois
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="voyagesMensuelsChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card card-modern">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Répartition par Compagnie
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="repartitionCompagniesChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des voyages mensuels
    const voyagesCtx = document.getElementById('voyagesMensuelsChart').getContext('2d');
    new Chart(voyagesCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Voyages',
                data: @json(array_values($voyagesMensuels)),
                backgroundColor: 'rgba(70, 114, 249, 0.8)',
                borderColor: 'rgba(70, 114, 249, 1)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Graphique de répartition
    const repartitionCtx = document.getElementById('repartitionCompagniesChart').getContext('2d');
    new Chart(repartitionCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($repartitionCompagnies)),
            datasets: [{
                data: @json(array_values($repartitionCompagnies)),
                backgroundColor: [
                    '#4672f9', '#28c76f', '#ff9f43', '#ea5455', '#00cfe8',
                    '#7367f0', '#8b93a6', '#b23a3a', '#4caf50', '#ff9800'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
