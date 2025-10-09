 <!-- Sidebar -->
 <aside class="sidebar">
            <!-- Logo Section -->
            <div class="sidebar-logo-section">
                <!-- Mobile close button -->
                <button class="sidebar-close-btn" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="brand-logo" title="Centro Europeo De Idiomas"></div>
                @php
                    $user = Auth::user();
                    $primaryRole = $user->primaryRole->name ?? 'Usuario';
                @endphp
                @if($primaryRole === 'student')
                    <h4 class="brand-text mb-0 text-white">Mi Portal</h4>
                    <p class="text-white-50 mb-0" style="font-size: 0.875rem; color: white !important">Estudiante - {{ Auth::user()->name }}</p>
                @else
                    <h4 class="brand-text mb-0 text-white">PRODOCET LMS</h4>
                    <p class="text-white-50 mb-0" style="font-size: 0.875rem; color: white !important">Centro Europeo De Idiomas</p>
                @endif
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    @if($primaryRole === 'student')
                        <!-- Student Navigation -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="nav-text">Mi Dashboard</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.courses') || request()->routeIs('student.course-details') ? 'active' : '' }}" href="{{ route('student.courses') }}">
                                <i class="fas fa-book"></i>
                                <span class="nav-text">Mis Cursos</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.available-courses') ? 'active' : '' }}" href="{{ route('student.available-courses') }}">
                                <i class="fas fa-graduation-cap"></i>
                                <span class="nav-text">Cursos Disponibles</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}" href="{{ route('student.schedule') }}">
                                <i class="fas fa-calendar-alt"></i>
                                <span class="nav-text">Mi Horario</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                                <i class="fas fa-user"></i>
                                <span class="nav-text">Mi Perfil</span>
                            </a>
                        </li>
                    @else
                        <!-- Admin/Teacher Navigation -->
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="nav-text">{{ __('common.dashboard') }}</span>
                            </a>
                        </li>
                        
                        <!-- Course Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                                <i class="fas fa-book"></i>
                                <span class="nav-text">{{ __('common.courses') }}</span>
                            </a>
                        </li>
                        
                        <!-- Group Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}" href="{{ route('groups.index') }}">
                                <i class="fas fa-users"></i>
                                <span class="nav-text">{{ __('common.groups') }}</span>
                            </a>
                        </li>
                        
                        <!-- Curriculum Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('curriculum.*') ? 'active' : '' }}" href="{{ route('curriculum.index') }}">
                                <i class="fas fa-graduation-cap"></i>
                                <span class="nav-text">{{ __('common.curriculum') }}</span>
                            </a>
                        </li>
                        
                        <!-- Calendar & Scheduling -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}">
                                <i class="fas fa-calendar-alt"></i>
                                <span class="nav-text">{{ __('common.calendar') }}</span>
                            </a>
                        </li>
                        
                        <!-- Attendance & Performance -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('attendance') ? 'active' : '' }}" href="{{ route('attendance') }}">
                                <i class="fas fa-check-circle"></i>
                                <span class="nav-text">{{ __('common.attendance') }}</span>
                            </a>
                        </li>
                        
                        <!-- Teacher Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span class="nav-text">{{ __('common.teachers') }}</span>
                            </a>
                        </li>
                        
                        <!-- Student Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                                <i class="fas fa-user-graduate"></i>
                                <span class="nav-text">{{ __('common.students') }}</span>
                            </a>
                        </li>
                        
                        <!-- Student Permissions Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student-permissions.*') ? 'active' : '' }}" href="{{ route('student-permissions.index') }}">
                                <i class="fas fa-user-shield"></i>
                                <span class="nav-text">Permisos de Estudiantes</span>
                            </a>
                        </li>
                        
                        <!-- Enrollment Requests Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('enrollment-requests.*') ? 'active' : '' }}" href="{{ route('enrollment-requests.index') }}">
                                <i class="fas fa-user-plus"></i>
                                <span class="nav-text">Solicitudes de Inscripción</span>
                            </a>
                        </li>
                        
                        <!-- HR Client Panel -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('hr-panel') ? 'active' : '' }}" href="{{ route('hr-panel') }}">
                                <i class="fas fa-briefcase"></i>
                                <span class="nav-text">{{ __('common.hr_panel') }}</span>
                            </a>
                        </li>
                        
                        <!-- User Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}" href="{{ route('users') }}">
                                <i class="fas fa-users-cog"></i>
                                <span class="nav-text">{{ __('common.users') }}</span>
                            </a>
                        </li>
                        
                        <!-- Role Management -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-shield"></i>
                                <span class="nav-text">{{ __('common.roles') }}</span>
                            </a>
                        </li>
                        
                        <!-- Reports & Billing -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports') ? 'active' : '' }}" href="{{ route('reports') }}">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span class="nav-text">{{ __('common.reports') }}</span>
                            </a>
                        </li>
                        
                        <!-- Class Data Upload -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('upload') ? 'active' : '' }}" href="{{ route('upload') }}">
                                <i class="fas fa-upload"></i>
                                <span class="nav-text">{{ __('common.upload') }}</span>
                            </a>
                        </li>
                        
                        <!-- Settings -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#settingsSubmenu" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                                <span class="nav-text">{{ __('common.settings') }}</span>
                                <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('settings.*') ? 'show' : '' }}" id="settingsSubmenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('settings.rate-schemes.*') ? 'active' : '' }}" href="{{ route('settings.rate-schemes.index') }}">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span class="nav-text">{{ __('settings.rate_schemes.page_title') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('settings.languages.*') ? 'active' : '' }}" href="{{ route('settings.languages.index') }}">
                                            <i class="fas fa-language"></i>
                                            <span class="nav-text">{{ __('settings.languages.page_title') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('settings.course-levels.*') ? 'active' : '' }}" href="{{ route('settings.course-levels.index') }}">
                                            <i class="fas fa-layer-group"></i>
                                            <span class="nav-text">{{ __('settings.course_levels.page_title') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!-- Analytics -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}" href="{{ route('analytics') }}">
                                <i class="fas fa-chart-line"></i>
                                <span class="nav-text">{{ __('common.analytics') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
        </aside>