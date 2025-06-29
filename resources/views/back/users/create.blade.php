<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($user) ? ($mode === 'show' ? 'Détails de l\'utilisateur' : 'Modifier l\'utilisateur') : 'Créer un utilisateur' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
        }
        .card-glass { 
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 20px; 
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 2.5rem; 
        }
        .btn-primary-gradient { 
            background: linear-gradient(45deg, #6a11cb, #2575fc); 
            border: none; 
            color: #fff; 
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary-gradient:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(106, 17, 203, 0.4);
            color: #fff;
        }
        .btn-secondary-gradient {
            background: linear-gradient(45deg, #6c757d, #495057);
            border: none;
            color: #fff;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
            color: #fff;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }
        .user-image {
            border-radius: 12px;
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .user-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .required {
            color: #dc3545;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }
        .loading {
            display: none;
        }
        .loading.show {
            display: inline-block;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-actif {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-inactif {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-glass">
                <h2 class="page-title">
                    <i class="fas fa-user me-2"></i>
                    {{ isset($user) ? ($mode === 'show' ? 'Détails de l\'utilisateur' : 'Modifier l\'utilisateur') : 'Créer un utilisateur' }}
                </h2>

                <!-- Messages d'alerte -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" enctype="multipart/form-data" id="userForm"
                      action="{{ $mode === 'edit' ? route('users.update', $user) : route('users.store') }}">

                    @csrf
                    @if(isset($user) && $mode === 'edit')
                        @method('PUT')
                    @endif

                    <div class="row g-4">
                        <!-- Nom -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Nom <span class="required">*</span>
                                </label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name ?? '') }}"
                                       {{ $mode === 'show' ? 'readonly' : 'required' }}
                                       placeholder="Entrez le nom complet">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope me-1"></i>
                                    Email <span class="required">*</span>
                                </label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email ?? '') }}"
                                       {{ $mode === 'show' ? 'readonly' : 'required' }}
                                       placeholder="exemple@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Téléphone -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone me-1"></i>
                                    Téléphone <span class="required">*</span>
                                </label>
                                <input type="tel" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                       value="{{ old('telephone', $user->telephone ?? '') }}"
                                       {{ $mode === 'show' ? 'readonly' : 'required' }}
                                       placeholder="+226 XX XX XX XX">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Statut <span class="required">*</span>
                                </label>
                                @if($mode === 'show')
                                    <div class="form-control-plaintext">
                                        <span class="status-badge status-{{ $user->statut ?? 'actif' }}">
                                            <i class="fas fa-{{ ($user->statut ?? 'actif') === 'actif' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                            {{ ($user->statut ?? 'actif') === 'actif' ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>
                                @else
                                    <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                        <option value="">Sélectionner un statut</option>
                                        <option value="actif" {{ (old('statut', $user->statut ?? 'actif') === 'actif') ? 'selected' : '' }}>
                                            <i class="fas fa-check-circle"></i> Actif
                                        </option>
                                        <option value="inactif" {{ (old('statut', $user->statut ?? '') === 'inactif') ? 'selected' : '' }}>
                                            <i class="fas fa-times-circle"></i> Inactif
                                        </option>
                                    </select>
                                @endif
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mot de passe -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Mot de passe
                                    @if($mode === 'create')
                                        <span class="required">*</span>
                                    @endif
                                    @if($mode === 'edit')
                                        <small class="text-muted">(Laisser vide pour ne pas modifier)</small>
                                    @endif
                                </label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       {{ $mode === 'show' ? 'readonly' : '' }}
                                       {{ $mode === 'create' ? 'required' : '' }}
                                       placeholder="••••••••"
                                       minlength="8">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($mode === 'create')
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Le mot de passe doit contenir au moins 8 caractères avec majuscules, minuscules, chiffres et caractères spéciaux.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Confirmation mot de passe (seulement pour la création) -->
                        @if($mode === 'create')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Confirmer le mot de passe <span class="required">*</span>
                                </label>
                                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                       required
                                       placeholder="••••••••"
                                       minlength="8">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Profil -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user-tag me-1"></i>
                                    Profil <span class="required">*</span>
                                </label>
                                <select name="idProfil" class="form-select @error('idProfil') is-invalid @enderror" 
                                        {{ $mode === 'show' ? 'disabled' : 'required' }}>
                                    <option value="">Sélectionner un profil</option>
                                    @foreach($profils as $profil)
                                        <option value="{{ $profil->id }}" 
                                                {{ (old('idProfil', $user->idProfil ?? '') == $profil->id) ? 'selected' : '' }}>
                                            {{ $profil->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idProfil')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Gare -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Gare
                                </label>
                                <select name="idGarre" class="form-select @error('idGarre') is-invalid @enderror" 
                                        {{ $mode === 'show' ? 'disabled' : '' }}>
                                    <option value="">Sélectionner une gare</option>
                                    @foreach($garres as $garre)
                                        <option value="{{ $garre->id }}" 
                                                {{ (old('idGarre', $user->idGarre ?? '') == $garre->id) ? 'selected' : '' }}>
                                            {{ $garre->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idGarre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Compagnie -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-building me-1"></i>
                                    Compagnie
                                </label>
                                <select name="idCompagnie" class="form-select @error('idCompagnie') is-invalid @enderror" 
                                        {{ $mode === 'show' ? 'disabled' : '' }}>
                                    <option value="">Sélectionner une compagnie</option>
                                    @foreach($compagnies as $compagnie)
                                        <option value="{{ $compagnie->id }}" 
                                                {{ (old('idCompagnie', $user->idCompagnie ?? '') == $compagnie->id) ? 'selected' : '' }}>
                                            {{ $compagnie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idCompagnie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Photo -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-camera me-1"></i>
                                    Photo de profil
                                </label>
                                @if(isset($user) && $user->image)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $user->image) }}" 
                                             alt="Photo de {{ $user->name }}" 
                                             class="user-image"
                                             width="120" height="120" 
                                             style="object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                       {{ $mode === 'show' ? 'disabled' : '' }}
                                       accept="image/*">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Formats acceptés : JPG, PNG, GIF. Taille maximale : 2MB
                                </div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-5 text-center">
                        @if($mode !== 'show')
                            <button type="submit" class="btn btn-primary-gradient me-3" id="submitBtn">
                                <i class="fas fa-save me-2"></i>
                                <span class="btn-text">{{ $mode === 'edit' ? 'Mettre à jour' : 'Créer l\'utilisateur' }}</span>
                                <span class="loading spinner-border spinner-border-sm ms-2" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </span>
                            </button>
                        @endif
                        
                        <a href="{{ route('users.index') }}" class="btn btn-secondary-gradient">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.querySelector('.loading');
    const btnText = document.querySelector('.btn-text');

    // Gestion de la soumission du formulaire
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Afficher le loading
            loading.classList.add('show');
            submitBtn.disabled = true;
            btnText.textContent = 'Traitement en cours...';
        });
    }

    // Prévisualisation de l'image
    const imageInput = document.querySelector('input[name="image"]');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'image-preview user-image mt-2';
                        preview.width = 120;
                        preview.height = 120;
                        preview.style.objectFit = 'cover';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-success')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
});
</script>

</body>
</html>