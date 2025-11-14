@extends('back.app')

@section('title', 'Types d\'Abonnement')

@section('dashboard-header')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Liste des Types d'Abonnement</h3>
        <div class="card-tools">
            <a href="{{ route('type-abonements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter
            </a>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Libellé</th>
                        <th>Type Compagnie</th>
                        <th>Prix Mensuel</th>
                        <th>Notifications/Jour</th>
                        <th>Accès Notes</th>
                        <th>Commission Sur Place</th>
                        <th>Commission En Ligne</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                    <tr>
                        <td>{{ $type->id }}</td>
                        <td>
                            <strong>{{ $type->nom }}</strong>
                            @if($type->description)
                                <br><small class="text-muted">{{ Str::limit($type->description, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $type->libelle }}</td>
                        <td>
                            <span class="badge badge-{{ $type->type_compagnie === 'privee' ? 'primary' : 'success' }}">
                                {{ $type->type_compagnie === 'privee' ? 'Privée' : 'Publique' }}
                            </span>
                        </td>
                        <td>{{ number_format($type->prix_mensuel, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($type->limite_notifications === -1)
                                <span class="badge badge-success">Illimité</span>
                            @else
                                <span class="badge badge-info">{{ $type->limite_notifications }}/jour</span>
                            @endif
                        </td>
                        <td>
                            @if($type->acces_notes)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Oui</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Non</span>
                            @endif
                        </td>
                        <td>
                            @if($type->commission_sur_place > 0)
                                {{ number_format($type->commission_sur_place, 0, ',', ' ') }} FCFA
                            @else
                                <span class="text-success">Gratuit</span>
                            @endif
                        </td>
                        <td>
                            @if($type->commission_en_ligne > 0)
                                {{ number_format($type->commission_en_ligne, 0, ',', ' ') }} FCFA
                            @else
                                <span class="text-success">Gratuit</span>
                            @endif
                        </td>
                        <td>
                            @if($type->est_actif)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge badge-danger">Inactif</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap">
                            <a href="{{ route('type-abonements.edit', $type) }}" class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('type-abonements.destroy', $type) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <a href="{{ route('type-abonements.show', $type) }}" class="btn btn-sm btn-info" title="Détails">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center">Aucun type d'abonnement trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Résumé des fonctionnalités -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Résumé des Types d'Abonnement</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $freemium = $types->firstWhere('nom', 'freemium');
                                $standard = $types->firstWhere('nom', 'standard');
                                $premium = $types->firstWhere('nom', 'premium');
                                $public = $types->firstWhere('nom', 'public');
                            @endphp

                            <div class="col-md-3">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Freemium</span>
                                        <span class="info-box-number">{{ $freemium ? number_format($freemium->prix_mensuel, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-star"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Standard</span>
                                        <span class="info-box-number">{{ $standard ? number_format($standard->prix_mensuel, 0, ',', ' ') . ' FCFA' : '300 000 FCFA' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-crown"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Premium</span>
                                        <span class="info-box-number">{{ $premium ? number_format($premium->prix_mensuel, 0, ',', ' ') . ' FCFA' : '500 000 FCFA' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-building"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Public</span>
                                        <span class="info-box-number">{{ $public ? number_format($public->prix_mensuel, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    background: #fff;
    display: flex;
    margin-bottom: 1rem;
    min-height: 80px;
    padding: 0.5rem;
    position: relative;
}
.info-box .info-box-icon {
    border-radius: 0.25rem;
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
}
.info-box .info-box-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.8;
    flex: 1;
    padding: 0 10px;
}
.info-box-text {
    text-transform: uppercase;
    font-weight: 600;
    font-size: 0.875rem;
}
.info-box-number {
    font-weight: 700;
    font-size: 1.5rem;
}
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endpush
