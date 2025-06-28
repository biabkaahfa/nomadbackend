@extends('back.app')

@section('title', isset($compagnie) ? 'Modifier une compagnie' : 'Créer une compagnie')

@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title mt-5">
                {{ isset($compagnie) ? 'Modifier' : 'Ajouter' }} une Compagnie
            </h3>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ isset($compagnie) ? route('compagnies.update', $compagnie) : route('compagnies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($compagnie))
                    @method('PUT')
                @endif

                <!-- Nom -->
                <div class="form-group">
                    <label for="name">Nom de la compagnie</label>
                    <input type="text" class="form-control" name="name"
                        value="{{ isset($compagnie) ? old('name', $compagnie->name) : old('name') }}" required>
                    @error('name')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email de contact</label>
                    <input type="email" class="form-control" name="email"
                        value="{{ isset($compagnie) ? old('email', $compagnie->email) : old('email') }}" required>
                    @error('email')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" class="form-control" name="telephone"
                        value="{{ isset($compagnie) ? old('telephone', $compagnie->telephone) : old('telephone') }}">
                    @error('telephone')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ isset($compagnie) ? old('description', $compagnie->description) : old('description') }}</textarea>
                    @error('description')
                        <p class="text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Logo -->
                <div class="form-group">
                    <label for="logo">Logo (optionnel)</label>
                    <input type="file" class="form-control-file" name="logo">
                    @if (isset($compagnie) && $compagnie->logo)
                        <p class="mt-2">Logo actuel :</p>
                        <img src="{{ asset('storage/' . $compagnie->logo) }}" alt="Logo" style="max-height: 80px;">
                    @endif
                </div>

                <!-- Bouton d'enregistrement -->
                <button type="submit" class="btn btn-primary">
                    {{ isset($compagnie) ? 'Mettre à jour' : 'Créer la compagnie' }}
                </button>
            </form>
        </div>
    </div>
@endsection
