<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Gares</title>
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
        }

        .btn-primary-soft { 
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
            border: none; 
            color: #fff; 
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .btn-primary-soft:hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 122, 224, 0.3);
            color: #fff;
        }

        .btn-success-soft {
            background: linear-gradient(135deg, #52c41a, #73d13d);
            border: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .btn-success-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(82, 196, 26, 0.3);
            color: #fff;
        }

        .btn-warning-soft {
            background: linear-gradient(135deg, #faad14, #ffc53d);
            border: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .btn-warning-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(250, 173, 20, 0.3);
            color: #fff;
        }

        .btn-danger-soft {
            background: linear-gradient(135deg, #ff4d4f, #ff7875);
            border: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .btn-danger-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 77, 79, 0.3);
        }

        .table-modern {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            background: white;
        }

        .table-modern thead {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .table-modern thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .table-modern tbody tr:hover {
            background-color: var(--light-blue);
            transform: scale(1.005);
        }

        .table-modern tbody td {
            padding: 1rem;
            vertical-align: middle;
            border: none;
        }

        .page-title {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2.2rem;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
            font-size: 0.9rem;
        }

        .search-box {
            border-radius: 8px;
            border: 2px solid var(--border-color);
            padding: 10px 16px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(108, 122, 224, 0.15);
        }

        .stats-card {
            background: linear-gradient(135deg, #fff, #f8fafc);
            color: #4a5568;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card i {
            color: var(--primary-color);
        }

        .stats-card h4 {
            color: #2d3748;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .badge-compagnie {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 0.4rem;
            justify-content: center;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #718096;
        }

        .empty-state i {
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        .pagination .page-link {
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin: 0 2px;
            padding: 0.5rem 0.75rem;
        }

        .pagination .page-link:hover {
            background-color: var(--light-blue);
            border-color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card card-glass">
                <h1 class="page-title">
                    <i class="fas fa-train me-3"></i>
                    Gestion des Gares
                </h1>

                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-train fa-2x mb-2"></i>
                            <h4>{{ $gares->total() }}</h4>
                            <p class="mb-0">Total Gares</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-building fa-2x mb-2"></i>
                            <h4>{{ $gares->unique('idCompagnie')->count() }}</h4>
                            <p class="mb-0">Compagnies</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-city fa-2x mb-2"></i>
                            <h4>{{ $gares->unique('ville')->count() }}</h4>
                            <p class="mb-0">Villes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-plus fa-2x mb-3"></i>
                            <a href="{{ route('garres.create') }}" class="btn btn-primary-soft btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Nouvelle Gare
                            </a>
                        </div>
                    </div>
                </div>

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

                <!-- Section de filtrage et recherche -->
                <div class="filter-section">
                    <form method="GET" action="{{ route('garres.index') }}" id="searchForm">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">
                                    <i class="fas fa-search me-1"></i>
                                    Rechercher
                                </label>
                                <input type="text" 
                                       class="form-control search-box" 
                                       id="search" 
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Nom de la gare, ville...">
                            </div>
                            <div class="col-md-4">
                                <label for="compagnie" class="form-label">
                                    <i class="fas fa-building me-1"></i>
                                    Compagnie
                                </label>
                                <select class="form-select search-box" id="compagnie" name="compagnie">
                                    <option value="">Toutes les compagnies</option>
                                    @foreach($compagnies as $compagnie)
                                        <option value="{{ $compagnie->id }}" 
                                                {{ request('compagnie') == $compagnie->id ? 'selected' : '' }}>
                                            {{ $compagnie->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-soft">
                                        <i class="fas fa-search me-1"></i>
                                        Rechercher
                                    </button>
                                    <a href="{{ route('garres.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Effacer
                                    </a>
                                    <a href="{{ route('garres.create') }}" class="btn btn-success-soft">
                                        <i class="fas fa-plus me-1"></i>
                                        Nouvelle Gare
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tableau des gares -->
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                               
                                <th><i class="fas fa-train me-2"></i>Nom</th>
                                <th><i class="fas fa-map-marker-alt me-2"></i>Localisation</th>
                                <th><i class="fas fa-city me-2"></i>Ville</th>
                                <th><i class="fas fa-building me-2"></i>Compagnie</th>
                                <th><i class="fas fa-calendar me-2"></i>Créé le</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gares as $garre)
                                <tr>
                                   
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-train text-primary me-2"></i>
                                            <strong>{{ $garre->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($garre->localisation)
                                            <i class="fas fa-map-pin text-success me-1"></i>
                                            {{ $garre->localisation }}
                                        @else
                                            <span class="text-muted fst-italic">Non spécifiée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-city me-2" style="color: var(--primary-color)"></i>
                                        {{ $garre->ville }}
                                    </td>
                                    <td>
                                        <span class="badge-compagnie">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $garre->compagnie->nom ?? 'Non assignée' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                           <div class="fw-semibold">{{ $garre->created_at?->format('d/m/Y') ?? 'Date inconnue' }}</div>
<small class="text-muted">{{ $garre->created_at?->format('H:i') ?? '--:--' }}</small>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('garres.show', $garre) }}" 
                                               class="btn btn-success-soft btn-sm" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('garres.edit', $garre) }}" 
                                               class="btn btn-warning-soft btn-sm" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('garres.destroy', $garre) }}" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette gare ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger-soft btn-sm" 
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="fas fa-train fa-4x"></i>
                                            <h5 class="mt-3 mb-2">Aucune gare trouvée</h5>
                                            <p class="mb-3">
                                                @if(request('search') || request('compagnie'))
                                                    Aucun résultat ne correspond à vos critères de recherche.
                                                @else
                                                    Commencez par créer votre première gare.
                                                @endif
                                            </p>
                                            @if(!request('search') && !request('compagnie'))
                                                <a href="{{ route('garres.create') }}" class="btn btn-primary-soft">
                                                    <i class="fas fa-plus me-2"></i>
                                                    Créer une gare
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($gares->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $gares->appends(request()->query())->links() }}
                    </div>
                @endif

                <!-- Informations de pagination -->
                @if($gares->count() > 0)
                    <div class="text-center text-muted mt-3">
                        <small>
                            Affichage de {{ $gares->firstItem() }} à {{ $gares->lastItem() }} 
                            sur {{ $gares->total() }} résultats
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit du formulaire de recherche quand on change la compagnie
    const compagnieSelect = document.getElementById('compagnie');
    if (compagnieSelect) {
        compagnieSelect.addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });
    }

    // Recherche en temps réel (optionnel - décommentez si souhaité)
    /*
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 500);
    });
    */

    // Auto-hide des alertes de succès
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Animation des cartes statistiques
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.stats-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Confirmation de suppression améliorée
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette gare ?\n\nCette action est irréversible.')) {
                this.submit();
            }
        });
    });
});
</script>

</body>
</html>