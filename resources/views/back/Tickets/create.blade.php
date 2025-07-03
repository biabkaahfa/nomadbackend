@extends('back.app')

@section('title', 'Créer un ticket - Achat sur place')

@section('dashboard-header')
<h3 class="page-title mt-5">Création d’un Ticket (Achat sur place)</h3>
@endsection

@section('dashboard-content')
<form action="{{ route('tickets.store') }}" method="POST">
    @csrf

    {{-- Date de réservation --}}
    <label>Date de réservation</label>
    <input type="datetime-local" name="dateReservation" id="dateReservation"
        class="form-control form-control-lg fs-5 mb-3" readonly
        style="font-size: 1.3rem; padding: 1rem; height:auto;"
        value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">

    {{-- Statut du ticket --}}
    <label>Statut du ticket</label>
    <select name="statut" class="form-control form-control-lg fs-5 mb-3" required
        style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <option value="CONFIRME">Confirmé</option>
        <option value="ANNULE">Annulé</option>
        <option value="REPORTE">Reporté</option>
        <option value="UTILISE">Utilisé</option>
    </select>

    {{-- Mode d'achat --}}
    <input type="hidden" name="typeAchat" value="sur_place">

    {{-- Mode de réception --}}
    <label>Mode de réception</label>
    <select name="modeReception" id="modeReception"
        class="form-control form-control-lg fs-5 mb-3" required
        style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <option value="email">Email</option>
        <option value="papier">Papier</option>
        <option value="application">Application</option>
    </select>

    {{-- Impression ticket --}}
    <div id="printTicketDiv" style="display: none;">
        <button type="button" class="btn btn-secondary mt-3" id="printBtn" disabled>
            🖨️ Imprimer le ticket
        </button>
    </div>

    {{-- Infos client --}}
    <label>Nom complet</label>
    <input type="text" name="name" class="form-control form-control-lg fs-5 mb-3" required
        placeholder="Nom du client" style="font-size: 1.3rem; padding: 1rem; height:auto;">

    <label>Téléphone</label>
    <input type="text" name="telephone" class="form-control form-control-lg fs-5 mb-3" required
        placeholder="Numéro de téléphone" style="font-size: 1.3rem; padding: 1rem; height:auto;">

    <label>Email</label>
    <input type="email" name="email" class="form-control form-control-lg fs-5 mb-3" required
        placeholder="Adresse email" style="font-size: 1.3rem; padding: 1rem; height:auto;">

    {{-- Sélection du voyage --}}
    <label>Voyage</label>
    <select name="idVoyage" id="idVoyage" class="form-control" required
        style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <option value="">-- Choisir un voyage --</option>
        @foreach($voyages as $voyage)
            @php
                $placesRestantes = $voyage->bus
                    ? $voyage->bus->placesDisponible
                    : max(0, $voyage->trajet->frequences
                        ->where('heureDepart', $voyage->heuresDepart)
                        ->first()?->nombrePlaceMinimum - $voyage->tickets->count() ?? 0);
            @endphp
            <option value="{{ $voyage->id }}"
                data-prix="{{ $voyage->trajet->prix }}"
                data-date="{{ $voyage->dateDepart }}"
                data-heure="{{ $voyage->heuresDepart }}"
                data-places="{{ $placesRestantes }}">
                {{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}
                | {{ \Carbon\Carbon::parse($voyage->dateDepart)->format('d/m/Y') }}
                à {{ \Carbon\Carbon::parse($voyage->heuresDepart)->format('H:i') }}
            </option>
        @endforeach
    </select>

    {{-- Affichage des places restantes --}}
    <strong id="placesRestantesInfo" class="text-success fs-5"></strong>

    {{-- Paiement --}}
    <h4 class="mt-4">Informations de paiement</h4>

    <label>Montant</label>
    <input type="number" name="montant" id="montant" readonly
        class="form-control form-control-lg fs-5 mb-3"
        style="font-size: 1.3rem; padding: 1rem; height:auto;">

    <label>Moyen de paiement</label>
    <select name="moyenPaiement" id="moyenPaiement"
        class="form-control form-control-lg fs-5 mb-3"
        style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <option value="OM">Orange Money</option>
        <option value="MOOV">MOOV Money</option>
        <option value="CARTE">CARTE</option>
        <option value="ESPECE">ESPECE</option>
    </select>

    <div id="refPaiementDiv" style="display: none;">
        <label>Référence transaction</label>
        <input type="text" name="referenceTransaction"
            class="form-control form-control-lg fs-5 mb-3"
            style="font-size: 1.3rem; padding: 1rem; height:auto;">
    </div>

    <label>Statut du paiement</label>
    <select name="statutPaiement"
        class="form-control form-control-lg fs-5 mb-3"
        style="font-size: 1.3rem; padding: 1rem; height:auto;">
        <option value="SUCCES">SUCCES</option>
        <option value="EN_ATTENTE">EN ATTENTE</option>
        <option value="ECHEC">ECHEC</option>
    </select>

    <label>Téléphone du payeur (facultatif)</label>
    <input type="text" name="telephone_paiement"
        class="form-control form-control-lg fs-5 mb-3"
        placeholder="Téléphone pour le reçu"
        style="font-size: 1.3rem; padding: 1rem; height:auto;"
        value="{{ old('telephone') }}">

    {{-- Urgence --}}
    <h4 class="mt-4">Personne à prévenir en cas d'urgence</h4>

    <label>Nom de la personne à prévenir</label>
    <input type="text" name="namePersonneAPrevenir" required
        class="form-control form-control-lg fs-5 mb-3"
        placeholder="Nom de la personne à contacter"
        style="font-size: 1.3rem; padding: 1rem; height:auto;">

    <label>Numéro de téléphone</label>
    <input type="text" name="numeroPersonneAPrevenir" required
        class="form-control form-control-lg fs-5 mb-3"
        placeholder="Numéro de téléphone"
        style="font-size: 1.3rem; padding: 1rem; height:auto;">

    <label>Email (facultatif)</label>
    <input type="email" name="emailPersonneAPrevenir"
        class="form-control form-control-lg fs-5 mb-3"
        placeholder="Email de la personne à prévenir"
        style="font-size: 1.3rem; padding: 1rem; height:auto;">

    <button type="submit" class="btn btn-primary mt-3">Créer le ticket</button>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const idVoyage = document.getElementById('idVoyage');
    const montant = document.getElementById('montant');
    const dateReservation = document.getElementById('dateReservation');
    const placesInfo = document.getElementById('placesRestantesInfo');
    const moyenPaiement = document.getElementById('moyenPaiement');
    const refPaiementDiv = document.getElementById('refPaiementDiv');
    const modeReception = document.getElementById('modeReception');
    const printTicketDiv = document.getElementById('printTicketDiv');
    const printBtn = document.getElementById('printBtn');

    idVoyage.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const prix = selected.getAttribute('data-prix');
        const date = selected.getAttribute('data-date');
        const heure = selected.getAttribute('data-heure');
        const places = selected.getAttribute('data-places');

        montant.value = prix || '';

        if (date && heure) {
            const dateTimeLocal = date + 'T' + heure.slice(0, 5);
            dateReservation.value = dateTimeLocal;
        }

        placesInfo.innerText = places ? `${places} place(s) restante(s)` : "Aucune information";
    });

    moyenPaiement.addEventListener('change', function () {
        refPaiementDiv.style.display = (this.value !== 'ESPECE') ? 'block' : 'none';
    });

    modeReception.addEventListener('change', function () {
        printTicketDiv.style.display = (this.value === 'papier') ? 'block' : 'none';
    });

    document.querySelector('form').addEventListener('submit', function () {
        setTimeout(() => {
            printBtn.disabled = false;
        }, 3000);
    });
});
</script>
@endsection
