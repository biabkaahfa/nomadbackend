
@extends('back.app')  

@section('title', 'Liste des Permissions')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Profil</h4>
                
                <a href="{{ route('profils.create') }}" class="btn btn-primary float-right viewbutton">Ajouter un profil</a>
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
                <th>Nom</th>
                <th>Description</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profils as $profil)
                <tr>
                    <td>{{ $profil->id }}</td>
                    <td>{{ $profil->name }}</td>
                    <td>{{ $profil->description }}</td>
                    <td>
                        @foreach ($profil->permissions as $permission)
                            <span class="badge badge-success">{{ $permission->name }}</span>
                        @endforeach
                    </td>
                    <td class="actions">
                        <a href="#">👁️ Voir</a>
                        <a href="{{ route("profils.edit",$profil) }}">✏️ Modifier</a>
                        <a href="#" class="delete">🗑️ Supprimer</a>
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

