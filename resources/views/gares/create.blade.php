<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($mode === 'create')
            Créer une gare
        @elseif($mode === 'edit')
            Modifier la gare
        @else
            Détails de la gare
        @endif
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6c7ae0;
            --secondary-color: #a8b8f0;
            --success-color: #52c41a;
            --warning-color: #faad14;
            --danger-color: #ff4d4f;
            --light-blue: #e6f3ff;
            --soft-gray: #f8f9fa;
            --border-color: #e8e8e8;
        }

        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            color: #4a5568;
        }

        .card-glass { 
            background: rgba(255, 255, 255, 0.98); 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem; 
            margin-bottom: 2rem;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            color: var(--primary-color);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(108, 122, 224, 0.15);
        }

        .btn-primary-soft { 
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
            border: none; 
            color: #fff; 
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-primary-soft:hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 122, 224, 0.3);
            color: #fff;
        }

        .btn-secondary-soft {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
            border: none;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-secondary-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            color: #fff;
        }

        .btn-warning-soft {
            background: linear-gradient(135deg, var(--warning-color), #ffc53d);
            border: none;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-warning-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(250, 173, 20, 0.3);
            color: #fff;
        }

        .btn-danger-soft {
            background: linear-gradient(135deg, var(--danger-color), #ff7875);
            border: none;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-danger-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 77, 79, 0.3);
            color: #fff;
        }

        .page-title {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        .breadcrumb {
            background: linear-gradient(135deg, var(--light-blue), rgba(255, 255, 255, 0.8));
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 2rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
            font-size: 0.9rem;
        }

        .info-card {
            background: linear-gradient(135deg, #fff, #f8fafc);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .info-card .info-label {
            color: #718096;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .info-card .info-value {
            color: #2d3748;
            font-size: 1rem;
            font-weight: 500;
        }

        .form-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .section-title {
            color: #2d3748;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--light-blue);
            display: flex;
            align-items: center;
        }

        .required::after {
            content: " *";
            color: var(--danger-color);
        }

        .form-text {
            color: #718096;
            font-size: 0.875rem;
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: var(--danger-color);
        }

        .action-section {
            background: var(--soft-gray);
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }

        .readonly-field {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

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
                                        <select class="form-select @error('idCompagnie') is-invalid @enderror" 
                                                id="idCompagnie" 
                                                name="idCompagnie" 
                                                required>
                                            <option value="">Sélectionner une compagnie</option>
                                            @if(isset($compagnies))
                                                @foreach($compagnies as $compagnie)
                                                    <option value="{{ $compagnie->id }}" 
                                                            {{ old('idCompagnie', isset($garre) ? $garre->idCompagnie : '') == $compagnie->id ? 'selected' : '' }}>
                                                        {{ $compagnie->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <label for="idCompagnie" class="required">
                                            <i class="fas fa-building me-2"></i>Compagnie
                                        </label>
                                        @error('idCompagnie')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });

    // Form validation enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });

            // Remove is-invalid class on input
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        }
    });
</script>

</body>
</html>