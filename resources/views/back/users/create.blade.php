

@extends('back.app')

@section('title', isset($trajet) ? 'Modifier un Trajet' : 'Ajouter un Trajet')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($trajet) ? 'Modifier' : 'Ajouter' }} un Utilisateur
        
    </h3>
@endsection

@section('dashboard-content')
   
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
@endsection


