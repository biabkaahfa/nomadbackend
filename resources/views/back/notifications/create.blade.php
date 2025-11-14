@extends('back.app')
@section('title', 'Créer une notification')
@section('dashboard-header')
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="page-title mt-5">Nouvelle Notification</h3>
        <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
            ← Retour à la liste
        </a>
    </div>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('notifications.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="titre" class="form-label fw-bold">Titre de la notification</label>
                        <input type="text" name="titre" id="titre" placeholder="Ex: Retard du voyage..."
                            class="form-control form-control-lg" required>
                        @error('titre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="type" class="form-label fw-bold">Type de notification</label>
                        <select name="type" id="type" class="form-control form-control-lg" required>
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="retard">Retard</option>
                            <option value="annulation">Annulation</option>
                            <option value="report">Report</option>
                            <option value="accident">Accident</option>
                            <option value="rappel">Rappel</option>
                        </select>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="contenu" class="form-label fw-bold">Contenu du message</label>
                <textarea name="contenu" id="contenu" placeholder="Rédigez votre message ici..."
                    class="form-control" rows="5" required></textarea>
                <div class="form-text">
                    Ce message sera envoyé aux passagers. Incluez toutes les informations importantes.
                </div>
                @error('contenu')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="idVoyage" class="form-label fw-bold">Sélection du voyage</label>
                <select name="idVoyage" id="idVoyage" class="form-control form-control-lg" required>
                    <option value="">-- Choisir un voyage --</option>
                    @foreach($voyages as $voyage)
                        @php
                            $placesRestantes = $voyage->bus
                                ? $voyage->bus->placesDisponible
                                : max(0, optional($voyage->trajet->frequences->where('heureDepart', $voyage->heuresDepart)->first())->nombrePlaceMinimum - $voyage->tickets->count());
                        @endphp
                        <option value="{{ $voyage->id }}" data-tickets="{{ $voyage->tickets->count() }}">
                            {{ $voyage->trajet->pointDepart }} → {{ $voyage->trajet->pointArrive }}
                            | {{ \Carbon\Carbon::parse($voyage->dateDepart)->format('d/m/Y') }}
                            à {{ \Carbon\Carbon::parse($voyage->heuresDepart)->format('H:i') }}
                            | {{ $placesRestantes }} places restantes
                            | {{ $voyage->tickets->count() }} passager(s)
                        </option>
                    @endforeach
                </select>
                @error('idVoyage')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Section des modes d'envoi --}}
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">📨 Modes d'envoi</h5>
                </div>
                <div class="card-body">
                    {{-- Compteurs des destinataires --}}
                    <div class="alert alert-info mb-4" id="destinatairesInfo">
                        <h6 class="alert-heading">Destinataires potentiels :</h6>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white">
                                    <div class="h5 mb-1" id="countEmail">0</div>
                                    <small class="text-muted">📧 Emails</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white">
                                    <div class="h5 mb-1" id="countSms">0</div>
                                    <small class="text-muted">📱 SMS</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white">
                                    <div class="h5 mb-1" id="countPush">0</div>
                                    <small class="text-muted">🔔 Notifications Push</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <strong>Total passagers :</strong> <span id="totalPassagers">0</span>
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check card card-hover">
                                <div class="card-body">
                                    <input class="form-check-input mode-envoi" type="checkbox" name="modes_envoi[]" id="email_mode" value="email">
                                    <label class="form-check-label fw-bold" for="email_mode">
                                        📧 Email
                                    </label>
                                    <div class="form-text small">
                                        Envoi par email aux adresses renseignées
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check card card-hover">
                                <div class="card-body">
                                    <input class="form-check-input mode-envoi" type="checkbox" name="modes_envoi[]" id="sms_mode" value="sms">
                                    <label class="form-check-label fw-bold" for="sms_mode">
                                        📱 SMS
                                    </label>
                                    <div class="form-text small">
                                        Envoi par SMS aux numéros de téléphone
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check card card-hover">
                                <div class="card-body">
                                    <input class="form-check-input mode-envoi" type="checkbox" name="modes_envoi[]" id="push_mode" value="push">
                                    <label class="form-check-label fw-bold" for="push_mode">
                                        🔔 Notification Push
                                    </label>
                                    <div class="form-text small">
                                        Alertes sur l'application mobile
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('modes_envoi')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                    <i class="fas fa-paper-plane me-2"></i>
                    Envoyer la notification
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const voyageSelect = document.getElementById('idVoyage');
    const checkboxes = document.querySelectorAll('.mode-envoi');
    const submitBtn = document.getElementById('submitBtn');
    const totalPassagers = document.getElementById('totalPassagers');

    // Cache pour stocker les données des voyages
    const voyageData = {};

    // Récupérer les données des voyages
    @foreach($voyages as $voyage)
        voyageData[{{ $voyage->id }}] = {
            tickets_count: {{ $voyage->tickets->count() }},
            emails_count: {{ $voyage->tickets->filter(function($ticket) {
                return $ticket->email || $ticket->user?->email || $ticket->emailPersonneAPrevenir;
            })->count() }},
            sms_count: {{ $voyage->tickets->filter(function($ticket) {
                return $ticket->telephone || $ticket->user?->phone_number || $ticket->telephonePersonneAPrevenir;
            })->count() }},
            push_count: {{ $voyage->tickets->filter(function($ticket) {
                return $ticket->user && $ticket->user->fcm_tokens && count($ticket->user->fcm_tokens) > 0;
            })->count() }}
        };
    @endforeach

    function updateDestinatairesInfo() {
        const voyageId = voyageSelect.value;

        if (voyageId && voyageData[voyageId]) {
            const data = voyageData[voyageId];

            // Mettre à jour les compteurs
            document.getElementById('countEmail').textContent = data.emails_count;
            document.getElementById('countSms').textContent = data.sms_count;
            document.getElementById('countPush').textContent = data.push_count;
            totalPassagers.textContent = data.tickets_count;

            // Gérer l'état des checkboxes basé sur la disponibilité
            checkboxes.forEach(checkbox => {
                const mode = checkbox.value;
                const label = checkbox.closest('.card');

                if (mode === 'email' && data.emails_count === 0) {
                    checkbox.disabled = true;
                    label.classList.add('opacity-50');
                } else if (mode === 'sms' && data.sms_count === 0) {
                    checkbox.disabled = true;
                    label.classList.add('opacity-50');
                } else if (mode === 'push' && data.push_count === 0) {
                    checkbox.disabled = true;
                    label.classList.add('opacity-50');
                } else {
                    checkbox.disabled = false;
                    label.classList.remove('opacity-50');
                }
            });

            // Activer/désactiver le bouton selon la sélection
            const checkedModes = document.querySelectorAll('.mode-envoi:checked');
            submitBtn.disabled = checkedModes.length === 0;

            if (checkedModes.length === 0) {
                submitBtn.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Sélectionnez au moins un mode d\'envoi';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-warning');
            } else {
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer la notification';
                submitBtn.classList.remove('btn-warning');
                submitBtn.classList.add('btn-primary');
            }
        } else {
            // Réinitialiser les compteurs
            document.getElementById('countEmail').textContent = '0';
            document.getElementById('countSms').textContent = '0';
            document.getElementById('countPush').textContent = '0';
            totalPassagers.textContent = '0';

            // Désactiver tout
            checkboxes.forEach(checkbox => {
                checkbox.disabled = true;
                checkbox.checked = false;
                checkbox.closest('.card').classList.add('opacity-50');
            });

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Sélectionnez un voyage';
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-warning');
        }
    }

    // Événements
    voyageSelect.addEventListener('change', updateDestinatairesInfo);
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDestinatairesInfo);
    });

    // Initialisation
    updateDestinatairesInfo();
});
</script>

<style>
.card-hover {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.card-hover:hover {
    border-color: #007bff;
    transform: translateY(-2px);
}
.form-check-input:checked ~ .card-body {
    background-color: #e3f2fd;
    border-radius: 0.375rem;
}
.opacity-50 {
    opacity: 0.5;
}
</style>
@endsection
