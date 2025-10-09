@extends('layouts.auth')

@section('title', 'PRODOCET LMS - Iniciar Sesión')

@section('content')
<!-- Login Form -->
<div class="auth-form" id="loginForm">
    <h3 class="form-title">Iniciar Sesión</h3>
    <p class="form-subtitle">Accede a tu panel de administración</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" placeholder="tu@email.com" 
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password_origin" name="password" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="background-color: #fff;">
                    <i class="fas fa-eye-slash"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                <label class="form-check-label" for="rememberMe">
                    Recordarme
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 login-btn">
            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
        </button>
    </form>

    <div class="form-footer">
        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
        <p class="signup-link d-none">
            ¿No tienes una cuenta? 
            <a href="#" onclick="showSignupForm()">Regístrate aquí</a>
        </p>
    </div>
</div>


@endsection

@push('scripts')
<script>
// Form switching functionality
function showSignupForm() {
    document.getElementById('loginForm').classList.add('d-none');
    document.getElementById('signupForm').classList.remove('d-none');
}

function showLoginForm() {
    console.log('showLoginForm');
    document.getElementById('signupForm').classList.add('d-none');
    document.getElementById('loginForm').classList.remove('d-none');
}

// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded');
    const togglePasswordBtn = document.getElementById('togglePassword');
    
    if (togglePasswordBtn) {
        
        togglePasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('togglePasswordBtn clicked');
            
            let passwordField = document.getElementById('password_origin');
            let icon = this.querySelector('i');
            console.log('passwordField', passwordField);
            console.log('icon', icon);

            if (passwordField && icon) {
                console.log("passwordField and icon found",passwordField.getAttribute('type'), icon.className);
                // Toggle password visibility
                if (passwordField.getAttribute('type') == 'password') {
                    passwordField.setAttribute('type', 'text');
                    icon.className = 'fas fa-eye';
                    console.log("passwordField and icon found");
                } else {
                    passwordField.setAttribute('type', 'password');
                    icon.className = 'fas fa-eye-slash';
                    console.log("passwordField and icon found");
                }
            }
        });
    }
});
</script>
@endpush
