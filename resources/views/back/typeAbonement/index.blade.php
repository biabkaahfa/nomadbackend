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

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Taux</th>
                        <th>Prix</th>
                        <th>Max Tickets</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                    <tr>
                        <td>{{ $type->id }}</td>
                        <td>{{ $type->nom }}</td>
                        <td>{{ $type->taux }}%</td>
                        <td>{{ number_format($type->prix, 2) }} FCFA</td>
                        <td>{{ $type->maxTicket }}</td>
                        <td style="white-space: nowrap">
                            <a href="{{ route('type-abonements.edit', $type) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('type-abonements.destroy', $type) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun type d'abonnement trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
