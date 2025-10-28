<div class="container-fluid mt-4">
    <!-- Cartes de Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="stats-icon">
                    <i class="fas fa-bus"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $buses->count() }}</h3>
                    <p>Buses</p>
                    <span class="stats-trend up">
                        <i class="fas fa-arrow-up"></i>
                        Flotte
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="stats-icon">
                    <i class="fas fa-train-station"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $garres->count() }}</h3>
                    <p>Gares</p>
                    <span class="stats-trend up">
                        <i class="fas fa-arrow-up"></i>
                        Réseau
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="stats-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $voyagesJour }}</h3>
                    <p>Voyages Aujourd'hui</p>
                    <span class="stats-trend {{ $voyagesJour > 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $voyagesJour > 0 ? 'up' : 'down' }}"></i>
                        Programme
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card info">
                <div class="stats-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $occupationMoyenne }}%</h3>
                    <p>Taux d'Occupation</p>
                    <span class="stats-trend {{ $occupationMoyenne > 50 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $occupationMoyenne > 50 ? 'up' : 'down' }}"></i>
                        Moyen
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Voyages du Jour par Gare -->
    <div class="card card-modern mb-4">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-calendar-alt me-2"></i>
                Voyages du Jour par Gare
            </h4>
            <div class="card-actions">
                <span class="text-muted">{{ now()->translatedFormat('l d F Y') }}</span>
            </div>
        </div>
        <div class="card-body">
            @forelse($voyagesParGarre as $data)
                @if($data['voyages']->count() > 0)
                    <div class="garre-section">
                        <div class="garre-header">
                            <div class="garre-info">
                                <div class="garre-avatar">
                                    <i class="fas fa-train-station"></i>
                                </div>
                                <div>
                                    <h5>{{ $data['garre']->name }}</h5>
                                    <p class="text-muted mb-0">
                                        {{ $data['garre']->ville }} •
                                        {{ $data['voyages']->count() }} voyage(s) aujourd'hui
                                    </p>
                                </div>
                            </div>
                            <div class="garre-stats">
                                <span class="badge bg-primary">
                                    {{ $data['voyages']->where('trajet.idGarreDepart', $data['garre']->id)->count() }} départs
                                </span>
                                <span class="badge bg-success">
                                    {{ $data['voyages']->where('trajet.idGarreArrive', $data['garre']->id)->count() }} arrivées
                                </span>
                            </div>
                        </div>

                        <div class="voyages-grid">
                            @foreach($data['voyages'] as $voyage)
                                @php
                                    $heureDepart = \Carbon\Carbon::parse($voyage->heuresDepart);
                                    $now = \Carbon\Carbon::now();
                                    $isDepart = $voyage->trajet->idGarreDepart == $data['garre']->id;
                                @endphp
                                <div class="voyage-card {{ $isDepart ? 'departure' : 'arrival' }}">
                                    <div class="voyage-header">
                                        <div class="voyage-direction">
                                            <span class="direction-badge {{ $isDepart ? 'bg-primary' : 'bg-success' }}">
                                                {{ $isDepart ? 'Départ' : 'Arrivée' }}
                                            </span>
                                            <span class="voyage-time">
                                                {{ $heureDepart->format('H:i') }}
                                            </span>
                                        </div>
                                        <div class="voyage-status">
                                            @if($heureDepart->isPast())
                                                <span class="status-badge completed">Terminé</span>
                                            @elseif($heureDepart->diffInMinutes($now) <= 30)
                                                <span class="status-badge imminent">Imminent</span>
                                            @else
                                                <span class="status-badge upcoming">À venir</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="voyage-route">
                                        <div class="route-point departure">
                                            <i class="fas fa-map-marker-alt text-success"></i>
                                            <span>{{ $voyage->trajet->pointDepart }}</span>
                                        </div>
                                        <div class="route-line">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                        <div class="route-point arrival">
                                            <i class="fas fa-map-marker-alt text-danger"></i>
                                            <span>{{ $voyage->trajet->pointArrive }}</span>
                                        </div>
                                    </div>

                                    <div class="voyage-details">
                                        <div class="detail-item">
                                            <i class="fas fa-bus"></i>
                                            <span>{{ $voyage->bus->numeroBus ?? 'Non assigné' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-ticket-alt"></i>
                                            <span>{{ $voyage->tickets->count() }} tickets</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $voyage->bus->nombrePlaces ?? 0 }} places</span>
                                        </div>
                                    </div>

                                    <div class="voyage-progress">
                                        <div class="progress">
                                            @php
                                                $occupation = $voyage->bus ?
                                                    round(($voyage->tickets->count() / $voyage->bus->nombrePlaces) * 100, 2) : 0;
                                            @endphp
                                            <div class="progress-bar {{ $occupation > 80 ? 'bg-danger' : ($occupation > 50 ? 'bg-warning' : 'bg-success') }}"
                                                 style="width: {{ $occupation }}%">
                                                {{ $occupation }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if(!$loop->last)
                            <hr class="my-4">
                        @endif
                    </div>
                @endif
            @empty
                <div class="empty-state">
                    <i class="fas fa-calendar-times fa-3x"></i>
                    <h5>Aucun voyage aujourd'hui</h5>
                    <p>Aucun voyage n'est programmé pour aujourd'hui dans vos gares</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Buses et Gares -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card card-modern">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-bus me-2"></i>
                        Flotte de Buses
                    </h4>
                </div>
                <div class="card-body">
                    <div class="buses-grid">
                        @forelse($buses as $bus)
                            <div class="bus-card">
                                <div class="bus-header">
                                    <div class="bus-number">{{ $bus->numeroBus }}</div>
                                    <span class="bus-status available">Disponible</span>
                                </div>
                                <div class="bus-details">
                                    <div class="detail">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $bus->nombrePlaces }} places</span>
                                    </div>
                                    <div class="detail">
                                        <i class="fas fa-cog"></i>
                                        <span>{{ $bus->modele }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state-sm">
                                <i class="fas fa-bus"></i>
                                <p>Aucun bus enregistré</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card card-modern">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-train-station me-2"></i>
                        Gares du Réseau
                    </h4>
                </div>
                <div class="card-body">
                    <div class="garres-list">
                        @forelse($garres as $garre)
                            <div class="garre-item">
                                <div class="garre-avatar">
                                    <i class="fas fa-train-station"></i>
                                </div>
                                <div class="garre-info">
                                    <h6>{{ $garre->name }}</h6>
                                    <p class="text-muted mb-1">{{ $garre->ville }}</p>
                                    <small class="text-primary">
                                        {{ $garre->trajets_count }} trajet(s)
                                    </small>
                                </div>
                                <div class="garre-actions">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state-sm">
                                <i class="fas fa-train-station"></i>
                                <p>Aucune gare assignée</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
