@extends('layouts.app')

@section('title', 'Detalles del Curso - ' . $course->name)

@push('css-link')
    @include('partials.common.datatable_style')
    <style>
.info-item {
    margin-bottom: 1rem;
}

.info-label {
    display: block;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-value {
    color: #2c3e50;
    font-size: 1rem;
}

.info-description {
    color: #495057;
    line-height: 1.6;
    margin-top: 0.5rem;
}

.info-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.topics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.topic-card {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1rem;
    background: #fff;
    transition: all 0.3s ease;
}

.topic-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.topic-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.topic-number {
    width: 30px;
    height: 30px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    margin-right: 0.75rem;
}

.topic-title {
    font-weight: 600;
    color: #2c3e50;
    flex: 1;
}

.topic-description {
    color: #6c757d;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 0.75rem;
}

.topic-meta {
    display: flex;
    gap: 0.75rem;
    font-size: 0.75rem;
}

.topic-hours, .topic-required {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    color: #6c757d;
}

.topic-required {
    background: #fff3cd;
    color: #856404;
}

.sessions-list {
    max-height: 500px;
    overflow-y: auto;
}

.session-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.session-item:last-child {
    border-bottom: none;
}

.session-date {
    text-align: center;
    margin-right: 1rem;
    min-width: 60px;
}

.session-day {
    font-size: 1.5rem;
    font-weight: 600;
    color: #007bff;
    line-height: 1;
}

.session-month {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
}

.session-year {
    font-size: 0.75rem;
    color: #6c757d;
}

.session-details {
    flex: 1;
}

.session-time {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.session-topic, .session-notes {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.session-notes {
    font-style: italic;
}

.teacher-profile {
    text-align: center;
}

.teacher-avatar {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: #6c757d;
}

.teacher-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.teacher-email, .teacher-phone {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.group-info {
    space-y: 0.75rem;
}

.group-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
}

.group-label {
    color: #6c757d;
    font-weight: 500;
}

.group-value {
    color: #2c3e50;
    font-weight: 600;
}

.group-link {
    color: #007bff;
    text-decoration: none;
}

.group-link:hover {
    text-decoration: underline;
}

.group-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.progress-stats {
    margin-bottom: 1.5rem;
}

.progress-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
}

.progress-label {
    color: #6c757d;
}

.progress-value {
    font-weight: 600;
    color: #2c3e50;
}

.progress-bar-container {
    margin-bottom: 1.5rem;
}

.progress {
    height: 10px;
    background-color: #e9ecef;
    border-radius: 5px;
    margin-bottom: 0.5rem;
}

.progress-bar {
    background: linear-gradient(90deg, #28a745, #20c997);
    border-radius: 5px;
    transition: width 0.3s ease;
}

.progress-percentage {
    text-align: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: #28a745;
}

.enrollment-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.enrollment-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.enrollment-item:last-child {
    margin-bottom: 0;
}

.enrollment-label {
    color: #6c757d;
}

.enrollment-value {
    font-weight: 500;
    color: #2c3e50;
}

.enrollment-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-enrolled {
    background: #d4edda;
    color: #155724;
}

.status-completed {
    background: #cce5ff;
    color: #004085;
}

.status-dropped {
    background: #f8d7da;
    color: #721c24;
}

.enrollment-grade {
    font-weight: 600;
    color: #28a745;
    font-size: 1.1rem;
}

.quick-actions .btn {
    width: 100%;
}

.btn-block {
    display: block;
    width: 100%;
}
</style>
@endpush

@section('main-section')
<div class="content-header">
    <div class="content-title">
        <h1>{{ $course->name }}</h1>
        <p class="content-subtitle">{{ $course->language->name }} - {{ $course->level->name }} | Grupo: {{ $group->name }}</p>
    </div>
    <div class="content-actions">
        <a href="{{ route('student.courses') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Volver a Cursos
        </a>
        <button class="btn btn-primary" onclick="refreshCourseDetails()">
            <i class="fas fa-sync-alt"></i>
            Actualizar
        </button>
    </div>
</div>

<div class="row">
    <!-- Course Information -->
    <div class="col-lg-8">
        <!-- Course Overview -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Información del Curso
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">Nombre del Curso:</label>
                            <span class="info-value">{{ $course->name }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Código:</label>
                            <span class="info-value">{{ $course->code }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Idioma:</label>
                            <span class="info-value">{{ $course->language->name }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Nivel:</label>
                            <span class="info-value">{{ $course->level->name }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">Horas de Enseñanza:</label>
                            <span class="info-value">{{ $course->teaching_hours }} HC</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Total de Horas:</label>
                            <span class="info-value">{{ $course->total_hours }} horas</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Modalidad:</label>
                            <span class="info-value">{{ ucfirst($course->mode) }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Estado:</label>
                            <span class="info-status status-{{ $course->status }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @if($course->description)
                    <div class="info-item mt-3">
                        <label class="info-label">Descripción:</label>
                        <p class="info-description">{{ $course->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Course Topics -->
        @if($topics->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-list-ul me-2"></i>
                        Temas del Curso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="topics-grid">
                        @foreach($topics as $index => $curriculum)
                            <div class="topic-card">
                                <div class="topic-header">
                                    <div class="topic-number">{{ $index + 1 }}</div>
                                    <div class="topic-title">{{ $curriculum->curriculumTopic->name }}</div>
                                </div>
                                <div class="topic-content">
                                    @if($curriculum->curriculumTopic->description)
                                        <p class="topic-description">{{ $curriculum->curriculumTopic->description }}</p>
                                    @endif
                                    <div class="topic-meta">
                                        @if($curriculum->estimated_hours)
                                            <span class="topic-hours">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $curriculum->estimated_hours }} horas
                                            </span>
                                        @endif
                                        @if($curriculum->is_required)
                                            <span class="topic-required">
                                                <i class="fas fa-star me-1"></i>
                                                Requerido
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Course Sessions -->
        @if($sessions->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Sesiones del Curso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="sessions-list">
                        @foreach($sessions as $session)
                            <div class="session-item">
                                <div class="session-date">
                                    <div class="session-day">{{ \Carbon\Carbon::parse($session->date)->format('d') }}</div>
                                    <div class="session-month">{{ \Carbon\Carbon::parse($session->date)->format('M') }}</div>
                                    <div class="session-year">{{ \Carbon\Carbon::parse($session->date)->format('Y') }}</div>
                                </div>
                                <div class="session-details">
                                    <div class="session-time">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </div>
                                    @if($session->topic)
                                        <div class="session-topic">
                                            <i class="fas fa-book me-1"></i>
                                            {{ $session->topic }}
                                        </div>
                                    @endif
                                    @if($session->notes)
                                        <div class="session-notes">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            {{ $session->notes }}
                                        </div>
                                    @endif
                                </div>
                                <div class="session-status">
                                    @if($session->status === 'completed')
                                        <span class="badge bg-success">Completada</span>
                                    @elseif($session->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelada</span>
                                    @elseif(\Carbon\Carbon::parse($session->date)->isPast())
                                        <span class="badge bg-warning">Pasada</span>
                                    @else
                                        <span class="badge bg-primary">Programada</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Teacher Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-user-tie me-2"></i>
                    Profesor
                </h5>
            </div>
            <div class="card-body">
                <div class="teacher-profile">
                    <div class="teacher-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="teacher-info">
                        <h6 class="teacher-name">{{ $teacher->first_name }} {{ $teacher->last_name }}</h6>
                        <p class="teacher-email">{{ $teacher->email }}</p>
                        @if($teacher->phone)
                            <p class="teacher-phone">
                                <i class="fas fa-phone me-1"></i>
                                {{ $teacher->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-users me-2"></i>
                    Información del Grupo
                </h5>
            </div>
            <div class="card-body">
                <div class="group-info">
                    <div class="group-item">
                        <label class="group-label">Nombre del Grupo:</label>
                        <span class="group-value">{{ $group->name }}</span>
                    </div>
                    <div class="group-item">
                        <label class="group-label">Código:</label>
                        <span class="group-value">{{ $group->code }}</span>
                    </div>
                    @if($group->classroom)
                        <div class="group-item">
                            <label class="group-label">Aula:</label>
                            <span class="group-value">{{ $group->classroom }}</span>
                        </div>
                    @endif
                    @if($group->virtual_link)
                        <div class="group-item">
                            <label class="group-label">Enlace Virtual:</label>
                            <a href="{{ $group->virtual_link }}" target="_blank" class="group-link">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Unirse a la clase
                            </a>
                        </div>
                    @endif
                    <div class="group-item">
                        <label class="group-label">Fecha de Inicio:</label>
                        <span class="group-value">{{ \Carbon\Carbon::parse($group->start_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="group-item">
                        <label class="group-label">Fecha de Fin:</label>
                        <span class="group-value">{{ \Carbon\Carbon::parse($group->end_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="group-item">
                        <label class="group-label">Estado:</label>
                        <span class="group-status status-{{ $group->status }}">
                            {{ ucfirst($group->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Progress -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Mi Progreso
                </h5>
            </div>
            <div class="card-body">
                <div class="progress-stats">
                    <div class="progress-item">
                        <div class="progress-label">Horas Completadas</div>
                        <div class="progress-value">{{ $enrollment->academic_hours_used }}</div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">Horas Totales</div>
                        <div class="progress-value">{{ $enrollment->academic_hours_purchased }}</div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label">Horas Restantes</div>
                        <div class="progress-value">{{ $enrollment->academic_hours_purchased - $enrollment->academic_hours_used }}</div>
                    </div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress">
                        <div class="progress-bar" 
                             style="width: {{ $enrollment->academic_hours_purchased > 0 ? ($enrollment->academic_hours_used / $enrollment->academic_hours_purchased) * 100 : 0 }}%">
                        </div>
                    </div>
                    <div class="progress-percentage">
                        {{ $enrollment->academic_hours_purchased > 0 ? round(($enrollment->academic_hours_used / $enrollment->academic_hours_purchased) * 100, 1) : 0 }}% Completado
                    </div>
                </div>
                <div class="enrollment-info">
                    <div class="enrollment-item">
                        <span class="enrollment-label">Fecha de Inscripción:</span>
                        <span class="enrollment-value">{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="enrollment-item">
                        <span class="enrollment-label">Estado:</span>
                        <span class="enrollment-status status-{{ $enrollment->status }}">
                            @switch($enrollment->status)
                                @case('enrolled')
                                    Inscrito
                                    @break
                                @case('completed')
                                    Completado
                                    @break
                                @case('dropped')
                                    Abandonado
                                    @break
                                @default
                                    {{ ucfirst($enrollment->status) }}
                            @endswitch
                        </span>
                    </div>
                    @if($enrollment->final_grade)
                        <div class="enrollment-item">
                            <span class="enrollment-label">Calificación Final:</span>
                            <span class="enrollment-grade">{{ $enrollment->final_grade }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="{{ route('student.schedule') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Ver Horario
                    </a>
                    @if($group->virtual_link)
                        <a href="{{ $group->virtual_link }}" target="_blank" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-video me-1"></i>
                            Unirse a Clase Virtual
                        </a>
                    @endif
                    <button class="btn btn-outline-secondary btn-block" onclick="contactTeacher()">
                        <i class="fas fa-envelope me-1"></i>
                        Contactar Profesor
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('js-link')
<script>
function refreshCourseDetails() {
    location.reload();
}

function contactTeacher() {
    alert('Función de contacto con el profesor en desarrollo. Por favor usa el email del profesor: {{ $teacher->email }}');
}
</script>
@endpush
