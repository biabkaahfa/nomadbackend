
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
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('garres.index') }}">
                    <i class="fas fa-home me-1"></i>Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('garres.index') }}">
                    <i class="fas fa-train me-1"></i>Gares
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                @if($mode === 'create')
                    <i class="fas fa-plus me-1"></i>Créer
                @elseif($mode === 'edit')
                    <i class="fas fa-edit me-1"></i>Modifier
                @else
                    <i class="fas fa-eye me-1"></i>Détails
                @endif
            </li>
        </ol>
    </nav>

    <!-- Titre de la page -->
    <h1 class="page-title">
        @if($mode === 'create')
            <i class="fas fa-plus-circle me-2"></i>Créer une nouvelle gare
        @elseif($mode === 'edit')
            <i class="fas fa-edit me-2"></i>Modifier la gare
        @else
            <i class="fas fa-train me-2"></i>Détails de la gare
        @endif
    </h1>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erreurs de validation :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-glass">
                @if($mode === 'show')
                    <!-- Mode Affichage -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Informations de la gare
                        </h2>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">
                                        <i class="fas fa-tag me-2"></i>Nom de la gare
                                    </div>
                                    <div class="info-value">{{ $garre->name ?? 'Non défini' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">
                                        <i class="fas fa-building me-2"></i>Compagnie
                                    </div>
                                    <div class="info-value">{{ $garre->compagnie->name ?? 'Non définie' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Ville
                                    </div>
                                    <div class="info-value">{{ $garre->ville ?? 'Non définie' }}</div>
                                </div>
                            </div>

                            @if($garre->localisation)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">
                                        <i class="fas fa-location-arrow me-2"></i>Localisation
                                    </div>
                                    <div class="info-value">{{ $garre->localisation }}</div>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-plus me-2"></i>Date de création
                                    </div>
                                    <div class="info-value">{{ $garre->created_at ? $garre->created_at->format('d/m/Y à H:i') : 'Non définie' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-edit me-2"></i>Dernière modification
                                    </div>
                                    <div class="info-value">{{ $garre->updated_at ? $garre->updated_at->format('d/m/Y à H:i') : 'Non définie' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions pour le mode show -->
                    <div class="action-section">
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('garres.edit', $garre) }}" class="btn btn-warning-soft">
                                <i class="fas fa-edit me-2"></i>Modifier
                            </a>
                            <a href="{{ route('garres.index') }}" class="btn btn-secondary-soft">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                            <button type="button" class="btn btn-danger-soft" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                        </div>
                    </div>

                @else
                    <!-- Mode Création/Édition -->
                    <form action="{{ $mode === 'create' ? route('garres.store') : route('garres.update', $garre) }}"
                          method="POST" novalidate>
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
            {{-- Champ caché pour forcer la compagnie du user --}}
            <input type="hidden" name="idCompagnie" value="{{ $user->idCompagnie }}">
            <input type="text" class="form-control" value="{{ $user->compagnie->name ?? 'Ma compagnie' }}" readonly>
            <label><i class="fas fa-building me-2"></i>Compagnie</label>
        @elseif($isAdminGeneral)
            {{-- Affichage du select pour admin général --}}
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

                                <!-- Localisation -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text"
                                               class="form-control @error('localisation') is-invalid @enderror"
                                               id="localisation"
                                               name="localisation"
                                               placeholder="Localisation"
                                               value="{{ old('localisation', isset($garre) ? $garre->localisation : '') }}">
                                        <label for="localisation">
                                            <i class="fas fa-location-arrow me-2"></i>Localisation
                                        </label>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Coordonnées GPS ou description de l'emplacement (optionnel, unique)
                                        </div>
                                        @error('localisation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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

@if($mode === 'show')
<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Êtes-vous sûr de vouloir supprimer cette gare ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible.
                </div>
                <p><strong>Gare :</strong> {{ $garre->name ?? 'N/A' }}</p>
                <p><strong>Compagnie :</strong> {{ $garre->compagnie->name ?? 'N/A' }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-soft" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form action="{{ route('garres.destroy', $garre) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-soft">
                        <i class="fas fa-trash me-2"></i>Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection



