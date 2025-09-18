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
                       id="password" name="password" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
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
        <p class="signup-link">
            ¿No tienes una cuenta? 
            <a href="#" onclick="showSignupForm()">Regístrate aquí</a>
        </p>
    </div>
</div>

<!-- Signup Form -->
<div class="auth-form d-none" id="signupForm">
    <h3 class="form-title">Crear Cuenta</h3>
    <p class="form-subtitle">Regístrate para acceder al sistema</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="firstName" class="form-label">Nombre</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                               id="firstName" name="first_name" placeholder="Nombre" 
                               value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="lastName" class="form-label">Apellido</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                               id="lastName" name="last_name" placeholder="Apellido" 
                               value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="signupEmail" class="form-label">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="signupEmail" name="email" placeholder="tu@email.com" 
                       value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="signupPassword" class="form-label">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="signupPassword" name="password" placeholder="••••••••" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" 
                       id="confirmPassword" name="password_confirmation" placeholder="••••••••" required>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="role" class="form-label">Rol</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                <select class="form-select @error('role') is-invalid @enderror" 
                        id="role" name="role" required>
                    <option value="">Seleccionar rol</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Profesor</option>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Estudiante</option>
                    <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>Recursos Humanos</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acceptTerms" name="terms" required>
                <label class="form-check-label" for="acceptTerms">
                    Acepto los <a href="#">términos y condiciones</a>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 signup-btn">
            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
        </button>
    </form>

    <div class="form-footer">
        <p class="login-link">
            ¿Ya tienes una cuenta? 
            <a href="#" onclick="showLoginForm()">Inicia sesión aquí</a>
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
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
@endpush
