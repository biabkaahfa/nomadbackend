@extends('pages.auth.layout')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1>Nouveau mot de passe</h1>
        <p>Créez un nouveau mot de passe sécurisé</p>
    </div>
    
    <div class="auth-body">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Adresse e-mail
                </label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $request->email) }}" 
                       required 
                       autofocus
                       placeholder="votre@email.com">
                @error('email')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Nouveau mot de passe
                </label>
                <div class="position-relative">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="••••••••">
                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2" 
                            onclick="togglePassword('password')" id="togglePasswordBtn">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Le mot de passe doit contenir au moins 8 caractères.
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-lock me-2"></i>Confirmer le mot de passe
                </label>
                <div class="position-relative">
                    <input type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           placeholder="••••••••">
                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2" 
                            onclick="togglePassword('password_confirmation')" id="togglePasswordConfirmBtn">
                        <i class="fas fa-eye" id="togglePasswordConfirmIcon"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Réinitialiser le mot de passe
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

@section('scripts')
<script>
function togglePassword(fieldId) {
    const password = document.getElementById(fieldId);
    const toggleBtn = document.getElementById('toggle' + fieldId.charAt(0).toUpperCase() + fieldId.slice(1) + 'Icon');
    
    if (password.type === 'password') {
        password.type = 'text';
        toggleBtn.classList.remove('fa-eye');
        toggleBtn.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        toggleBtn.classList.remove('fa-eye-slash');
        toggleBtn.classList.add('fa-eye');
    }
}

// Validation en temps réel
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>
@endsection