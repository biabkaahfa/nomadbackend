
@extends('back.app')

@section('title', isset($ticket) ? 'Modifier un ticket' : 'Créer un ticket - Achat sur place')

@section('dashboard-header')
<h3 class="page-title mt-5">
    {{ isset($ticket) ? 'Modifier le Ticket' : 'Création d’un Ticket (Achat sur place)' }}
</h3>
@endsection

@section('dashboard-content')
<form action="{{ isset($ticket) ? route('tickets.update', $ticket) : route('tickets.store') }}" method="POST">
    @csrf
    @if(isset($ticket))
        @method('PUT')
    @endif

    <label>Date de réservation</label>
    <input type="datetime-local" name="dateReservation" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" required
        value="{{ old('dateReservation', isset($ticket) ? \Carbon\Carbon::parse($ticket->dateReservation)->format('Y-m-d\TH:i') : '') }}">

    <label>Statut</label>
    <select name="statut" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" required>
        @foreach(['CONFIRME', 'ANNULE', 'REPORTE', 'UTILISE'] as $status)
            <option value="{{ $status }}" {{ old('statut', $ticket->statut ?? '') === $status ? 'selected' : '' }}>
                {{ ucfirst(strtolower($status)) }}
            </option>
        @endforeach
    </select>

    <input type="hidden" name="modeAchat" value="sur place">

    <label>Mode de réception</label>
    <select name="modeReception" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" required>
        @foreach(['email', 'papier', 'application'] as $mode)
            <option value="{{ $mode }}" {{ old('modeReception', $ticket->modeReception ?? '') === $mode ? 'selected' : '' }}>
                {{ ucfirst($mode) }}
            </option>
        @endforeach
    </select>

    <label>Nom complet</label>
    <input type="text" name="name" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" placeholder="Nom du client" 
        value="{{ old('name', $ticket->name ?? '') }}" {{ isset($ticket) ? '' : 'required' }}>

    <label>Téléphone</label>
    <input type="text" name="telephone" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" placeholder="Numéro de téléphone" 
        value="{{ old('telephone', $ticket->telephone ?? '') }}" {{ isset($ticket) ? '' : 'required' }}>

    <label>Email</label>
    <input type="email" name="email" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" placeholder="Adresse email" 
        value="{{ old('email', $ticket->email ?? '') }}" {{ isset($ticket) ? '' : 'required' }}>

    <label for="idVoyage">Voyage</label>
<select name="idVoyage" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" required>
    <option value="">-- Choisir un voyage --</option>
    @foreach($voyages as $voyage)
        <option value="{{ $voyage->id }}"
            {{ old('idVoyage', $ticket->idVoyage ?? '') == $voyage->id ? 'selected' : '' }}>
            {{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }} | {{ \Carbon\Carbon::parse($voyage->heuresDepart)->format('H:i') }}
        </option>
    @endforeach
</select>


    <label>Date du scan (si connu)</label>
    <input type="date" name="dateScan" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;"
     min="{{ date('Y-m-d') }}"
        value="{{ old('dateScan', isset($ticket->dateScan) ? \Carbon\Carbon::parse($ticket->dateScan)->format('Y-m-d') : '') }}">

    <label>ID Paiement</label>
    <input type="number" name="idPaiement" class="form-control form-control-lg fs-5 mb-3" style="font-size: 1.3rem; padding: 1rem; height: auto;" required
    min:1
        value="{{ old('idPaiement', $ticket->idPaiement ?? '') }}">

    <button class="btn btn-primary">
        {{ isset($ticket) ? 'Mettre à jour le ticket' : 'Créer le ticket' }}
    </button>
</form>
@endsection
