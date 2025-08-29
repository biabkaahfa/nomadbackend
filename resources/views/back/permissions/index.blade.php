@extends('back.app')

@section('title', 'Liste des Permissions')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Permissions</h4>
                <a href="{{ route('permissions.create') }}" class="btn btn-primary float-right viewbutton">Ajouter une permission</a>
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
                                    <th>Nom de la permission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DONNÉES STATIQUES TEMPORAIRES --}}
                                @foreach ($permissions as $permission )


                                <tr>
                                    <td>{{ $permission->id }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td class="text-right">

                                                <a href="{{ route('permissions.edit',$permission) }}" class="btn btn-sm btn-warning">Modifier</a>

                                                <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Supprimer cette permissions ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Supprimer</button>
                                                </form>
                                    </td>
                                </tr>

                                 @endforeach


                                {{-- FIN DES LIGNES STATIQUES --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
