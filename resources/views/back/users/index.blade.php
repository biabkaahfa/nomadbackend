<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .page-title {
            font-weight: 700;
            color: #343a40;
        }
        .card-glass {
            background: rgba(255, 255, 255, 0.85);
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
        }
        .btn-primary-gradient {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
            color: #fff;
        }
        .btn-primary-gradient:hover {
            filter: brightness(1.1);
        }
        .badge-status {
            padding: 0.5em 0.75em;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-actif {
            background: #28a745;
            color: white;
        }
        .badge-inactif {
            background: #dc3545;
            color: white;
        }
        .table-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">Gestion des Utilisateurs</h2>
            <p class="text-muted">Liste et actions des utilisateurs</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="btn btn-primary-gradient">
                <i class="fas fa-plus me-2"></i>Nouvel Utilisateur
            </a>
        </div>
    </div>

    <div class="card card-glass mb-4 p-4">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Nom, Email, Tel..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Profil</label>
                    <select name="profil" class="form-select">
                        <option value="">Tous</option>
                        @foreach($profils as $profil)
                            <option value="{{ $profil->id }}" {{ request('profil') == $profil->id ? 'selected' : '' }}>{{ $profil->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gare</label>
                    <select name="garre" class="form-select">
                        <option value="">Toutes</option>
                        @foreach($garres as $garre)
                            <option value="{{ $garre->id }}" {{ request('garre') == $garre->id ? 'selected' : '' }}>{{ $garre->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-gradient w-100">
                        <i class="fas fa-search me-2"></i>Rechercher
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card card-glass">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Profil</th>
                        <th>Statut</th>
                        <th>Gare</th>
                        <th>Compagnie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" class="table-avatar" alt="avatar">
                                @else
                                    <div class="bg-secondary text-white text-center rounded-circle" style="width:40px; height:40px; line-height:40px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telephone }}</td>
                            <td>{{ $user->profil->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-status {{ $user->statut === 'actif' ? 'badge-actif' : 'badge-inactif' }}">
                                    {{ ucfirst($user->statut) }}
                                </span>
                            </td>
                            <td>{{ $user->garre->name ?? 'N/A' }}</td>
                            <td>{{ $user->compagnie->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Aucun utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
