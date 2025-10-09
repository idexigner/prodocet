<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PRODOCET LMS') - Iniciar Sesión</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Login Page Container -->
    <div class="auth-container">
        <div class="container-fluid" style="height: 100vh;">
            <div class="row h-100">
                <!-- Left Side - Branding -->
                <div class="col-lg-6 d-none d-lg-block auth-brand-side">
                    <div class="brand-content">
                        <div class="brand-logo">
                            <img src="{{ asset('assets/img/lg.jpg') }}" alt="Centro Europeo De Idiomas">
                            <h1>PRODOCET</h1>
                            <p class="brand-subtitle">Sistema de Gestión de Aprendizaje</p>
                        </div>
                        <div class="brand-features d-none">
                            <div class="feature-item">
                                <i class="fas fa-users"></i>
                                <span>Gestión de Estudiantes</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Administración de Profesores</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Programación de Clases</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-chart-line"></i>
                                <span>Reportes y Análisis</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Auth Form -->
                <div class="col-lg-6 auth-form-side">
                    <div class="auth-form-container">
                        <!-- Mobile Logo -->
                        <div class="mobile-logo d-lg-none">
                            <img src="{{ asset('assets/img/lg.jpg') }}" alt="Centro Europeo De Idiomas">
                            <h2>PRODOCET</h2>
                        </div>

                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/auth.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
