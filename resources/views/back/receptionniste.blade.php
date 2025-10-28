<div class="container-fluid mt-4">
    <!-- Cartes de Statistiques pour Réceptionniste -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="stats-icon">
                    <i class="fas fa-route"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $voyagesDisponibles->count() }}</h3>
                    <p>Voyages Disponibles</p>
                    <span class="stats-trend {{ $voyagesDisponibles->count() > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $voyagesDisponibles->count() > 0 ? 'up' : 'down' }}"></i>
                        Multi-gares
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="stats-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $ticketsVendusAujourdhui }}</h3>
                    <p>Tickets Vendus</p>
                    <span class="stats-trend {{ $ticketsVendusAujourdhui > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $ticketsVendusAujourdhui > 0 ? 'up' : 'down' }}"></i>
                        Ce jour
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="stats-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ number_format($revenusAujourdhui, 0, ',', ' ') }} F</h3>
                    <p>Chiffre d'Affaires</p>
                    <span class="stats-trend {{ $revenusAujourdhui > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $revenusAujourdhui > 0 ? 'up' : 'down' }}"></i>
                        Aujourd'hui
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Interface principale de vente multi-gares -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card card-modern">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Vente de Tickets - {{ $garre->name }}
                            </h4>
                            <p class="text-muted mb-0 mt-1">
                                Tickets valables dans toutes les gares desservant la destination
                            </p>
                        </div>
                        <div class="sale-indicator">
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Multi-gares Actif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres de recherche -->
                    <div class="search-filters mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Destination</label>
                                <select class="form-select" id="filterDestination">
                                    <option value="">Toutes les destinations</option>
                                    @foreach($voyagesDisponibles->pluck('trajet.pointArrive')->unique() as $destination)
                                        <option value="{{ $destination }}">{{ $destination }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date de voyage</label>
                                <input type="date" class="form-control" id="filterDate" value="{{ today()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Compagnie</label>
                                <select class="form-select" id="filterCompagnie">
                                    <option value="">Toutes les compagnies</option>
                                    @foreach($voyagesDisponibles->pluck('trajet.compagnie.name')->unique() as $compagnie)
                                        <option value="{{ $compagnie }}">{{ $compagnie }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="voyages-sales-grid" id="voyagesGrid">
                        @forelse($voyagesDisponibles as $voyage)
                            @php
                                $heureDepart = \Carbon\Carbon::parse($voyage->heuresDepart);
                                $now = \Carbon\Carbon::now();
                                $placesDisponibles = $voyage->bus ?
                                    ($voyage->bus->nombrePlaces - $voyage->tickets->count()) : 0;
                                $occupation = $voyage->bus ?
                                    round(($voyage->tickets->count() / $voyage->bus->nombrePlaces) * 100, 2) : 0;
                                $garresDesservies = $voyage->trajet->garres ?? collect();
                                $prix = $voyage->trajet->prix ?? 5000;
                            @endphp
                            <div class="voyage-sale-card"
                                 data-destination="{{ $voyage->trajet->pointArrive }}"
                                 data-date="{{ $voyage->dateDepart->format('Y-m-d') }}"
                                 data-compagnie="{{ $voyage->trajet->compagnie->name ?? '' }}"
                                 data-places="{{ $placesDisponibles }}">
                                <div class="voyage-header">
                                    <div class="voyage-title">
                                        <h5>
                                            <i class="fas fa-route me-2 text-primary"></i>
                                            {{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}
                                        </h5>
                                        <div class="voyage-meta">
                                            <span class="compagnie-badge">
                                                <i class="fas fa-building me-1"></i>
                                                {{ $voyage->trajet->compagnie->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="voyage-price">
                                        <span class="price-tag">{{ number_format($prix, 0, ',', ' ') }} F</span>
                                        <small class="text-muted">par ticket</small>
                                    </div>
                                </div>

                                <div class="voyage-details">
                                    <div class="detail-row">
                                        <div class="detail-item">
                                            <i class="fas fa-clock text-info"></i>
                                            <div>
                                                <span class="fw-semibold">{{ $heureDepart->format('H:i') }}</span>
                                                <small class="text-muted d-block">
                                                    {{ $voyage->dateDepart->translatedFormat('l d F') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-bus text-warning"></i>
                                            <div>
                                                <span>{{ $voyage->bus->numeroBus ?? 'N/A' }}</span>
                                                <small class="text-muted d-block">
                                                    {{ $voyage->bus->modele ?? '' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gares desservies -->
                                    <div class="garres-section">
                                        <div class="garres-header">
                                            <i class="fas fa-train-station text-success me-2"></i>
                                            <small class="fw-semibold">Gares desservies :</small>
                                        </div>
                                        <div class="garres-list">
                                            @foreach($garresDesservies->take(3) as $garreDesservie)
                                                <span class="garre-badge {{ $garreDesservie->id == $garre->id ? 'current-garre' : '' }}">
                                                    {{ $garreDesservie->name }}
                                                    @if($garreDesservie->id == $garre->id)
                                                        <i class="fas fa-check ms-1"></i>
                                                    @endif
                                                </span>
                                            @endforeach
                                            @if($garresDesservies->count() > 3)
                                                <span class="garre-badge more">
                                                    +{{ $garresDesservies->count() - 3 }} autres
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="detail-row">
                                        <div class="detail-item">
                                            <i class="fas fa-users text-success"></i>
                                            <span>{{ $placesDisponibles }} places libres</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-chart-pie text-primary"></i>
                                            <span>{{ $occupation }}% occupation</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="occupation-bar">
                                    <div class="progress">
                                        <div class="progress-bar {{ $occupation > 80 ? 'bg-danger' : ($occupation > 50 ? 'bg-warning' : 'bg-success') }}"
                                             style="width: {{ $occupation }}%">
                                            <span class="progress-text">{{ $occupation }}%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="sale-actions">
                                    @if($placesDisponibles > 0)
                                        <button class="btn btn-primary btn-sale"
                                                onclick="openSaleModal({{ $voyage->id }}, {{ $prix }}, '{{ $voyage->trajet->pointDepart }}', '{{ $voyage->trajet->pointArrive }}')">
                                            <i class="fas fa-cart-plus me-2"></i>
                                            Vendre un Ticket
                                        </button>
                                        <button class="btn btn-outline-info btn-sm"
                                                onclick="showVoyageDetails({{ $voyage->id }})"
                                                title="Détails du voyage">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-danger disabled" disabled>
                                            <i class="fas fa-times me-2"></i>
                                            Complet
                                        </button>
                                    @endif
                                </div>

                                @if($placesDisponibles > 0 && $placesDisponibles <= 5)
                                    <div class="availability-alert">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Plus que {{ $placesDisponibles }} place(s) !
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun voyage disponible</h5>
                                <p class="text-muted mb-4">Aucun voyage n'est disponible pour la vente de tickets aujourd'hui</p>
                                <button class="btn btn-primary" onclick="reloadVoyages()">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Actualiser
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Panneau latéral des ventes rapides -->
        <div class="col-lg-4 mb-4">
            <!-- Statistiques de vente en temps réel -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Ventes du Jour - {{ $garre->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="sales-stats">
                        <div class="sale-stat-item">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ $ticketsVendusAujourdhui }}</h4>
                                <p>Tickets Vendus</p>
                                <small class="text-muted">Multi-gares</small>
                            </div>
                        </div>
                        <div class="sale-stat-item">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ number_format($revenusAujourdhui, 0, ',', ' ') }} F</h4>
                                <p>Chiffre d'Affaires</p>
                                <small class="text-muted">Aujourd'hui</small>
                            </div>
                        </div>
                        <div class="sale-stat-item">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ $voyagesDisponibles->count() }}</h4>
                                <p>Destinations</p>
                                <small class="text-muted">Disponibles</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info multi-gares -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Info Multi-gares
                    </h5>
                </div>
                <div class="card-body">
                    <div class="multi-gare-info">
                        <div class="info-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Tickets valables dans toutes les gares</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-train-station text-primary me-2"></i>
                            <span>Départ depuis n'importe quelle gare desservie</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-users text-warning me-2"></i>
                            <span>Flexibilité totale pour les voyageurs</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card card-modern">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <button class="quick-action-btn" onclick="openBulkSale()">
                            <div class="action-icon bg-warning">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <span>Vente en Groupe</span>
                        </button>
                        <button class="quick-action-btn" onclick="openReservation()">
                            <div class="action-icon bg-info">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <span>Réservation</span>
                        </button>
                        <button class="quick-action-btn" onclick="openRefund()">
                            <div class="action-icon bg-danger">
                                <i class="fas fa-undo"></i>
                            </div>
                            <span>Remboursement</span>
                        </button>
                        <button class="quick-action-btn" onclick="openReports()">
                            <div class="action-icon bg-success">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <span>Rapport Journalier</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de vente multi-gares -->
<div class="modal fade" id="saleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleModalTitle">Vente de Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="saleModalContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filtrage des voyages
    document.addEventListener('DOMContentLoaded', function() {
        const filterDestination = document.getElementById('filterDestination');
        const filterDate = document.getElementById('filterDate');
        const filterCompagnie = document.getElementById('filterCompagnie');

        function filterVoyages() {
            const destination = filterDestination.value.toLowerCase();
            const date = filterDate.value;
            const compagnie = filterCompagnie.value.toLowerCase();

            const voyageCards = document.querySelectorAll('.voyage-sale-card');

            voyageCards.forEach(card => {
                const cardDestination = card.getAttribute('data-destination').toLowerCase();
                const cardDate = card.getAttribute('data-date');
                const cardCompagnie = card.getAttribute('data-compagnie').toLowerCase();
                const cardPlaces = parseInt(card.getAttribute('data-places'));

                const matchDestination = !destination || cardDestination.includes(destination);
                const matchDate = !date || cardDate === date;
                const matchCompagnie = !compagnie || cardCompagnie.includes(compagnie);

                if (matchDestination && matchDate && matchCompagnie) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        filterDestination.addEventListener('change', filterVoyages);
        filterDate.addEventListener('change', filterVoyages);
        filterCompagnie.addEventListener('change', filterVoyages);
    });

    function openSaleModal(voyageId, prix, depart, arrivee) {
        // Simulation d'ouverture de modal de vente
        document.getElementById('saleModalTitle').textContent = `Vente - ${depart} → ${arrivee}`;

        // Contenu dynamique du modal
        const modalContent = `
            <div class="sale-modal-content">
                <div class="voyage-info mb-4 p-3 bg-light rounded">
                    <h6>Détails du voyage</h6>
                    <div class="row">
                        <div class="col-6">
                            <strong>Trajet:</strong> ${depart} → ${arrivee}
                        </div>
                        <div class="col-6">
                            <strong>Prix:</strong> ${prix.toLocaleString()} F
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-info-circle me-1"></i>
                            Ce ticket est valable dans toutes les gares desservant cette destination
                        </small>
                    </div>
                </div>

                <form id="saleForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom du voyageur</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gare de départ choisie</label>
                            <select class="form-select" required>
                                <option value="">Sélectionnez une gare</option>
                                <option value="{{ $garre->id }}">{{ $garre->name }} (Gare actuelle)</option>
                                <!-- Autres gares seraient chargées dynamiquement -->
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nombre de tickets</label>
                            <input type="number" class="form-control" value="1" min="1" max="10" required>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-warning-soft rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Important
                        </h6>
                        <p class="mb-0 small">
                            Le voyageur peut embarquer depuis n'importe quelle gare desservant cette destination.
                            Le ticket est valable uniquement pour le voyage sélectionné.
                        </p>
                    </div>
                </form>
            </div>
        `;

        document.getElementById('saleModalContent').innerHTML = modalContent;
        const modal = new bootstrap.Modal(document.getElementById('saleModal'));
        modal.show();
    }

    function openBulkSale() {
        showNotification('Ouverture de la vente en groupe multi-gares', 'warning');
    }

    function openReservation() {
        showNotification('Ouverture du module de réservation multi-gares', 'info');
    }

    function openRefund() {
        showNotification('Ouverture du module de remboursement', 'danger');
    }

    function openReports() {
        showNotification('Génération du rapport journalier multi-gares', 'success');
    }

    function showVoyageDetails(voyageId) {
        showNotification(`Affichage des détails du voyage #${voyageId} avec les gares desservies`, 'primary');
    }

    function reloadVoyages() {
        showNotification('Actualisation des voyages disponibles...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

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
</script>
@endpush
