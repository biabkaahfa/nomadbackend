@extends('back.app')

@section('title', 'Créer un Abonnement Public')

@section('dashboard-header')
    <h3 class="page-title mt-5">
        {{ isset($abonementPublic) ? "Modifier l'Abonnement Public" : "Création d’un Abonnement Public" }}
    </h3>
@endsection

@section('dashboard-content')
<form action="{{ isset($abonementPublic) ? route('abonementPublic.update', $abonementPublic->id) : route('abonementPublic.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($abonementPublic))
        @method('PUT')
    @endif

    {{-- ✅ Sélection Compagnie --}}
    <label>Compagnie associée</label>
    <select name="idCompagnie" id="idCompagnie" class="form-control form-control-lg fs-5 mb-3" required>
        <option value="">-- Sélectionner une compagnie --</option>
        @foreach ($compagnies as $compagnie)
            <option value="{{ $compagnie->id }}"
                data-prix="{{ $compagnie->personalisationCard->prix ?? 0 }}"
                {{ (old('idCompagnie', $abonementPublic->idCompagnie ?? '') == $compagnie->id) ? 'selected' : '' }}>
                {{ $compagnie->name }}
            </option>
        @endforeach
    </select>

    {{-- ✅ Infos Abonné --}}
    <label>Nom</label>
    <input type="text" name="nom" class="form-control form-control-lg fs-5 mb-3"
           value="{{ old('nom', $abonementPublic->nom ?? '') }}" required>

    <label>Prénom</label>
    <input type="text" name="prenom" class="form-control form-control-lg fs-5 mb-3"
           value="{{ old('prenom', $abonementPublic->prenom ?? '') }}" required>

    <label>Profession</label>
    <input type="text" name="profession" class="form-control form-control-lg fs-5 mb-3"
           value="{{ old('profession', $abonementPublic->profession ?? '') }}">

    <label>Établissement</label>
    <input type="text" name="etablissement" class="form-control form-control-lg fs-5 mb-3"
           value="{{ old('etablissement', $abonementPublic->etablissement ?? '') }}">

    <label>Date de naissance</label>
    <input type="date" name="dateNaiss" class="form-control form-control-lg fs-5 mb-3"
           value="{{ old('dateNaiss', $abonementPublic->dateNaiss ?? '') }}" required>

    <label>Durée (jours)</label>
    <input type="number" name="duree" id="duree" class="form-control form-control-lg fs-5 mb-3"
           value="{{ old('duree', $abonementPublic->duree ?? 30) }}" required>

    <label>Photo</label>
    <input type="file" name="Photo" class="form-control form-control-lg fs-5 mb-3">
    @if(isset($abonementPublic) && $abonementPublic->Photo)
        <img src="{{ asset('storage/' . $abonementPublic->Photo) }}" class="img-thumbnail mt-2" style="max-width:150px;">
    @endif

    {{-- ✅ Paiement --}}
    <h4 class="mt-4">Informations de paiement</h4>

    <label>Montant</label>
    <input type="number" name="montant" id="montant" readonly
           class="form-control form-control-lg fs-5 mb-3">

    <label>Moyen de paiement</label>
    <select name="moyenPaiement" id="moyenPaiement" class="form-control form-control-lg fs-5 mb-3">
        <option value="OM">Orange Money</option>
        <option value="MOOV">MOOV Money</option>
        <option value="CARTE">CARTE</option>
        <option value="ESPECE">ESPECE</option>
    </select>

    <div id="refPaiementDiv" style="display: none;">
        <label>Référence transaction</label>
        <input type="text" name="referenceTransaction"
               class="form-control form-control-lg fs-5 mb-3">
    </div>

    <label>Statut du paiement</label>
    <select name="statutPaiement" class="form-control form-control-lg fs-5 mb-3">
        <option value="SUCCES">SUCCES</option>
        <option value="EN_ATTENTE">EN ATTENTE</option>
        <option value="ECHEC">ECHEC</option>
    </select>

    <label>Téléphone du payeur (facultatif)</label>
    <input type="text" name="telephone_paiement"
           class="form-control form-control-lg fs-5 mb-3"
           placeholder="Téléphone pour le reçu"
           value="{{ old('telephone_paiement') }}">

    <button type="submit" class="btn btn-primary mt-3">
        {{ isset($abonementPublic) ? "Mettre à jour" : "Créer l'abonnement" }}
    </button>
    <a href="{{ route('abonementPublic.index') }}" class="btn btn-secondary mt-3">Annuler</a>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const idCompagnie = document.getElementById('idCompagnie');
    const montant = document.getElementById('montant');
    const moyenPaiement = document.getElementById('moyenPaiement');
    const refPaiementDiv = document.getElementById('refPaiementDiv');

    // 🎯 Quand on change de compagnie -> montant mis à jour
    idCompagnie.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const prix = selected.getAttribute('data-prix');
        montant.value = prix || '';
    });

    // 🎯 Si paiement ≠ espèce -> afficher champ référence
    moyenPaiement.addEventListener('change', function () {
        refPaiementDiv.style.display = (this.value !== 'ESPECE') ? 'block' : 'none';
    });
});
</script>
@endsection
