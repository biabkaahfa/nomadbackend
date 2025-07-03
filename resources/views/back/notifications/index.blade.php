@extends('back.app')
@section('title', 'Notifications')
@section('dashboard-header')
<h4 class="mt-5">Historique des notifications envoyées</h4>
 <a href="{{ route('notifications.create') }}" class="btn btn-primary float-right viewbutton">Envoyer  une Notifications</a>
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
                    @foreach($notification->voyage->tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->user->name ?? $ticket->name ?? '-' }}</td>
                            <td>{{ $notification->titre }}</td>
                            <td>{{ $notification->contenu }}</td>
                            <td>{{ $notification->DateEnvoie }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
