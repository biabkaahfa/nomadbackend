
  @php
    $pageTitle = match($mode) {
        'create' => 'Créer une gare',
        'edit' => 'Modifier la gare',
        default => 'Détails de la gare',
    };
@endphp


@extends('back.app')
@section('title', $pageTitle)

@section('dashboard-header')
<div class="container my-5">
    <!-- Breadcrumb et titres existants... -->

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-glass">
                @if($mode === 'show')
                    <!-- Mode Affichage avec carte -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Informations de la gare
                        </h2>

                        <div class="row">
                            <!-- Informations existantes... -->

                            @if($garre->localisation)
                            <div class="col-12 mb-4">
                                <div class="info-card">
                                    <div class="info-label">
                                        <i class="fas fa-map-marked-alt me-2"></i>Position sur la carte
                                    </div>
                                    <div class="info-value">
                                        <div id="mapPreview" style="height: 300px; border-radius: 8px;"></div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-map-pin me-1"></i>
                                                Coordonnées : {{ $garre->localisation }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                @else
                    <!-- Mode Création/Édition avec carte -->
                    <form action="{{ $mode === 'create' ? route('garres.store') : route('garres.update', $garre) }}"
                          method="POST" novalidate id="garreForm">
                        @csrf
                        @if($mode === 'edit')
                            @method('PUT')
                        @endif

                        <div class="form-section">
                            <h2 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Informations principales
                            </h2>

                            <div class="row">
                                <!-- Nom de la gare -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               placeholder="Nom de la gare"
                                               value="{{ old('name', isset($garre) ? $garre->name : '') }}"
                                               required>
                                        <label for="name" class="required">
                                            <i class="fas fa-tag me-2"></i>Nom de la gare
                                        </label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Compagnie -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        @php
                                            $user = Auth::user();
                                            $isAdminCompagnie = $user->profil->name === 'Admin compagnie';
                                            $isAdminGeneral = $user->profil->name === 'Admin général';
                                        @endphp

                                        @if($isAdminCompagnie)
                                            <input type="hidden" name="idCompagnie" value="{{ $user->idCompagnie }}">
                                            <input type="text" class="form-control" value="{{ $user->compagnie->name ?? 'Ma compagnie' }}" readonly>
                                            <label><i class="fas fa-building me-2"></i>Compagnie</label>
                                        @elseif($isAdminGeneral)
                                            <select class="form-select @error('idCompagnie') is-invalid @enderror"
                                                    id="idCompagnie"
                                                    name="idCompagnie"
                                                    required>
                                                <option value="">Sélectionner une compagnie</option>
                                                @foreach($compagnies as $compagnie)
                                                    <option value="{{ $compagnie->id }}"
                                                        {{ old('idCompagnie', isset($garre) ? $garre->idCompagnie : '') == $compagnie->id ? 'selected' : '' }}>
                                                        {{ $compagnie->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="idCompagnie" class="required">
                                                <i class="fas fa-building me-2"></i>Compagnie
                                            </label>
                                            @error('idCompagnie')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>
                                </div>

                                <!-- Ville -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text"
                                               class="form-control @error('ville') is-invalid @enderror"
                                               id="ville"
                                               name="ville"
                                               placeholder="Ville"
                                               value="{{ old('ville', isset($garre) ? $garre->ville : '') }}"
                                               required>
                                        <label for="ville" class="required">
                                            <i class="fas fa-map-marker-alt me-2"></i>Ville
                                        </label>
                                        @error('ville')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Localisation (champ caché pour les coordonnées) -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text"
                                               class="form-control @error('localisation') is-invalid @enderror"
                                               id="localisation"
                                               name="localisation"
                                               placeholder="Coordonnées GPS"
                                               value="{{ old('localisation', isset($garre) ? $garre->localisation : '') }}"
                                               readonly>
                                        <label for="localisation">
                                            <i class="fas fa-location-arrow me-2"></i>Coordonnées GPS
                                        </label>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Sélectionnez la position sur la carte ci-dessous
                                        </div>
                                        @error('localisation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Carte de sélection -->
                                <div class="col-12 mb-4">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            Sélectionnez la position sur la carte
                                        </div>
                                        <div class="card-body p-0">
                                            <div id="map" style="height: 400px; width: 100%;"></div>
                                            <div class="p-3 bg-light">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Cliquez sur la carte pour positionner la gare.
                                                    <span id="coordinatesDisplay">Aucune sélection</span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions pour les modes create/edit -->
                        <div class="action-section">
                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <button type="submit" class="btn btn-primary-soft">
                                    <i class="fas fa-save me-2"></i>
                                    {{ $mode === 'create' ? 'Créer la gare' : 'Mettre à jour' }}
                                </button>
                                <a href="{{ route('garres.index') }}" class="btn btn-secondary-soft">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Inclure Leaflet CSS et JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map, #mapPreview {
        border-radius: 8px;
        z-index: 1;
    }
    .leaflet-container {
        font-family: inherit;
    }
    .coordinates-info {
        background: white;
        padding: 8px 12px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        font-size: 14px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ Mode Création/Édition
    @if($mode !== 'show')
        initializeMap();
    @else
        // ✅ Mode Affichage
        @if($garre->localisation)
            showMapPreview();
        @endif
    @endif

    function initializeMap() {
        // Centre par défaut (Côte d'Ivoire)
        const defaultLat = 7.5399;
        const defaultLng = -5.5471;
        const defaultZoom = 7;

        // Initialiser la carte
        const map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

        // Ajouter la couche OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marqueur initial
        let marker = null;
        const localisationField = document.getElementById('localisation');

        // Si des coordonnées existent déjà, placer le marqueur
        if (localisationField.value) {
            const coords = parseCoordinates(localisationField.value);
            if (coords) {
                marker = L.marker([coords.lat, coords.lng]).addTo(map);
                map.setView([coords.lat, coords.lng], 15);
                updateCoordinatesDisplay(coords.lat, coords.lng);
            }
        }

        // Gestion du clic sur la carte
        map.on('click', function(e) {
            const { lat, lng } = e.latlng;

            // Supprimer l'ancien marqueur
            if (marker) {
                map.removeLayer(marker);
            }

            // Ajouter le nouveau marqueur
            marker = L.marker([lat, lng]).addTo(map);

            // Formater les coordonnées pour le champ localisation
            const coordinates = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

            // Mettre à jour le champ localisation
            localisationField.value = coordinates;

            // Mettre à jour l'affichage
            updateCoordinatesDisplay(lat, lng);
        });

        function updateCoordinatesDisplay(lat, lng) {
            const display = document.getElementById('coordinatesDisplay');
            display.innerHTML = `<strong>Lat:</strong> ${lat.toFixed(6)}, <strong>Lng:</strong> ${lng.toFixed(6)}`;
            display.style.color = '#28a745';
        }

        function parseCoordinates(localisation) {
            if (!localisation) return null;

            const parts = localisation.split(',');
            if (parts.length === 2) {
                const lat = parseFloat(parts[0].trim());
                const lng = parseFloat(parts[1].trim());

                if (!isNaN(lat) && !isNaN(lng)) {
                    return { lat, lng };
                }
            }
            return null;
        }

        // Recherche d'adresse par nom de ville
        const villeInput = document.getElementById('ville');
        let searchTimeout;

        villeInput.addEventListener('blur', function() {
            if (villeInput.value.length > 2) {
                searchByVille(villeInput.value);
            }
        });

        function searchByVille(villeName) {
            // Si on a déjà des coordonnées, ne pas rechercher
            if (localisationField.value) return;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(villeName + ', Côte d\'Ivoire')}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const result = data[0];
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lon);

                        // Centrer la carte sur la ville
                        map.setView([lat, lng], 12);

                        // Mettre à jour l'affichage mais pas le champ localisation
                        // L'utilisateur doit cliquer pour confirmer
                        updateCoordinatesDisplay(lat, lng);

                        // Afficher un message
                        const display = document.getElementById('coordinatesDisplay');
                        display.innerHTML += ' - <em>Cliquez pour confirmer</em>';
                    }
                })
                .catch(error => console.error('Erreur recherche:', error));
        }
    }

    function showMapPreview() {
        const localisation = "{{ $garre->localisation }}";
        const coords = parseCoordinates(localisation);

        if (!coords) return;

        const map = L.map('mapPreview').setView([coords.lat, coords.lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([coords.lat, coords.lng])
            .addTo(map)
            .bindPopup(`
                <strong>{{ $garre->name }}</strong><br>
                {{ $garre->ville }}<br>
                <small>${localisation}</small>
            `)
            .openPopup();

        function parseCoordinates(localisation) {
            if (!localisation) return null;

            const parts = localisation.split(',');
            if (parts.length === 2) {
                const lat = parseFloat(parts[0].trim());
                const lng = parseFloat(parts[1].trim());

                if (!isNaN(lat) && !isNaN(lng)) {
                    return { lat, lng };
                }
            }
            return null;
        }
    }
});
</script>
@endsection
