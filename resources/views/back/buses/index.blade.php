@extends('back.app')

@section('title', 'Bus disponibles')

@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <h4 class="card-title mt-5">Listes des Buses</h4>
        <a href="{{ route('buses.create') }}" class="btn btn-primary float-right">Ajouter un Bus</a>
    </div>
</div>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numéro Bus</th>
                    <th>Places totales</th>
                    <th>Places disponibles</th>
                    <th>Status</th>
                    <th>Compagnie</th> 
                    <th>Actions</th> 
                </tr>
            </thead>
            <tbody>
                @foreach($buses as $bus)
                <tr>
                    <td>{{ $bus->id }}</td>
                    <td>{{ $bus->numeroBus }}</td>
                    <td>{{ $bus->nombrePlaces }}</td>
                    <td>{{ $bus->nombrePlaceDispo }}</td>
                    <td>{{ $bus->status }}</td>
                    <td>{{ $bus->compagnie->name ?? '' }}</td> 
                    <td>
                        <a href="{{ route('buses.edit', $bus) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form action="{{ route('buses.destroy', $bus) }}" method="POST" style="display:inline;">
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
