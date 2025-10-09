@extends('layouts.app')

@section('title', 'Mi Dashboard')

@push('css-link')
    @include('partials.common.datatable_style')
    <style>
    /* Student-specific styling */
    .content-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        margin-bottom: 2rem;
    }
    
    .content-title h1 {
        color: white;
        margin-bottom: 0.5rem;
    }
    
    .content-subtitle {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .content-actions .btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .content-actions .btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
</style>
<style>
.course-card {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1rem;
    background: #fff;
    transition: all 0.3s ease;
}

.course-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.course-header {
    margin-bottom: 0.75rem;
}

.course-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.course-level {
    font-size: 0.875rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.course-info {
    margin-bottom: 1rem;
}

.course-teacher, .course-group {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #495057;
}

.course-progress {
    margin-top: 0.75rem;
}

.progress {
    height: 6px;
    background-color: #e9ecef;
    border-radius: 3px;
    margin-bottom: 0.25rem;
}

.progress-bar {
    background-color: #007bff;
    border-radius: 3px;
}

.session-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.session-item:last-child {
    border-bottom: none;
}

.session-date {
    text-align: center;
    margin-right: 1rem;
    min-width: 50px;
}

.session-day {
    font-size: 1.25rem;
    font-weight: 600;
    color: #007bff;
    line-height: 1;
}

.session-month {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
}

.session-details {
    flex: 1;
}

.session-time {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.875rem;
}

.session-course {
    color: #495057;
    font-size: 0.875rem;
    margin: 0.125rem 0;
}

.session-teacher {
    color: #6c757d;
    font-size: 0.75rem;
}

.quick-action-card {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.quick-action-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.1);
    transform: translateY(-2px);
}

.quick-action-icon {
    font-size: 2rem;
    color: #007bff;
    margin-bottom: 0.75rem;
}

.quick-action-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.quick-action-desc {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush



@section('main-section')
<div class="content-header">
    <div class="content-title">
        <h1>Mi Dashboard</h1>
        <p class="content-subtitle">Bienvenido, {{ $student->first_name }} {{ $student->last_name }}</p>
    </div>
    <div class="content-actions">
        <button class="btn btn-primary" onclick="refreshDashboard()">
            <i class="fas fa-sync-alt"></i>
            Actualizar
        </button>
    </div>
</div>

<!-- Student Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $activeGroups }}</div>
                    <div class="stats-label">Grupos Activos</div>
                    <div class="stats-change positive">
                        <i class="fas fa-book-open"></i>
                        <span>Cursos en progreso</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $remainingHours }}</div>
                    <div class="stats-label">Horas Restantes</div>
                    <div class="stats-change info">
                        <i class="fas fa-hourglass-half"></i>
                        <span>De {{ $totalHoursPurchased }} horas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $totalHoursUsed }}</div>
                    <div class="stats-label">Horas Completadas</div>
                    <div class="stats-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ $totalHoursPurchased > 0 ? round(($totalHoursUsed / $totalHoursPurchased) * 100, 1) : 0 }}% completado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $upcomingSessions->count() }}</div>
                    <div class="stats-label">Próximas Clases</div>
                    <div class="stats-change info">
                        <i class="fas fa-calendar-check"></i>
                        <span>Esta semana</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Courses and Upcoming Sessions -->
<div class="row mb-4">
    <!-- My Active Courses -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-book me-2"></i>
                    Mis Cursos Activos
                </h5>
                <div class="card-tools">
                    <a href="{{ route('student.courses') }}" class="btn btn-sm btn-primary">
                        Ver todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($enrolledGroups->count() > 0)
                    <div class="row">
                        @foreach($enrolledGroups as $enrollment)
                            <div class="col-md-6 mb-3">
                                <div class="course-card">
                                    <div class="course-header">
                                        <h6 class="course-title">{{ $enrollment->group->course->name }}</h6>
                                        <span class="course-level">{{ $enrollment->group->course->language->name }} - {{ $enrollment->group->course->level->name }}</span>
                                    </div>
                                    <div class="course-info">
                                        <div class="course-teacher">
                                            <i class="fas fa-user-tie me-1"></i>
                                            <span>{{ $enrollment->group->teacher->first_name }} {{ $enrollment->group->teacher->last_name }}</span>
                                        </div>
                                        <div class="course-group">
                                            <i class="fas fa-users me-1"></i>
                                            <span>{{ $enrollment->group->name }}</span>
                                        </div>
                                        <div class="course-progress">
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ $enrollment->academic_hours_purchased > 0 ? ($enrollment->academic_hours_used / $enrollment->academic_hours_purchased) * 100 : 0 }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $enrollment->academic_hours_used }}/{{ $enrollment->academic_hours_purchased }} horas</small>
                                        </div>
                                    </div>
                                    <div class="course-actions">
                                        <a href="{{ route('student.course-details', $enrollment->group->id) }}" class="btn btn-sm btn-outline-primary">
                                            Ver detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tienes cursos activos</h5>
                        <p class="text-muted">Contacta con la administración para inscribirte en un curso.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Sessions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-calendar-check me-2"></i>
                    Próximas Clases
                </h5>
                <div class="card-tools">
                    <a href="{{ route('student.schedule') }}" class="btn btn-sm btn-primary">
                        Ver calendario
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($upcomingSessions->count() > 0)
                    @foreach($upcomingSessions as $session)
                        <div class="session-item">
                            <div class="session-date">
                                <div class="session-day">{{ \Carbon\Carbon::parse($session->date)->format('d') }}</div>
                                <div class="session-month">{{ \Carbon\Carbon::parse($session->date)->format('M') }}</div>
                            </div>
                            <div class="session-details">
                                <div class="session-time">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</div>
                                <div class="session-course">{{ $session->group->course->name }}</div>
                                <div class="session-teacher">{{ $session->group->teacher->first_name }} {{ $session->group->teacher->last_name }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay clases programadas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="quick-action-card" onclick="window.location.href='{{ route('student.courses') }}'">
                            <div class="quick-action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Mis Cursos</div>
                                <div class="quick-action-desc">Ver todos mis cursos</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="quick-action-card" onclick="window.location.href='{{ route('student.schedule') }}'">
                            <div class="quick-action-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Mi Horario</div>
                                <div class="quick-action-desc">Ver calendario de clases</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="quick-action-card" onclick="window.location.href='{{ route('student.profile') }}'">
                            <div class="quick-action-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Mi Perfil</div>
                                <div class="quick-action-desc">Actualizar información</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="quick-action-card" onclick="contactSupport()">
                            <div class="quick-action-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Soporte</div>
                                <div class="quick-action-desc">Contactar ayuda</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('js-link')
<script>
function refreshDashboard() {
    location.reload();
}

function contactSupport() {
    alert('Función de soporte en desarrollo. Por favor contacta a la administración.');
}
</script>
@endpush
