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

                <!-- Role Indicator -->
                <div class="role-indicator">
                    @php
                        $user = Auth::user();
                        $userRoles = $user->roles->pluck('name')->toArray();
                        $primaryRole = $user->primaryRole->name ?? 'Usuario';
                    @endphp
                    <span class="role-badge role-{{ strtolower($primaryRole) }}">
                        <i class="fas fa-user-tag me-1"></i>
                        {{ ucfirst($primaryRole) }}
                    </span>
                    @if(count($userRoles) > 1)
                        <small class="text-muted ms-2">(+{{ count($userRoles) - 1 }} más)</small>
                    @endif
                </div>

                <!-- Debug Information (remove in production) -->
                @if(config('app.debug'))
                    <!-- <div class="debug-info">
                        <strong>Debug Info:</strong><br>
                        User ID: {{ $user->id }}<br>
                        Primary Role: {{ $primaryRole }}<br>
                        All Roles: {{ implode(', ', $userRoles) }}<br>
                        hasRole('student'): {{ $user->hasRole('student') ? 'Yes' : 'No' }}<br>
                        Role Check: {{ $primaryRole === 'student' ? 'Student' : 'Other' }}
                    </div> -->
                @endif

                <!-- Desktop Navigation -->
                <div class="navbar-nav-desktop d-none d-lg-flex">
                    <ul class="navbar-nav">
                        @if($primaryRole === 'student')
                            <!-- Student Navigation -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Mi Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.courses') || request()->routeIs('student.course-details') ? 'active' : '' }}" href="{{ route('student.courses') }}">
                                    <i class="fas fa-book"></i>
                                    <span>Mis Cursos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}" href="{{ route('student.schedule') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Mi Horario</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                                    <i class="fas fa-user"></i>
                                    <span>Mi Perfil</span>
                                </a>
                            </li>
                        @else
                            <!-- Admin/Teacher Navigation -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>{{ __('common.dashboard') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}" href="{{ route('groups.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span>{{ __('common.groups') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ __('common.calendar') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('attendance') ? 'active' : '' }}" href="{{ route('attendance') }}">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ __('common.attendance') }}</span>
                                </a>
                            </li>
                        @endif
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
                            <h6 class='dropdown-header'>{{ __('common.notifications') }}</h6>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">{{ __('common.sessions_remaining', ['count' => 5]) }}</div>
                                    <div class="notification-text">{{ __('common.group_basic_english') }}</div>
                                    <div class="notification-time">{{ __('common.hours_ago', ['count' => 2]) }}</div>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-user-plus text-info"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">{{ __('common.new_student_registered') }}</div>
                                    <div class="notification-text">{{ __('common.student_french_b1') }}</div>
                                    <div class="notification-time">{{ __('common.hours_ago', ['count' => 4]) }}</div>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-calendar-times text-danger"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">{{ __('common.class_cancelled') }}</div>
                                    <div class="notification-text">{{ __('common.german_intermediate_tomorrow') }}</div>
                                    <div class="notification-time">{{ __('common.yesterday') }}</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="#" onclick="markAllAsRead()">
                                {{ __('common.mark_all_read') }}
                            </a>
                            <a class="dropdown-item text-center" href="#" onclick="viewAllAlerts()">
                                <i class="fas fa-external-link-alt me-2"></i>
                                {{ __('common.view_all_alerts') }}
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
                            <h6 class='dropdown-header'>{{ __('common.language') }}</h6>
                            <a class="dropdown-item language-option {{ app()->getLocale() == 'es' ? 'active' : '' }}" 
                               href="#" onclick="changeLanguage('es')">
                                <i class="fas fa-flag me-2"></i>
                                {{ __('common.spanish') }}
                            </a>
                            <a class="dropdown-item language-option {{ app()->getLocale() == 'en' ? 'active' : '' }}" 
                               href="#" onclick="changeLanguage('en')">
                                <i class="fas fa-flag me-2"></i>
                                {{ __('common.english') }}
                            </a>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="nav-item dropdown">
                        <a class="nav-link user-profile" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar" title="{{ Auth::user()->name ?? 'Usuario' }}"></div>
                            <span class="user-name d-none d-md-inline">{{ Auth::user()->name ?? 'Usuario' }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end user-dropdown">
                            <div class="user-info">
                                <div class="user-avatar-large" title="{{ Auth::user()->name ?? 'Usuario' }}"></div>
                                <div class="user-details">
                                    <div class="user-name">{{ Auth::user()->name ?? 'Usuario' }}</div>
                                    <div class="user-email">{{ Auth::user()->email ?? 'usuario@ejemplo.com' }}</div>
                                    <div class="user-role">
                                        @if(Auth::user()->hasRole('student'))
                                            <span class="badge bg-primary">Estudiante</span>
                                        @elseif(Auth::user()->hasRole('teacher'))
                                            <span class="badge bg-success">Profesor</span>
                                        @elseif(Auth::user()->hasRole('admin'))
                                            <span class="badge bg-warning">Administrador</span>
                                        @else
                                            <span class="badge bg-secondary">{{ Auth::user()->primaryRole->name ?? 'Usuario' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            @if($primaryRole === 'student')
                                <a class="dropdown-item" href="{{ route('student.profile') }}">
                                    <i class="fas fa-user"></i>
                                    Mi Perfil
                                </a>
                                <a class="dropdown-item" href="{{ route('student.courses') }}">
                                    <i class="fas fa-book"></i>
                                    Mis Cursos
                                </a>
                                <a class="dropdown-item" href="{{ route('student.schedule') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    Mi Horario
                                </a>
                            @else
                                <a class="dropdown-item" href="#" onclick="openProfileSettings()">
                                    <i class="fas fa-user-cog"></i>
                                    {{ __('common.configure_profile') }}
                                </a>
                                <a class="dropdown-item" href="#" onclick="openSettings()">
                                    <i class="fas fa-cog"></i>
                                    {{ __('common.settings') }}
                                </a>
                                <a class="dropdown-item" href="#" onclick="openColorCustomization()">
                                    <i class="fas fa-palette"></i>
                                    {{ __('common.customize_colors') }}
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    {{ __('common.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <script>
        // Language switching functionality
        function changeLanguage(locale) {
            $.ajax({
                url: "{{ route('language.change', '') }}/" + locale,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the page to apply the new language
                        location.reload();
                    } else {
                        console.error('Language change failed:', response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Language change error:', xhr.responseText);
                }
            });
        }

        // Notification functions
        function markAllAsRead() {
            // Implementation for marking all notifications as read
            console.log('Mark all notifications as read');
        }

        function viewAllAlerts() {
            // Implementation for viewing all alerts
            console.log('View all alerts');
        }

        // Profile functions
        function openProfileSettings() {
            // Implementation for opening profile settings
            console.log('Open profile settings');
        }

        function openSettings() {
            // Implementation for opening settings
            console.log('Open settings');
        }

        function openColorCustomization() {
            // Implementation for opening color customization
            $('#colorCustomizationModal').modal('show');
        }
    </script>

    <style>
        .role-indicator {
            display: flex;
            align-items: center;
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .role-student {
            background: #007bff;
            color: white;
        }
        
        .role-teacher {
            background: #28a745;
            color: white;
        }
        
        .role-admin {
            background: #ffc107;
            color: #212529;
        }
        
        .role-usuario {
            background: #6c757d;
            color: white;
        }
        
        .user-role {
            margin-top: 0.5rem;
        }
        
        .user-role .badge {
            font-size: 0.75rem;
        }
        
        .user-details {
            text-align: left;
        }
        
        .user-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .user-email {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        
        /* Debug info - remove in production */
        .debug-info {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 9999;
        }
    </style>