@extends('back.app')
@section('title', 'Notifications')
@section('dashboard-header')
<div class="d-flex justify-content-between align-items-center mt-5">
    <h4>Historique des notifications envoyées</h4>
    <a href="{{ route('notifications.create') }}" class="btn btn-primary viewbutton">
        <i class="fas fa-paper-plane me-2"></i>Envoyer une notification
    </a>
</div>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-body">
        {{-- Statistiques résumées --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $notifications->count() }}</h3>
                        <p class="mb-0">Notifications envoyées</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $notifications->sum('email_count') + $notifications->sum('sms_count') + $notifications->sum('push_count') }}</h3>
                        <p class="mb-0">Messages distribués</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $notifications->where('email_sent', true)->count() }}</h3>
                        <p class="mb-0">Avec emails</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ $notifications->where('sms_sent', true)->count() }}</h3>
                        <p class="mb-0">Avec SMS</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Voyage</th>
                        <th>Modes d'envoi</th>
                        <th>Statistiques</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($notification->DateEnvoie)->format('d/m/Y') }}</strong><br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($notification->DateEnvoie)->format('H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ Str::limit($notification->titre, 40) }}</strong><br>
                                <small class="text-muted">{{ Str::limit($notification->contenu, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge
                                    @switch($notification->type)
                                        @case('accident') bg-danger @break
                                        @case('annulation') bg-danger @break
                                        @case('retard') bg-warning @break
                                        @case('report') bg-info @break
                                        @default bg-secondary
                                    @endswitch">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </td>
                            <td>
                                @if($notification->voyage)
                                    {{ $notification->voyage->trajet->pointDepart }} →
                                    {{ $notification->voyage->trajet->pointArrive }}<br>
                                    <small class="text-muted">
                                        {{-- CORRECTION ICI : Utiliser Carbon::parse() --}}
                                        {{ \Carbon\Carbon::parse($notification->voyage->dateDepart)->format('d/m/Y') }}
                                    </small>
                                @else
                                    <span class="text-muted">Voyage supprimé</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($notification->email_sent)
                                        <span class="badge bg-primary" title="Email">
                                            📧 {{ $notification->email_count }}
                                        </span>
                                    @endif
                                    @if($notification->sms_sent)
                                        <span class="badge bg-success" title="SMS">
                                            📱 {{ $notification->sms_count }}
                                        </span>
                                    @endif
                                    @if($notification->push_sent)
                                        <span class="badge bg-info" title="Push">
                                            🔔 {{ $notification->push_count }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <small>
                                    <strong>Total:</strong> {{ $notification->email_count + $notification->sms_count + $notification->push_count }}<br>
                                    <strong>Passagers:</strong> {{ $notification->voyage->tickets->count() ?? 0 }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#notificationModal{{ $notification->id }}"
                                        title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Modal de détails --}}
                                    <div class="modal fade" id="notificationModal{{ $notification->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Détails de la notification</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>{{ $notification->titre }}</h6>
                                                    <p class="text-muted">{{ $notification->contenu }}</p>

                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <strong>Voyage:</strong><br>
                                                            {{ $notification->voyage->trajet->pointDepart }} →
                                                            {{ $notification->voyage->trajet->pointArrive }}<br>
                                                            {{-- CORRECTION ICI AUSSI --}}
                                                            {{ \Carbon\Carbon::parse($notification->voyage->dateDepart)->format('d/m/Y') }} à
                                                            {{ $notification->voyage->heuresDepart }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Statistiques d'envoi:</strong><br>
                                                            @if($notification->email_sent)
                                                                📧 Emails: {{ $notification->email_count }}<br>
                                                            @endif
                                                            @if($notification->sms_sent)
                                                                📱 SMS: {{ $notification->sms_count }}<br>
                                                            @endif
                                                            @if($notification->push_sent)
                                                                🔔 Push: {{ $notification->push_count }}<br>
                                                            @endif
                                                            <strong>Total: {{ $notification->email_count + $notification->sms_count + $notification->push_count }} messages</strong>
                                                        </div>
                                                    </div>

                                                    @if($notification->voyage->tickets->count() > 0)
                                                        <div class="mt-3">
                                                            <strong>Passagers concernés ({{ $notification->voyage->tickets->count() }}):</strong>
                                                            <div class="mt-2" style="max-height: 200px; overflow-y: auto;">
                                                                @foreach($notification->voyage->tickets as $ticket)
                                                                    <div class="border-bottom py-1">
                                                                        <small>
                                                                            👤 {{ $ticket->user->name ?? $ticket->name ?? 'Passager' }}
                                                                            @if($ticket->email)
                                                                                • 📧 {{ $ticket->email }}
                                                                            @endif
                                                                            @if($ticket->telephone)
                                                                                • 📱 {{ $ticket->telephone }}
                                                                            @endif
                                                                        </small>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('notifications.destroy', $notification->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-bell fa-2x mb-3 d-block"></i>
                                Aucune notification envoyée pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Script pour gérer les tooltips Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
