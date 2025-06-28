@extends('back.app')
@section('title', 'Créer un ticket')
@section('dashboard-header')<h3 class="page-title mt-5">Créer un Ticket</h3>@endsection
@section('dashboard-content')
<form action="{{ route('tickets.store') }}" method="POST">@csrf
    <input type="datetime-local" name="dateReservation" class="form-control">
    <select name="statut" class="form-control">
        <option value="CONFIRME">CONFIRME</option>
        <option value="ANNULE">ANNULE</option>
    </select>
    <input type="number" name="idUtilisateur" placeholder="ID Utilisateur" class="form-control">
    <input type="number" name="idVoyage" placeholder="ID Voyage" class="form-control">
    <input type="number" name="idGarre" placeholder="ID Garre" class="form-control">
    <input type="date" name="dateScan" class="form-control">
    <input type="number" name="idPaiement" placeholder="ID Paiement" class="form-control">
    <button class="btn btn-primary">Créer</button>
</form>
@endsection