@extends('back.app')
@section('title', 'Paiements')
@section('dashboard-header')
<h4 class="mt-5">Liste des paiements</h4>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Montant</th>
                    <th>Moyen</th>
                    <th>Statut</th>
                    <th>Référence</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paiements as $p)
                <tr>
                    <td>{{ $p->compagnie->name ?? '-' }}</td>
                    <td>{{ $p->montant }} F</td>
                    <td>{{ $p->moyenPaiement }}</td>
                    <td>{{ $p->statut }}</td>
                    <td>{{ $p->referenceTransaction }}</td>
                    <td>{{ $p->datePaiement }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
