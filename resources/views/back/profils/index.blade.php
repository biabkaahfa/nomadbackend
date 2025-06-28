@extends('back.app')

@section('title', 'Liste des Profils')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Profils</h4>
                <a href="#" class="btn btn-primary float-right viewbutton">Ajouter un profil</a>
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
                                    <th>ID Profil</th>
                                    <th>Nom du profil</th>
                                    <th>Description</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DONNÉES STATIQUES TEMPORAIRES --}}
                                <tr>
                                    <td>1</td>
                                    <td>admin_general</td>
                                    <td>Gère toute la plateforme</td>
                                    <td>
                                        <span class="badge badge-success">Gérer utilisateurs</span>
                                        <span class="badge badge-success">Créer compagnies</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v ellipse_color"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"><i class="fas fa-eye m-r-5"></i> Voir</a>
                                                <a class="dropdown-item" href="#"><i class="fas fa-edit m-r-5"></i> Modifier</a>
                                                <a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt m-r-5"></i> Supprimer</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>admin_compagnie</td>
                                    <td>Gère les gares et trajets de sa compagnie</td>
                                    <td>
                                        <span class="badge badge-info">Gérer gares</span>
                                        <span class="badge badge-info">Gérer trajets</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v ellipse_color"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"><i class="fas fa-eye m-r-5"></i> Voir</a>
                                                <a class="dropdown-item" href="#"><i class="fas fa-edit m-r-5"></i> Modifier</a>
                                                <a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt m-r-5"></i> Supprimer</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>voyageur</td>
                                    <td>Client pouvant acheter des tickets</td>
                                    <td>
                                        <span class="badge badge-secondary">Voir trajets</span>
                                        <span class="badge badge-secondary">Acheter ticket</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v ellipse_color"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"><i class="fas fa-eye m-r-5"></i> Voir</a>
                                                <a class="dropdown-item" href="#"><i class="fas fa-edit m-r-5"></i> Modifier</a>
                                                <a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt m-r-5"></i> Supprimer</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                {{-- FIN DES LIGNES STATIQUES --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
