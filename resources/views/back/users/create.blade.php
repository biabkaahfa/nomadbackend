<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .card-glass { background: rgba(255, 255, 255, 0.9); border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); padding: 2rem; }
        .btn-primary-gradient { background: linear-gradient(to right, #6a11cb, #2575fc); border: none; color: #fff; }
        .btn-primary-gradient:hover { filter: brightness(1.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card card-glass">
        <h3 class="mb-4">
            {{ isset($user) ? ($mode === 'show' ? 'Détails de l\'utilisateur' : 'Modifier l\'utilisateur') : 'Créer un utilisateur' }}
        </h3>

        <form method="POST" enctype="multipart/form-data"
              action="{{ isset($user) ? ($mode === 'edit' ? route('users.update', $user) : '#') : route('users.store') }}">

            @csrf
            @if(isset($user) && $mode === 'edit')
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $user->name ?? '') }}"
                           {{ $mode === 'show' ? 'readonly' : '' }}>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $user->email ?? '') }}"
                           {{ $mode === 'show' ? 'readonly' : '' }}>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                           value="{{ old('telephone', $user->telephone ?? '') }}"
                           {{ $mode === 'show' ? 'readonly' : '' }}>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control"
                           {{ $mode === 'show' ? 'readonly' : '' }}>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Profil</label>
                    <select name="idProfil" class="form-select" {{ $mode === 'show' ? 'disabled' : '' }}>
                        @foreach($profils as $profil)
                            <option value="{{ $profil->id }}" {{ (old('idProfil', $user->idProfil ?? '') == $profil->id) ? 'selected' : '' }}>
                                {{ $profil->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gare</label>
                    <select name="idGarre" class="form-select" {{ $mode === 'show' ? 'disabled' : '' }}>
                        @foreach($garres as $garre)
                            <option value="{{ $garre->id }}" {{ (old('idGarre', $user->idGarre ?? '') == $garre->id) ? 'selected' : '' }}>
                                {{ $garre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Compagnie</label>
                    <select name="idCompagnie" class="form-select" {{ $mode === 'show' ? 'disabled' : '' }}>
                        @foreach($compagnies as $compagnie)
                            <option value="{{ $compagnie->id }}" {{ (old('idCompagnie', $user->idCompagnie ?? '') == $compagnie->id) ? 'selected' : '' }}>
                                {{ $compagnie->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Photo</label><br>
                    @if(isset($user) && $user->image)
                        <img src="{{ asset('storage/' . $user->image) }}" width="100" class="mb-2">
                    @endif
                    <input type="file" name="image" class="form-control" {{ $mode === 'show' ? 'disabled' : '' }}>
                </div>
            </div>

            @if($mode !== 'show')
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary-gradient">
                        {{ isset($user) ? 'Mettre à jour' : 'Créer' }}
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
