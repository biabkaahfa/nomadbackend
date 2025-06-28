@extends('back.app')
@section('title', 'Notifications')
@section('dashboard-header')
<h4 class="mt-5">Historique des notifications envoyées</h4>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Titre</th>
                    <th>Contenu</th>
                    <th>Date d'envoi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notifications as $notification)
                <tr>
                    <td>{{ $notification->user->name ?? '-' }}</td>
                    <td>{{ $notification->titre }}</td>
                    <td>{{ $notification->contenu }}</td>
                    <td>{{ $notification->DateEnvoie }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
