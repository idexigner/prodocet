 <!-- Sidebar -->
 <aside class="sidebar">
            <!-- Logo Section -->
            <div class="sidebar-logo-section">
                <!-- Mobile close button -->
                <button class="sidebar-close-btn" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="brand-logo" title="Centro Europeo De Idiomas"></div>
                <h4 class="brand-text mb-0 text-white">PRODOCET LMS</h4>
                <p class="text-white-50 mb-0" style="font-size: 0.875rem; color: white !important">Centro Europeo De Idiomas</p>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-text">{{ __('common.dashboard') }}</span>
                        </a>
                    </li>
                    
                    <!-- Group & Course Management -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('groups') ? 'active' : '' }}" href="{{ route('groups') }}">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">{{ __('common.groups') }}</span>
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
                        <a class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}">
                            <i class="fas fa-cog"></i>
                            <span class="nav-text">{{ __('common.settings') }}</span>
                        </a>
                    </li>
                    
                    <!-- Analytics -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}" href="{{ route('analytics') }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-text">{{ __('common.analytics') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>