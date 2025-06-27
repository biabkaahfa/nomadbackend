@extends('back.app')
@section('title', 'Bus disponibles')
@section('dashboard-header')
<h4 class="mt-5">Liste des bus</h4>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Places totales</th>
                    <th>Places disponibles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buses as $bus)
                <tr>
                    <td>{{ $bus->id }}</td>
                    <td>{{ $bus->nombrePlaces }}</td>
                    <td>{{ $bus->nombrePlaceDispo }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
