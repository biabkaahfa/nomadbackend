@extends('back.app')
@section('title', 'Ajouter un paiement')
@section('dashboard-header')<h3 class="page-title mt-5">Ajouter un paiement</h3>@endsection
@section('dashboard-content')
<form action="{{ route('paiements.store') }}" method="POST">@csrf
    <input type="number" name="montant" placeholder="Montant"  class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;">
    <input type="datetime-local" name="datePaiement"  class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;">
    <select name="moyenPaiement"  class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <option value="OM">OM</option>
        <option value="MOOV">MOOV</option>
        <option value="CARTE">CARTE</option>
        <option value="ESPECE">ESPECE</option>
    </select>
    <select name="statut"  class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <option value="EN_ATTENTE">EN_ATTENTE</option>
        <option value="SUCCES">SUCCES</option>
        <option value="ECHEC">ECHEC</option>
    </select>
    <input type="text" name="referenceTransaction" placeholder="Référence"  class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;">
    <input type="number" name="idUtilisateur" placeholder="ID utilisateur"  class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;">
    <button class="btn btn-primary">Valider</button>
</form>
@endsection