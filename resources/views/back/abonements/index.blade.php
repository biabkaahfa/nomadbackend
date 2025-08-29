@extends('back.app')

@section('title', 'Liste des Abonnements')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Abonnements</h4>
                <a href="{{ route('abonnements.create') }}" class="btn btn-primary float-right viewbutton">Ajouter un abonnement</a>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body booking_card">
                    <div class="table-responsive">
                        <table class="datatable table table-striped table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type d'abonnement</th>
                                    <th>Compagnie associée</th>
                                    <th>Date Fin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($abonnements as $abonnement)
                                <tr>
                                    <td>{{ $abonnement->id }}</td>
                                    <td>{{ $abonnement->typeAbonement->nom ?? 'Non défini' }}</td>
                                    <td>{{ $abonnement->compagnie->name ?? 'Non associée' }}</td>
                                    <td>{{ $abonnement->dateFin }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('abonnements.edit', $abonnement) }}" class="btn btn-sm btn-warning">Modifier</a>
                                        <form action="{{ route('abonnements.destroy', $abonnement) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Supprimer cet abonnement ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
