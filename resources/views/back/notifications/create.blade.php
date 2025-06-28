@extends('back.app')
@section('title', 'Créer une notification')
@section('dashboard-header')<h3 class="page-title mt-5">Nouvelle Notification</h3>@endsection
@section('dashboard-content')
<form action="{{ route('notifications.store') }}" method="POST">@csrf
    <input type="text" name="titre" placeholder="Titre" class="form-control">
    <textarea name="contenu" placeholder="Contenu" class="form-control"></textarea>
    <input type="date" name="DateEnvoie" class="form-control">
    <input type="number" name="idUtilisateur" placeholder="ID Utilisateur" class="form-control">
    <button class="btn btn-primary">Envoyer</button>
</form>
@endsection