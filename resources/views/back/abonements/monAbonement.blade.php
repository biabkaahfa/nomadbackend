@extends('back.app')

@section('title', 'Mes Abonnements')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Mes Abonnements</h4>
                <a href="{{ route('abonnements.create') }}" class="btn btn-primary float-right viewbutton">Nouvel abonnement</a>
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
                                    <th>Type d'abonnement</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Durée (jours)</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($abonnements as $abonnement)
                                <tr>
                                    <td>{{ $abonnement->typeAbonement->nom }}</td>
                                    <td>{{ $abonnement->date_debut->format('d/m/Y') }}</td>
                                    <td>{{ $abonnement->date_fin->format('d/m/Y') }}</td>
                                    <td>{{ $abonnement->duree }}</td>
                                    <td>
                                        @if($abonnement->isActive())
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-secondary">Expiré</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($abonnement->isActive())
                                            <span class="text-muted">En cours</span>
                                        @else
                                            <a href="{{ route('abonnements.renew', $abonnement->id) }}" class="btn btn-sm btn-primary">Renouveler</a>
                                        @endif
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
