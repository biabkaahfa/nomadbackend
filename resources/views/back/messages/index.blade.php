@extends('back.app')

@section('title', 'Notifications')
@section('dashboard-header')
 <div class="row align-items-center">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">
                {{ auth()->user()->profil->name == 'Admin général' ? 'Toutes les Notifications' : 'Vos Notifications' }}
            </h3>
            @auth
                @if(auth()->user()->profil->name == 'Admin compagnie' && ($compagnie = auth()->user()->compagnie))
                    <span class="badge bg-warning">
                        {{ $compagnie->name }} -
                        {{ optional($compagnie->abonementActuel->typeAbonement)->nom ?? 'Sans abonement' }}
                    </span>
                @endif
            @endauth
        </div>
    </div>
 </div>
@endsection
@section('dashboard-content')

    <div class="card-body">
        @if($messages->isEmpty())
            <div class="alert alert-info">Aucune notification disponible</div>
        @else
            <div class="list-group">
                @foreach($messages as $message)
                <div class="list-group-item {{ $message->estVu ? '' : 'bg-light' }} message-item"
                    data-id="{{ $message->id }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                @switch($message->type)
                                    @case('rappelTicket')
                                        <span class="badge bg-warning me-2">
                                            <i class="fas fa-ticket-alt"></i> Tickets
                                        </span>
                                        @break
                                    @case('rappelFin')
                                        <span class="badge bg-danger me-2">
                                            <i class="fas fa-calendar-times"></i> Expiration
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-info me-2">
                                            <i class="fas fa-info-circle"></i> Notification
                                        </span>
                                @endswitch
                                <span>{{ $message->contenu }}</span>
                            </div>

                            @if(auth()->user()->profil->name == 'Admin général' && $message->abonement->compagnie)
                                <small class="text-muted d-block">
                                    <i class="fas fa-building"></i> {{ $message->abonement->compagnie->name }}
                                </small>
                            @endif

                            @if($message->type === 'rappelTicket' && $message->abonement && $message->abonement->typeAbonement)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-chart-pie"></i> Utilisation :
                                    {{ $message->abonement->ticketsCeMois()->count() }}/{{ $message->abonement->typeAbonement->maxTicket }}
                                    tickets ce mois
                                </small>
                            </div>
                        @endif

                        </div>

                        <div class="text-end ms-3">
                            <small class="text-muted d-block" title="{{ $message->dateEnvoi->format('d/m/Y H:i') }}">
                                <i class="far fa-clock"></i> {{ $message->dateEnvoi->diffForHumans() }}
                            </small>
                            @if($message->estVu)
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Lu
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
