<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4">
                <h3 class="text-center mb-4">Créer un compte utilisateur</h3>

                <!-- Affichage des erreurs -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Adresse email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmer mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Profil</label>
                            <select name="idProfil" class="form-select" required>
                                <option value="">-- Sélectionner un profil --</option>
                                @foreach($profils as $profil)
                                    <option value="{{ $profil->id }}" {{ old('idProfil') == $profil->id ? 'selected' : '' }}>
                                        {{ $profil->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select" required>
                                <option value="">-- Statut --</option>
                                <option value="actif" {{ old('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gare (optionnel)</label>
                            <select name="idGarre" class="form-select">
                                <option value="">-- Aucune --</option>
                                @foreach($garres as $garre)
                                    <option value="{{ $garre->id }}" {{ old('idGarre') == $garre->id ? 'selected' : '' }}>
                                        {{ $garre->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Compagnie (optionnel)</label>
                            <select name="idCompagnie" class="form-select">
                                <option value="">-- Aucune --</option>
                                @foreach($compagnies as $compagnie)
                                    <option value="{{ $compagnie->id }}" {{ old('idCompagnie') == $compagnie->id ? 'selected' : '' }}>
                                        {{ $compagnie->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Photo de profil (optionnel)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary px-4">Créer le compte</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optionnel si tu as besoin de fonctionnalités dynamiques comme dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
