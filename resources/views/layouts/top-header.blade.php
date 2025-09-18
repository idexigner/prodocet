<!-- Header -->
<header class="main-header sticky-top">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Mobile menu toggle -->
                <button class="btn btn-link sidebar-toggle d-lg-none" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="sidebar-header">
                    <button class="btn btn-link sidebar-collapse-btn">
                        <i class="fas fa-angle-double-left"></i>
                    </button>
                </div>

                <!-- Desktop Navigation -->
                <div class="navbar-nav-desktop d-none d-lg-flex">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('groups') ? 'active' : '' }}" href="{{ route('groups') }}">
                                <i class="fas fa-users"></i>
                                <span>Grupos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Calendario</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('attendance') ? 'active' : '' }}" href="{{ route('attendance') }}">
                                <i class="fas fa-check-circle"></i>
                                <span>Asistencia</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Right side navigation -->
                <div class="navbar-nav-right">
                    <!-- Notifications -->
                    <div class="nav-item dropdown">
                        <a class="nav-link notification-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <h6 class="dropdown-header">Notificaciones</h6>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">5 sesiones restantes</div>
                                    <div class="notification-text">Grupo Inglés Básico A1</div>
                                    <div class="notification-time">Hace 2 horas</div>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-user-plus text-info"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">Nuevo estudiante registrado</div>
                                    <div class="notification-text">María González - Francés B1</div>
                                    <div class="notification-time">Hace 4 horas</div>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-calendar-times text-danger"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">Clase cancelada</div>
                                    <div class="notification-text">Alemán Intermedio - Mañana 10:00</div>
                                    <div class="notification-time">Ayer</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="#" onclick="markAllAsRead()">
                                Marcar todas como leídas
                            </a>
                            <a class="dropdown-item text-center" href="#" onclick="viewAllAlerts()">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Ver Todas las Alertas
                            </a>
                        </div>
                    </div>

                    <!-- Language Toggle -->
                    <div class="nav-item dropdown">
                        <a class="nav-link language-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe"></i>
                            <span class="language-text">{{ app()->getLocale() == 'es' ? 'ES' : 'EN' }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end language-dropdown">
                            <h6 class="dropdown-header">Idioma / Language</h6>
                            <a class="dropdown-item language-option {{ app()->getLocale() == 'es' ? 'active' : '' }}" 
                               href="#" onclick="changeLanguage('es')">
                                <i class="fas fa-flag me-2"></i>
                                Español
                            </a>
                            <a class="dropdown-item language-option {{ app()->getLocale() == 'en' ? 'active' : '' }}" 
                               href="#" onclick="changeLanguage('en')">
                                <i class="fas fa-flag me-2"></i>
                                English
                            </a>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="nav-item dropdown">
                        <a class="nav-link user-profile" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar" title="Administrador"></div>
                            <span class="user-name d-none d-md-inline">Administrador</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end user-dropdown">
                            <div class="user-info">
                                <div class="user-avatar-large" title="Administrador Principal"></div>
                                <div class="user-details">
                                    <div class="user-name">Administrador Principal</div>
                                    <div class="user-email">admin@prodocet.com</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="openProfileSettings()">
                                <i class="fas fa-user-cog"></i>
                                Configurar Perfil
                            </a>
                            <a class="dropdown-item" href="#" onclick="openSettings()">
                                <i class="fas fa-cog"></i>
                                Configuración
                            </a>
                            <a class="dropdown-item" href="#" onclick="openColorCustomization()">
                                <i class="fas fa-palette"></i>
                                Personalizar Colores
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i>
                                Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>