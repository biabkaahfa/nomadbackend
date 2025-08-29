@extends('back.app')

@section('title', isset($typeAbonement) ? 'Modifier Type' : 'Créer Type')

@section('dashboard-content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{isset($typeAbonement)  ? 'Modifier' : 'Créer' }} un Type d'Abonnement
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($typeAbonement) ? route('type-abonements.update', $typeAbonement) : route('type-abonements.store') }}" method="POST">
                @csrf
                @if(isset($typeAbonement))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                           value="{{isset($typeAbonement) ? old('nom', $typeAbonement->nom) : old('nom')}}" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Taux (%)</label>
                    <input type="number" step="0.01" name="taux" class="form-control @error('taux') is-invalid @enderror"
                           value="{{ isset($typeAbonement) ?old('taux', $typeAbonement->taux):old('taux') }}" required>
                    @error('taux')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Prix</label>
                    <input type="number" step="0.01" name="prix" class="form-control @error('prix') is-invalid @enderror"
                           value="{{ isset($typeAbonement) ?old('prix', $typeAbonement->prix): old('prix') }}" required>
                    @error('prix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Nombre Max de Tickets</label>
                    <input type="number" name="maxTicket" class="form-control @error('maxTicket') is-invalid @enderror"
                           value="{{ isset($typeAbonement) ?old('maxTicket', $typeAbonement->maxTicket): old('maxTicket') }}" required>
                    @error('maxTicket')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($typeAbonement) ? 'Mettre à jour' : 'Créer' }}
                    </button>
                    <a href="{{ route('type-abonements.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
{{-- @extends('back.app')

@section('title', 'Test Affichage Type Abonnement')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Page de test pour Type d'Abonnement
            </h3>
        </div>
        <div class="card-body">
            <p>Si vous voyez ce message, la vue fonctionne !</p>

            @if(isset($typeAbonement))
                <p>La variable <strong>$typeAbonement</strong> est bien définie.</p>
                <p>Son ID est : <strong>{{ $typeAbonement->id ?? 'NULL (nouvel objet)' }}</strong></p>
                <p>Son nom est : <strong>{{ $typeAbonement->nom ?? 'N/A' }}</strong></p>
                <p>Son taux est : <strong>{{ $typeAbonement->taux ?? 'N/A' }}</strong></p>
            @else
                <p>La variable <strong>$typeAbonement</strong> n'est PAS définie.</p>
            @endif

            <a href="{{ route('type-abonements.index') }}" class="btn btn-secondary mt-3">Retour à la liste</a>
        </div>
    </div>
@endsection --}}
