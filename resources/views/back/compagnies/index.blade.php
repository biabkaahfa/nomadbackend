@extends('back.app')

@section('title', 'Liste des Compagnies')

@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <h4 class="card-title mt-5">Compagnies</h4>
        <a href="{{ route('compagnies.create') }}" class="btn btn-primary float-right">Ajouter une compagnie</a>
    </div>
</div>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                     <th>Logo</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                     <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compagnies as $compagnie)
                <tr>
                    <td>{{ $compagnie->id }}</td>
                    <td> <img src="{{ $compagnie->imageUrl() }}" alt="{{ $compagnie->name }}" width="100"
                                                height="100">  </td>
                    <td>{{ $compagnie->name }}</td>
                    <td>{{ $compagnie->email }}</td>
                    <td>{{ $compagnie->telephone }}</td>
                    <td>{{ $compagnie->descriptions }}</td>
                    
                    <td>
                        <a href="{{ route('compagnies.edit', $compagnie) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form action="{{ route('compagnies.destroy', $compagnie) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
