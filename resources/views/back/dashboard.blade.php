@extends('back.app')

@section('title', 'Tableau de bord')

@section('dashboard-header')
<!-- Header avec background gradient -->
<div class="dashboard-header">
    <div class="container-fluid py-6">
        <div class="row align-items-center">
            <div class="col">
                <div class="welcome-section">
                    <h1 class="display-6 fw-bold text-green mb-2">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        Tableau de bord
                    </h1>
                    <p class="text-green-50 mb-0 fs-5">
                        Bienvenue, <span class="fw-semibold">{{ auth()->user()->name }}</span> !
                        Voici votre activité aujourd'hui.
                    </p>
                </div>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <div class="time-display text-green text-end">
                        <div class="fs-2 fw-bold" id="liveTime">{{ now()->format('H:i') }}</div>
                        <div class="fs-6 opacity-75">{{ now()->translatedFormat('l d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
@php $profil = strtolower($profil); @endphp

<!-- Messages d'alerte -->
@if(session('success'))
    <div class="container-fluid mt-4">
        <div class="alert alert-success alert-elegant fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fa-lg"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1">Succès !</h6>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
@endif

{{-- Admin Général --}}
@if($profil === 'admin général')
    @include('back.admin-general')

{{-- Admin Compagnie --}}
@elseif($profil === 'admin compagnie')
    @include('back.admin-compagnie')

{{-- Chef de Gare --}}
@elseif($profil === 'chef de gare')
    @include('back.chef-gare')

{{-- Réceptionniste --}}
@elseif($profil === 'réceptionniste')
    @include('back.receptionniste')
@endif

@endsection

@push('scripts')
<script>
    // Mise à jour de l'heure en temps réel
    function updateTime() {
        const now = new Date();
        const timeElement = document.getElementById('liveTime');
        const dateElement = document.querySelector('.current-time .fs-6');

        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    setInterval(updateTime, 1000);

    // Animation des cartes de statistiques
    document.addEventListener('DOMContentLoaded', function() {
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-in');
        });
    });
</script>
@endpush
