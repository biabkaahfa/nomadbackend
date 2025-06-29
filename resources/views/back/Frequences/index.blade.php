@extends('back.app')

@section('title', 'Fréquences des trajets')

{{-- @section('dashboard-header')
    <h4 class="mt-5">Historique des fréquences programmées</h4>
@endsection --}}
@section('dashboard-header')
<div class="row align-items-center">
    <div class="col">
        <h4 class="card-title mt-5">Historique des fréquences programmées</h4>
        <a href="{{ route('frequences.create') }}" class="btn btn-primary float-right">Ajouter une frequence</a>
    </div>
</div>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Trajet</th>
                    <th>Nombre place minimum</th>
                    <th>Jours couvert</th>
                    <th>Heures Depart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($frequences as $frequence)
                <tr>
                    <td>{{ $frequence->trajet->pointDepart }} → {{ $frequence->trajet->pointArrive }}</td>
                    <td>{{ $frequence->nombrePlaceMinimum }}</td>
                    <td>{{ $frequence->jourSemaine }}</td>
                    <td>{{ $frequence->heureDepart }}</td>
                    <td>
                        <a href="{{ route('frequences.edit',$frequence) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form action="{{ route('frequences.destroy',$frequence) }}" method="POST" style="display:inline;">
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
