@extends('back.app')
@section('title', 'Tableau de bord des paiements')

@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <div class="mt-5 d-flex justify-content-between align-items-center">
            <h4 class="card-title fw-bold text-gradient">Tableau de bord des paiements</h4>
            <span class="badge bg-gradient-primary rounded-pill px-3 py-2">{{ $profil }}</span>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
<!-- Filtres améliorés -->
<div class="card glass-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="🔍 Référence ou Téléphone"
                       class="form-control form-control-lg soft-input">
            </div>
            <div class="col-md-4">
                <select name="periode" class="form-control form-control-lg soft-input">
                    <option value="jour" {{ request('periode') == 'jour' ? 'selected' : '' }}>📅 Aujourd'hui</option>
                    <option value="semaine" {{ request('periode') == 'semaine' ? 'selected' : '' }}>📅 Cette semaine</option>
                    <option value="mois" {{ request('periode') == 'mois' ? 'selected' : '' }}>📅 Ce mois</option>
                    <option value="annee" {{ request('periode') == 'annee' ? 'selected' : '' }}>📅 Cette année</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-lg w-100 soft-shadow">
                    🎯 Appliquer les filtres
                </button>
            </div>
        </form>
    </div>
</div>

@if($chartData['type'] === 'compagnies')
<!-- Sélecteur de compagnie pour Admin Général -->
<div class="card glass-card mb-4">
    <div class="card-header bg-gradient-warning text-white">
        <h5 class="mb-0">🏢 Vue détaillée par compagnie</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <select id="compagnieSelector" class="form-control form-control-lg soft-input">
                    <option value="">📊 Sélectionner une compagnie...</option>
                    @foreach($compagnies as $compagnie)
                    <option value="{{ $compagnie->id }}">{{ $compagnie->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="text-center">
                    <h4 id="selectedCompagnie" class="text-primary mb-0">Toutes les compagnies</h4>
                    <small class="text-muted">Total général: {{ number_format(array_sum(array_column($chartData['compagnies'], 'total')), 0, ',', ' ') }} F</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section détaillée par compagnie (cachée par défaut) -->
<div id="compagnieDetail" class="d-none">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card glass-card">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0" id="detailTitle">📈 Détails par gare</h5>
                </div>
                <div class="card-body">
                    <canvas id="compagnieDetailChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass-card h-100">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">🏷️ Répartition par gare</h5>
                </div>
                <div class="card-body">
                    <div id="garesList">
                        <!-- La liste des gares sera injectée ici par JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques pour la compagnie sélectionnée -->
    <div class="row mb-4" id="compagnieStats">
        <!-- Les statistiques seront injectées ici par JavaScript -->
    </div>
</div>

<!-- Vue d'ensemble pour Admin Général -->
<div class="card glass-card mb-4">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0">🌍 Vue d'ensemble toutes compagnies</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <canvas id="compagniePieChart" height="300"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="compagnieBarChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Cartes des totaux par compagnie -->
<div class="row mb-4">
    @foreach($chartData['compagnies'] as $compagnie => $data)
    <div class="col-md-4 mb-3">
        <div class="card glass-card h-100 compagnie-card" data-compagnie="{{ $compagnie }}">
            <div class="card-body text-center">
                <div class="company-avatar mb-3">
                    <div class="avatar-circle bg-gradient-{{ ['primary', 'success', 'warning', 'info'][$loop->index % 4] }}">
                        {{ substr($compagnie, 0, 2) }}
                    </div>
                </div>
                <h6 class="card-title fw-bold">{{ $compagnie }}</h6>
                <h4 class="text-primary mb-3">{{ number_format($data['total'], 0, ',', ' ') }} F</h4>

                <div class="payment-methods">
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-om">💰 OM</span>
                        <span class="fw-bold">{{ number_format($data['OM'], 0, ',', ' ') }} F</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-moov">💙 MOOV</span>
                        <span class="fw-bold">{{ number_format($data['MOOV'], 0, ',', ' ') }} F</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-espece">💵 Espèces</span>
                        <span class="fw-bold">{{ number_format($data['ESPECE'], 0, ',', ' ') }} F</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-carte">💳 Carte</span>
                        <span class="fw-bold">{{ number_format($data['CARTE'], 0, ',', ' ') }} F</span>
                    </div>
                </div>
                <button class="btn btn-outline-primary btn-sm mt-3 view-details" data-compagnie="{{ $compagnie }}">
                    📊 Voir détails
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

@elseif($chartData['type'] === 'gares')
<!-- Vue pour Admin Compagnie -->
<div class="card glass-card mb-4">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0">🏢 Tableau de bord - {{ $user->compagnie->name ?? 'Ma Compagnie' }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <canvas id="compagnieGareChart" height="300"></canvas>
            </div>
            <div class="col-md-4">
                <div class="text-center mb-4">
                    <h3 class="text-primary">{{ number_format($chartData['total_compagnie']['total'], 0, ',', ' ') }} F</h3>
                    <p class="text-muted">Chiffre d'affaires total de votre compagnie</p>
                </div>
                @foreach($chartData['gares'] as $garre => $data)
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                    <div>
                        <strong>{{ $garre }}</strong>
                        <div class="text-muted small">
                            Total: {{ number_format($data['total'], 0, ',', ' ') }} F
                        </div>
                    </div>
                    <span class="badge bg-primary">{{ number_format($data['total'], 0, ',', ' ') }} F</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Cartes de statistiques pour Admin Compagnie -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-om-gradient text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Orange Money</h6>
                <h4 class="mb-0">{{ number_format($chartData['total_compagnie']['OM'], 0, ',', ' ') }} F</h4>
                <small>{{ $chartData['total_compagnie']['total'] > 0 ? number_format(($chartData['total_compagnie']['OM']/$chartData['total_compagnie']['total'])*100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-moov-gradient text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Moov Money</h6>
                <h4 class="mb-0">{{ number_format($chartData['total_compagnie']['MOOV'], 0, ',', ' ') }} F</h4>
                <small>{{ $chartData['total_compagnie']['total'] > 0 ? number_format(($chartData['total_compagnie']['MOOV']/$chartData['total_compagnie']['total'])*100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-espece-gradient text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Espèces</h6>
                <h4 class="mb-0">{{ number_format($chartData['total_compagnie']['ESPECE'], 0, ',', ' ') }} F</h4>
                <small>{{ $chartData['total_compagnie']['total'] > 0 ? number_format(($chartData['total_compagnie']['ESPECE']/$chartData['total_compagnie']['total'])*100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-carte-gradient text-white">
            <div class="card-body text-center">
                <h6 class="card-title">Carte</h6>
                <h4 class="mb-0">{{ number_format($chartData['total_compagnie']['CARTE'], 0, ',', ' ') }} F</h4>
                <small>{{ $chartData['total_compagnie']['total'] > 0 ? number_format(($chartData['total_compagnie']['CARTE']/$chartData['total_compagnie']['total'])*100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
</div>

@else
<!-- Vue pour Chef de gare/Réceptionniste avec design amélioré -->

<!-- Vue pour Chef de gare/Réceptionniste avec distinction scanné/non scanné -->
<div class="alert alert-info mb-3">
    <i class="fas fa-info-circle"></i>
    Vue de votre gare : <strong>{{ $user->garre->name ?? 'Votre gare' }}</strong>
    • Paiements scannés : {{ $paiements->where('ticket.dateScan', '!==', null)->count() }}
    • En attente de scan : {{ $paiements->where('ticket.dateScan', null)->count() }}
</div>
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card glass-card">
            <div class="card-header bg-gradient-info text-white">
                <h5 class="mb-0">📈 Évolution des paiements (Tickets scannés dans votre gare)</h5>
            </div>
            <div class="card-body">
                <canvas id="paiementChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card h-100">
            <div class="card-header bg-gradient-secondary text-white">
                <h5 class="mb-0">🍩 Répartition des paiements</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="paiementPieChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Cartes de statistiques améliorées -->
@php
    $totalOM = array_sum($chartData['OM'] ?? []);
    $totalMOOV = array_sum($chartData['MOOV'] ?? []);
    $totalESPECE = array_sum($chartData['ESPECE'] ?? []);
    $totalCARTE = array_sum($chartData['CARTE'] ?? []);
    $totalGeneral = $totalOM + $totalMOOV + $totalESPECE + $totalCARTE;
@endphp

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-om-gradient text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <div class="icon-circle bg-warning">
                            <span>💰</span>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Orange Money</h6>
                        <h4 class="mb-0">{{ number_format($totalOM, 0, ',', ' ') }} F</h4>
                        <small>{{ $totalGeneral > 0 ? number_format(($totalOM/$totalGeneral)*100, 1) : 0 }}% du total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-moov-gradient text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <div class="icon-circle bg-primary">
                            <span>💙</span>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Moov Money</h6>
                        <h4 class="mb-0">{{ number_format($totalMOOV, 0, ',', ' ') }} F</h4>
                        <small>{{ $totalGeneral > 0 ? number_format(($totalMOOV/$totalGeneral)*100, 1) : 0 }}% du total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-espece-gradient text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <div class="icon-circle bg-success">
                            <span>💵</span>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Espèces</h6>
                        <h4 class="mb-0">{{ number_format($totalESPECE, 0, ',', ' ') }} F</h4>
                        <small>{{ $totalGeneral > 0 ? number_format(($totalESPECE/$totalGeneral)*100, 1) : 0 }}% du total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card bg-carte-gradient text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <div class="icon-circle bg-purple">
                            <span>💳</span>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-title mb-1">Carte</h6>
                        <h4 class="mb-0">{{ number_format($totalCARTE, 0, ',', ' ') }} F</h4>
                        <small>{{ $totalGeneral > 0 ? number_format(($totalCARTE/$totalGeneral)*100, 1) : 0 }}% du total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tableau des paiements -->
<div class="card glass-card">
    <div class="card-header bg-gradient-dark text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📋 Liste des paiements</h5>
            <span class="badge bg-light text-dark rounded-pill">{{ $paiements->count() }} paiements</span>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-gradient">
                <tr>
                    <th>👤 Utilisateur</th>
                    <th>💰 Montant</th>
                    <th>💳 Moyen</th>
                    <th>📊 Statut</th>
                    <th>🔗 Référence</th>
                    <th>🏢 Compagnie</th>
                    <th>📍 Gare</th>
                    <th>📅 Date</th>
                    <th>⚡ Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $p)
                <tr class="align-middle">
                    <td>
                        @if($p->ticket)
                            @if(!empty($p->ticket->name))
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm me-2">
                                        {{ substr($p->ticket->name, 0, 1) }}
                                    </div>
                                    {{ $p->ticket->name }}
                                </div>
                            @elseif($p->ticket->typeAchat === 'en_ligne' && $p->ticket->utilisateur)
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm me-2">
                                        {{ substr($p->ticket->utilisateur->name, 0, 1) }}
                                    </div>
                                    {{ $p->ticket->utilisateur->name }}
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold text-primary">{{ number_format($p->montant, 0, ',', ' ') }} F</span>
                    </td>
                    <td>
                        <span class="payment-badge payment-{{ strtolower($p->moyenPaiement) }}">
                            {{ $p->moyenPaiement }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ strtolower($p->statut) }}">
                            {{ $p->statut }}
                        </span>
                    </td>
                    <td>
                        <code class="reference-code">{{ $p->referenceTransaction }}</code>
                    </td>
                    <td>
                        @if($p->ticket && $p->ticket->voyage && $p->ticket->voyage->trajet && $p->ticket->voyage->trajet->compagnie)
                            <span class="badge bg-info">{{ $p->ticket->voyage->trajet->compagnie->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($p->ticket && $p->ticket->garre)
                            <span class="badge bg-success">{{ $p->ticket->garre->name }}</span>
                        @else
                            <span class="badge bg-secondary">Non scanné</span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('paiements.edit', $p) }}" class="btn btn-outline-warning btn-sm rounded">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('paiements.destroy', $p) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm rounded" onclick="return confirm('Supprimer ce paiement ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <div class="empty-state">
                            <i class="fas fa-receipt fa-3x mb-3"></i>
                            <p>Aucun paiement trouvé</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données globales
const allPaiements = @json($paiements);
const compagniesData = @json($chartData['type'] === 'compagnies' ? $chartData['compagnies'] : []);
const compagniesList = @json($compagnies);

@if($chartData['type'] === 'compagnies')
// Diagramme circulaire pour les compagnies
const compagniePieCtx = document.getElementById('compagniePieChart').getContext('2d');
const compagnies = Object.keys(compagniesData);

const compagnieTotals = compagnies.map(comp => compagniesData[comp].total);
const backgroundColors = [
    '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
    '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
];

new Chart(compagniePieCtx, {
    type: 'doughnut',
    data: {
        labels: compagnies,
        datasets: [{
            data: compagnieTotals,
            backgroundColor: backgroundColors,
            borderColor: '#ffffff',
            borderWidth: 2,
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value.toLocaleString()} F (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '60%'
    }
});

// Graphique en barres pour les méthodes de paiement par compagnie
const compagnieBarCtx = document.getElementById('compagnieBarChart').getContext('2d');
new Chart(compagnieBarCtx, {
    type: 'bar',
    data: {
        labels: compagnies,
        datasets: [
            {
                label: 'Orange Money',
                data: compagnies.map(comp => compagniesData[comp].OM),
                backgroundColor: '#FFA726',
                borderColor: '#FF9800',
                borderWidth: 1
            },
            {
                label: 'Moov Money',
                data: compagnies.map(comp => compagniesData[comp].MOOV),
                backgroundColor: '#42A5F5',
                borderColor: '#2196F3',
                borderWidth: 1
            },
            {
                label: 'Espèces',
                data: compagnies.map(comp => compagniesData[comp].ESPECE),
                backgroundColor: '#66BB6A',
                borderColor: '#4CAF50',
                borderWidth: 1
            },
            {
                label: 'Carte',
                data: compagnies.map(comp => compagniesData[comp].CARTE),
                backgroundColor: '#AB47BC',
                borderColor: '#9C27B0',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true
                }
            }
        }
    }
});

// Gestion du sélecteur de compagnie
document.getElementById('compagnieSelector').addEventListener('change', function(e) {
    const selectedCompagnieId = e.target.value;
    const selectedCompagnie = compagniesList.find(c => c.id == selectedCompagnieId);

    if (selectedCompagnie) {
        showCompagnieDetail(selectedCompagnie);
    } else {
        hideCompagnieDetail();
    }
});

// Gestion des boutons "Voir détails" sur les cartes
document.querySelectorAll('.view-details').forEach(button => {
    button.addEventListener('click', function() {
        const compagnieName = this.getAttribute('data-compagnie');
        const compagnie = compagniesList.find(c => c.name === compagnieName);
        if (compagnie) {
            document.getElementById('compagnieSelector').value = compagnie.id;
            showCompagnieDetail(compagnie);
        }
    });
});

function showCompagnieDetail(compagnie) {
    const detailSection = document.getElementById('compagnieDetail');
    const selectedCompagnieElement = document.getElementById('selectedCompagnie');

    // Afficher la section détaillée
    detailSection.classList.remove('d-none');
    selectedCompagnieElement.textContent = compagnie.name;
    document.getElementById('detailTitle').textContent = `📈 Détails pour ${compagnie.name}`;

    // Filtrer les paiements pour la compagnie sélectionnée
    const filteredPaiements = allPaiements.filter(p => {
        return p.ticket &&
               p.ticket.voyage &&
               p.ticket.voyage.trajet &&
               p.ticket.voyage.trajet.compagnie &&
               p.ticket.voyage.trajet.compagnie.id == compagnie.id;
    });

    // Grouper par gare
    const garesData = {};
    filteredPaiements.forEach(p => {
        const garreName = p.ticket && p.ticket.garre ? p.ticket.garre.name : 'Non scanné';

        if (!garesData[garreName]) {
            garesData[garreName] = {
                OM: 0, MOOV: 0, ESPECE: 0, CARTE: 0, total: 0, count: 0
            };
        }

        garesData[garreName][p.moyenPaiement] += p.montant;
        garesData[garreName]['total'] += p.montant;
        garesData[garreName]['count'] += 1;
    });

    // Mettre à jour la liste des gares
    updateGaresList(garesData);

    // Mettre à jour le graphique détaillé
    updateDetailChart(garesData, compagnie.name);

    // Mettre à jour les statistiques
    updateCompagnieStats(filteredPaiements, compagnie.name);

    // Scroll vers la section détaillée
    detailSection.scrollIntoView({ behavior: 'smooth' });
}

function hideCompagnieDetail() {
    const detailSection = document.getElementById('compagnieDetail');
    const selectedCompagnieElement = document.getElementById('selectedCompagnie');

    detailSection.classList.add('d-none');
    selectedCompagnieElement.textContent = 'Toutes les compagnies';
}

function updateGaresList(garesData) {
    const garesList = document.getElementById('garesList');
    let html = '';

    Object.entries(garesData).forEach(([garre, data]) => {
        const percentage = data.total > 0 ? Math.round((data.total / Object.values(garesData).reduce((sum, g) => sum + g.total, 0)) * 100) : 0;
        html += `
            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                <div>
                    <strong>${garre}</strong>
                    <div class="text-muted small">
                        ${data.count} paiement(s) • ${percentage}%
                    </div>
                </div>
                <span class="badge bg-primary">${data.total.toLocaleString()} F</span>
            </div>
        `;
    });

    garesList.innerHTML = html || '<p class="text-muted text-center">Aucun paiement trouvé</p>';
}

function updateDetailChart(garesData, compagnieName) {
    const ctx = document.getElementById('compagnieDetailChart').getContext('2d');
    const gares = Object.keys(garesData);

    // Détruire le graphique existant s'il y en a un
    if (window.detailChart) {
        window.detailChart.destroy();
    }

    window.detailChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: gares,
            datasets: [
                {
                    label: 'Orange Money',
                    data: gares.map(garre => garesData[garre].OM),
                    backgroundColor: '#FFA726',
                    borderColor: '#FF9800',
                    borderWidth: 1
                },
                {
                    label: 'Moov Money',
                    data: gares.map(garre => garesData[garre].MOOV),
                    backgroundColor: '#42A5F5',
                    borderColor: '#2196F3',
                    borderWidth: 1
                },
                {
                    label: 'Espèces',
                    data: gares.map(garre => garesData[garre].ESPECE),
                    backgroundColor: '#66BB6A',
                    borderColor: '#4CAF50',
                    borderWidth: 1
                },
                {
                    label: 'Carte',
                    data: gares.map(garre => garesData[garre].CARTE),
                    backgroundColor: '#AB47BC',
                    borderColor: '#9C27B0',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: `Répartition des paiements par gare - ${compagnieName}`
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' F';
                        }
                    }
                }
            }
        }
    });
}

function updateCompagnieStats(paiements, compagnieName) {
    const statsContainer = document.getElementById('compagnieStats');
    const total = paiements.reduce((sum, p) => sum + p.montant, 0);
    const paiementsScannes = paiements.filter(p => p.ticket && p.ticket.garre).length;
    const paiementsNonScannes = paiements.length - paiementsScannes;

    const stats = {
        'OM': paiements.filter(p => p.moyenPaiement === 'OM').reduce((sum, p) => sum + p.montant, 0),
        'MOOV': paiements.filter(p => p.moyenPaiement === 'MOOV').reduce((sum, p) => sum + p.montant, 0),
        'ESPECE': paiements.filter(p => p.moyenPaiement === 'ESPECE').reduce((sum, p) => sum + p.montant, 0),
        'CARTE': paiements.filter(p => p.moyenPaiement === 'CARTE').reduce((sum, p) => sum + p.montant, 0)
    };

    let html = `
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Total ${compagnieName}</h6>
                    <h4 class="mb-0">${total.toLocaleString()} F</h4>
                    <small>${paiements.length} paiement(s)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Tickets scannés</h6>
                    <h4 class="mb-0">${paiementsScannes}</h4>
                    <small>${paiements.length > 0 ? Math.round((paiementsScannes/paiements.length)*100) : 0}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Tickets non scannés</h6>
                    <h4 class="mb-0">${paiementsNonScannes}</h4>
                    <small>${paiements.length > 0 ? Math.round((paiementsNonScannes/paiements.length)*100) : 0}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Moyen principal</h6>
                    <h4 class="mb-0">${Object.keys(stats).reduce((a, b) => stats[a] > stats[b] ? a : b)}</h4>
                    <small>Méthode la plus utilisée</small>
                </div>
            </div>
        </div>
    `;

    statsContainer.innerHTML = html;
}

@elseif($chartData['type'] === 'gares')
// Graphique pour Admin Compagnie
const compagnieGareCtx = document.getElementById('compagnieGareChart').getContext('2d');
const gares = @json(array_keys($chartData['gares']));
const garesData = @json($chartData['gares']);

new Chart(compagnieGareCtx, {
    type: 'bar',
    data: {
        labels: gares,
        datasets: [
            {
                label: 'Orange Money',
                data: gares.map(garre => garesData[garre].OM),
                backgroundColor: '#FFA726',
                borderColor: '#FF9800',
                borderWidth: 1
            },
            {
                label: 'Moov Money',
                data: gares.map(garre => garesData[garre].MOOV),
                backgroundColor: '#42A5F5',
                borderColor: '#2196F3',
                borderWidth: 1
            },
            {
                label: 'Espèces',
                data: gares.map(garre => garesData[garre].ESPECE),
                backgroundColor: '#66BB6A',
                borderColor: '#4CAF50',
                borderWidth: 1
            },
            {
                label: 'Carte',
                data: gares.map(garre => garesData[garre].CARTE),
                backgroundColor: '#AB47BC',
                borderColor: '#9C27B0',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Répartition des paiements par gare (Votre compagnie)'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' F';
                    }
                }
            }
        }
    }
});

@else
// Graphique pour autres profils
const ctx = document.getElementById('paiementChart').getContext('2d');
const chartData = @json($chartData);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Orange Money',
                data: chartData.OM,
                borderColor: '#FFA726',
                backgroundColor: 'rgba(255, 167, 38, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            },
            {
                label: 'Moov Money',
                data: chartData.MOOV,
                borderColor: '#42A5F5',
                backgroundColor: 'rgba(66, 165, 245, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            },
            {
                label: 'Espèces',
                data: chartData.ESPECE,
                borderColor: '#66BB6A',
                backgroundColor: 'rgba(102, 187, 106, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            },
            {
                label: 'Carte',
                data: chartData.CARTE,
                borderColor: '#AB47BC',
                backgroundColor: 'rgba(171, 71, 188, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true
                }
            }
        }
    }
});

// Diagramme circulaire pour la répartition
const paiementPieCtx = document.getElementById('paiementPieChart').getContext('2d');
const pieData = [
    {{ $totalOM }},
    {{ $totalMOOV }},
    {{ $totalESPECE }},
    {{ $totalCARTE }}
];

new Chart(paiementPieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Orange Money', 'Moov Money', 'Espèces', 'Carte'],
        datasets: [{
            data: pieData,
            backgroundColor: ['#FFA726', '#42A5F5', '#66BB6A', '#AB47BC'],
            borderColor: '#ffffff',
            borderWidth: 3,
            hoverOffset: 20
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value.toLocaleString()} F (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '70%'
    }
});
@endif
</script>

<style>
/* Design Soft et Moderne */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --om-gradient: linear-gradient(135deg, #FFA726 0%, #FF9800 100%);
    --moov-gradient: linear-gradient(135deg, #42A5F5 0%, #2196F3 100%);
    --espece-gradient: linear-gradient(135deg, #66BB6A 0%, #4CAF50 100%);
    --carte-gradient: linear-gradient(135deg, #AB47BC 0%, #9C27B0 100%);
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.soft-input {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.soft-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.soft-shadow {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.soft-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Cartes de statistiques */
.stat-card {
    border: none;
    border-radius: 16px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.bg-om-gradient { background: var(--om-gradient); }
.bg-moov-gradient { background: var(--moov-gradient); }
.bg-espece-gradient { background: var(--espece-gradient); }
.bg-carte-gradient { background: var(--carte-gradient); }

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
    margin: 0 auto;
}

/* Badges de paiement */
.payment-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.payment-om { background: #FFF3E0; color: #EF6C00; border: 1px solid #FFB74D; }
.payment-moov { background: #E3F2FD; color: #1565C0; border: 1px solid #64B5F6; }
.payment-espece { background: #E8F5E8; color: #2E7D32; border: 1px solid #81C784; }
.payment-carte { background: #F3E5F5; color: #7B1FA2; border: 1px solid #BA68C8; }

/* Badges de statut */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-validé { background: #E8F5E8; color: #2E7D32; }
.status-en_attente { background: #FFF3E0; color: #EF6C00; }

/* Avatars utilisateurs */
.user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
}

/* Table header gradient */
.table-gradient {
    background: var(--primary-gradient);
    color: white;
}

/* Code de référence */
.reference-code {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    color: #495057;
}

/* État vide */
.empty-state {
    opacity: 0.6;
}

.empty-state i {
    font-size: 4rem;
}

/* Text colors */
.text-om { color: #FF9800; }
.text-moov { color: #2196F3; }
.text-espece { color: #4CAF50; }
.text-carte { color: #9C27B0; }

/* Responsive */
@media (max-width: 768px) {
    .glass-card {
        border-radius: 12px;
    }

    .stat-card .d-flex {
        flex-direction: column;
        text-align: center;
    }

    .stat-icon {
        margin-bottom: 10px;
    }
}

/* Animation pour les cartes de compagnie */
.compagnie-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.compagnie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
}
</style>
@endsection
