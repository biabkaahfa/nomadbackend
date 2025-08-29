@extends('pages.auth.layout')
@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="fas fa-user-circle"></i>
        </div>
        <h1>Connexion</h1>
        <p>Accédez à votre compte</p>
    </div>

    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('loga') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
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

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Mot de passe
                </label>
                <div class="position-relative">
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           required
                           placeholder="••••••••">
                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2"
                            onclick="togglePassword()" id="togglePasswordBtn">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="remember"
                           name="remember">
                    <label class="form-check-label" for="remember">
                        Se souvenir de moi
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </div>
        </form>
    </div>

    <div class="auth-footer">
        <div class="auth-links">
            <a href="{{ route('password.request') }}">
                <i class="fas fa-key me-1"></i>Mot de passe oublié ?
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword() {
    const password = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePasswordIcon');

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
</script>
@endsection
