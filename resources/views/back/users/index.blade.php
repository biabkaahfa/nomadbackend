<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   

<style>
.user-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.user-card .card-header {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 15px 15px 0 0;
    padding: 1.5rem;
}

.user-card .card-header h3 {
    color: white;
    font-weight: 600;
    margin: 0;
}

.btn-gradient {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
    border-radius: 25px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    color: white;
}

.filter-container {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.form-control-modern {
    border: 2px solid #e3f2fd;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: white;
}

.table-modern {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.table-modern thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 1rem;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table-modern tbody tr {
    transition: all 0.3s ease;
    border: none;
}

.table-modern tbody tr:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    transform: scale(1.01);
}

.table-modern tbody td {
    padding: 1rem;
    border: none;
    vertical-align: middle;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.user-avatar-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.badge-modern {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success-modern {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.badge-danger-modern {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
}

.badge-info-modern {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.btn-action {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    border: none;
    margin: 0 2px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-action:hover {
    transform: translateY(-2px);
}

.btn-action-view {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.btn-action-edit {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

.btn-action-toggle {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.btn-action-delete {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.alert-modern {
    border: none;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
}

.alert-success-modern {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

.pagination-modern .page-link {
    border: none;
    border-radius: 8px;
    margin: 0 2px;
    color: #667eea;
    transition: all 0.3s ease;
}

.pagination-modern .page-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
}

.pagination-modern .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}
</style>


<div class="d-flex align-items-center justify-content-between">
    <div>
        <h2 class="mb-1" style="color: #2c3e50; font-weight: 700;">Gestion des Utilisateurs</h2>
        <p class="text-muted mb-0">Gérez et administrez tous vos utilisateurs</p>
    </div>
    <div class="stats-card" style="min-width: 200px;">
        <div class="text-center">
            <h3 class="mb-0" style="color: #667eea; font-weight: 700;">{{ $users->total() }}</h3>
            <small class="text-muted">Utilisateurs total</small>
        </div>
    </div>
</div>



<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card user-card fade-in-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        Liste des Utilisateurs
                    </h3>
                    <a href="{{ route('users.create') }}" class="btn btn-gradient">
                        <i class="fas fa-plus mr-2"></i> Nouveau Utilisateur
                    </a>
                </div>

                <div class="card-body" style="background: white; border-radius: 0 0 15px 15px;">
                    {{-- Filtres --}}
                    <div class="filter-container">
                        <h5 class="mb-3">
                            <i class="fas fa-filter mr-2" style="color: #667eea;"></i>
                            Filtres de recherche
                        </h5>
                        <form method="GET" action="{{ route('users.index') }}" class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted">Recherche générale</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background: #667eea; border: none; color: white;">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="search" class="form-control form-control-modern" 
                                           placeholder="Nom, email, téléphone..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label text-muted">Profil</label>
                                <select name="profil" class="form-control form-control-modern">
                                    <option value="">Tous les profils</option>
                                    @foreach($profils as $profil)
                                        <option value="{{ $profil->id }}" {{ request('profil') == $profil->id ? 'selected' : '' }}>
                                            {{ $profil->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label text-muted">Statut</label>
                                <select name="statut" class="form-control form-control-modern">
                                    <option value="">Tous les statuts</option>
                                    <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label text-muted">Gare</label>
                                <select name="garre" class="form-control form-control-modern">
                                    <option value="">Toutes les gares</option>
                                    @foreach($garres as $garre)
                                        <option value="{{ $garre->id }}" {{ request('garre') == $garre->id ? 'selected' : '' }}>
                                            {{ $garre->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label text-muted">Compagnie</label>
                                <select name="compagnie" class="form-control form-control-modern">
                                    <option value="">Toutes les compagnies</option>
                                    @foreach($compagnies as $compagnie)
                                        <option value="{{ $compagnie->id }}" {{ request('compagnie') == $compagnie->id ? 'selected' : '' }}>
                                            {{ $compagnie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 mb-3">
                                <button type="submit" class="btn btn-gradient mr-2">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- Messages de succès/erreur --}}
                    @if(session('success'))
                        <div class="alert alert-success-modern alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Tableau des utilisateurs --}}
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-image mr-2"></i>Photo</th>
                                    <th><i class="fas fa-user mr-2"></i>Nom</th>
                                    <th><i class="fas fa-envelope mr-2"></i>Email</th>
                                    <th><i class="fas fa-phone mr-2"></i>Téléphone</th>
                                    <th><i class="fas fa-user-tag mr-2"></i>Profil</th>
                                    <th><i class="fas fa-toggle-on mr-2"></i>Statut</th>
                                    <th><i class="fas fa-map-marker-alt mr-2"></i>Gare</th>
                                    <th><i class="fas fa-building mr-2"></i>Compagnie</th>
                                    <th><i class="fas fa-cogs mr-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            @if($user->image)
                                                <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" 
                                                     class="user-avatar">
                                            @else
                                                <div class="user-avatar-placeholder">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong style="color: #2c3e50;">{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">ID: #{{ $user->id }}</small>
                                        </td>
                                        <td>
                                            <span style="color: #667eea;">{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light" style="background: #f8f9fa; color: #495057; border-radius: 15px; padding: 0.4rem 0.8rem;">
                                                <i class="fas fa-phone mr-1"></i>{{ $user->telephone }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-modern badge-info-modern">
                                                {{ $user->profil->libelle ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-modern badge-{{ $user->statut === 'actif' ? 'success' : 'danger' }}-modern">
                                                <i class="fas fa-{{ $user->statut === 'actif' ? 'check' : 'times' }} mr-1"></i>
                                                {{ ucfirst($user->statut) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: #6c757d;">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $user->garre->nom ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: #6c757d;">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $user->compagnie->nom ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('users.show', $user) }}" class="btn btn-action btn-action-view" 
                                                   title="Voir les détails" data-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-action btn-action-edit" 
                                                   title="Modifier" data-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('users.toggleStatus', $user) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-action btn-action-toggle" 
                                                            title="{{ $user->statut === 'actif' ? 'Désactiver' : 'Activer' }}" 
                                                            data-toggle="tooltip">
                                                        <i class="fas fa-{{ $user->statut === 'actif' ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action btn-action-delete" 
                                                            title="Supprimer" data-toggle="tooltip">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                                                <h5>Aucun utilisateur trouvé</h5>
                                                <p>Aucun utilisateur ne correspond aux critères de recherche actuels.</p>
                                                <a href="{{ route('users.create') }}" class="btn btn-gradient mt-3">
                                                    <i class="fas fa-plus mr-2"></i>Créer le premier utilisateur
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            <nav>
                                <ul class="pagination pagination-modern">
                                    {{ $users->appends(request()->query())->links() }}
                                </ul>
                            </nav>
                        </div>
                    @endif

                    {{-- Informations de pagination --}}
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Affichage de {{ $users->firstItem() ?? 0 }} à {{ $users->lastItem() ?? 0 }} 
                            sur {{ $users->total() }} utilisateurs
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser les tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Animation au hover sur les lignes du tableau
    $('.table-modern tbody tr').hover(
        function() {
            $(this).addClass('shadow-sm');
        },
        function() {
            $(this).removeClass('shadow-sm');
        }
    );
    
    // Confirmation stylée pour la suppression
    $('form[onsubmit*="confirm"]').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        
        if (confirm('⚠️ Attention!\n\nÊtes-vous absolument sûr de vouloir supprimer cet utilisateur ?\n\n✅ Cette action est IRRÉVERSIBLE\n❌ Toutes les données associées seront perdues')) {
            form.submit();
        }
    });
});
</script>
@endpush
</body>
</html>