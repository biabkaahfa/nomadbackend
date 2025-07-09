@extends('pages.auth.layout')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="fas fa-key"></i>
        </div>
        <h1>Mot de passe oublié</h1>
        <p>Réinitialisez votre mot de passe</p>
    </div>
    
    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('status') }}
            </div>
        @endif

        <div class="text-center mb-4">
            <p class="text-muted">
                <i class="fas fa-info-circle me-2"></i>
                Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>
        </div>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Adresse e-mail
                </label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       placeholder="votre@email.com">
                @error('email')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer le lien de réinitialisation
                </button>
            </div>
        </form>
    </div>
    
    <div class="auth-footer">
        <div class="auth-links">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left me-1"></i>Retour à la connexion
            </a>
        </div>
    </div>
</div>
@endsection