@extends('back.app')
@section('title', 'Créer une notification')
@section('dashboard-header')<h3 class="page-title mt-5">Nouvelle Notification</h3>@endsection
@section('dashboard-content')
<form action="{{ route('notifications.store') }}" method="POST">
    @csrf
    <input type="text" name="titre" placeholder="Titre"
    class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;" required>

<textarea name="contenu" placeholder="Contenu"
    class="form-control mb-3" style="font-size: 1.2rem; padding: 1rem;" required></textarea>

<select name="type" class="form-control mb-3" style="font-size: 1.3rem; padding: 1rem; height:auto;" required>
    <option value="retard">Retard</option>
    <option value="annulation">Annulation</option>
    <option value="report">Report</option>
    <option value="accident">Accident</option>
    <option value="rappel">Rappel</option>
</select>


   <label for="idVoyage" class="mt-2">Voyage</label>
<select name="idVoyage" id="idVoyage" class="form-control mb-3"
    style="font-size: 1.2rem; padding: 1rem; height: auto;" required>
    <option value="">-- Choisir un voyage --</option>
    @foreach($voyages as $voyage)
        @php
            $placesRestantes = $voyage->bus
                ? $voyage->bus->placesDisponible
                : max(0, optional($voyage->trajet->frequences->where('heureDepart', $voyage->heuresDepart)->first())->nombrePlaceMinimum - $voyage->tickets->count());
        @endphp
        <option value="{{ $voyage->id }}">
            {{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}
            | {{ \Carbon\Carbon::parse($voyage->dateDepart)->format('d/m/Y') }}
            à {{ \Carbon\Carbon::parse($voyage->heuresDepart)->format('H:i') }}
            | {{ $placesRestantes }} places restantes
        </option>
    @endforeach
</select>


    <button class="btn btn-primary">Envoyer</button>
</form>

@endsection
