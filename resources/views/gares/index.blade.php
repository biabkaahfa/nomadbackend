

@extends('back.app')

@section('title', 'Tickets')


@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Liste des Garres</h4>
                
                <a href="{{ route('garres.create') }}" class="btn btn-primary float-right viewbutton">Ajouter une garre</a>
            </div>
        </div>
    </div>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        
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
        <th><i class="fas fa-route me-2"></i>Trajets ravitaillés</th> <!-- 🆕 Nouvelle colonne -->
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
            <i class="fas fa-city me-2 text-primary"></i>
            {{ $garre->ville }}
        </td>
        <td>
            <span class="badge-compagnie">
                <i class="fas fa-building me-1"></i>
                {{ $garre->compagnie->name ?? 'Non assignée' }}
            </span>
        </td>
        <td> <!-- 🆕 Cellule trajets ravitaillés -->
            @if($garre->trajets->isEmpty())
                <span class="text-muted">Aucun</span>
            @else
                <ul class="mb-0 ps-3">
                    @foreach($garre->trajets as $trajet)
                        <li>{{ $trajet->pointDepart }} → {{ $trajet->pointArrive }}</li>
                    @endforeach
                </ul>
            @endif
        </td>
        <td>
            <div class="text-center">
                <div class="fw-semibold">{{ $garre->created_at?->format('d/m/Y') ?? 'Date inconnue' }}</div>
                <small class="text-muted">{{ $garre->created_at?->format('H:i') ?? '--:--' }}</small>
            </div>
        </td>
        <td>
            <div class="action-buttons">
                <a href="{{ route('garres.show', $garre) }}" class="btn btn-success-soft btn-sm" title="Voir les détails">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('garres.edit', $garre) }}" class="btn btn-warning-soft btn-sm" title="Modifier">
                    <i class="fas fa-edit"></i>
                </a>
                <form method="POST" action="{{ route('garres.destroy', $garre) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette gare ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-soft btn-sm" title="Supprimer">
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
    </div>
</div>
@endsection



<body>



</body>
