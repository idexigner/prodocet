@extends('layouts.app')

@section('title', 'Mis Cursos')

@push('css-link')
    @include('partials.common.datatable_style')
    <style>
    .course-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid #e3e6f0;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .enrollment-request-card {
        border-left: 4px solid #ffc107;
    }

    .enrollment-request-card .card-header {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%) !important;
    }

    .availability-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .availability-item {
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .day-name {
        font-weight: 600;
        color: #495057;
    }

    .time {
        color: #6c757d;
        margin-left: 0.25rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 1rem;
    }

    .empty-state-description {
        color: #6c757d;
        margin-bottom: 2rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .empty-state-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    </style>
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('student.courses.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>
                    <i class="fas fa-book me-2"></i>
                    Mis Cursos
                </h1>
                <p class="text-muted mb-0">Gestiona tus cursos y ve el progreso de tu aprendizaje.</p>
            </div>
        </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($enrolledGroups->count() > 0 || $enrollmentRequests->count() > 0)
        <!-- Course Cards Grid -->
        <div class="row">
            @foreach($enrolledGroups as $enrollment)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 course-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-book me-2"></i>
                                {{ $enrollment->group->course->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Course Info -->
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Nivel:</small>
                                        <div class="fw-bold">{{ $enrollment->group->course->level->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Idioma:</small>
                                        <div class="fw-bold">{{ $enrollment->group->course->language->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Teacher Information -->
                            <div class="mb-3">
                                <small class="text-muted">Profesor:</small>
                                <div class="fw-bold">{{ $enrollment->group->teacher->first_name }} {{ $enrollment->group->teacher->last_name }}</div>
                                <small class="text-muted">{{ $enrollment->group->teacher->email }}</small>
                            </div>

                            <!-- Course Progress -->
                            <div class="mb-3">
                                <span class="badge bg-info">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $enrollment->academic_hours_used }}/{{ $enrollment->academic_hours_purchased }} Horas
                                </span>
                                <span class="badge bg-success ms-2">
                                    <i class="fas fa-check me-1"></i>
                                    Activo
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <small class="text-muted">Progreso del curso:</small>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $enrollment->academic_hours_purchased > 0 ? ($enrollment->academic_hours_used / $enrollment->academic_hours_purchased) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-grid gap-2">
                                <a href="{{ route('student.course-details', $enrollment->group->id) }}" class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i>
                                    Ver Detalles
                                </a>
                                <a href="{{ route('student.schedule') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-calendar me-1"></i>
                                    Ver Horario
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Enrollment Requests Section -->
            @if($enrollmentRequests->count() > 0)
                @foreach($enrollmentRequests as $request)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 course-card enrollment-request-card">
                            <div class="card-header text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $request->course->name }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Course Info -->
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Nivel:</small>
                                            <div class="fw-bold">{{ $request->course->level->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Idioma:</small>
                                            <div class="fw-bold">{{ $request->course->language->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Information -->
                                <div class="mb-3">
                                    <small class="text-muted">Estado:</small>
                                    <div>
                                        @if($request->status == 'pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                Pendiente de Aprobación
                                            </span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>
                                                Aprobada
                                            </span>
                                        @elseif($request->status == 'rejected')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>
                                                Rechazada
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Course Details -->
                                <div class="mb-3">
                                    <span class="badge bg-info">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $request->total_hours }} Horas
                                    </span>
                                    <span class="badge bg-success ms-2">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        ${{ number_format($request->total_cost, 2) }}
                                    </span>
                                </div>

                                <!-- Availability -->
                                @if($request->availability)
                                    <div class="mb-3">
                                        <small class="text-muted">Disponibilidad:</small>
                                        <div class="availability-list mt-1">
                                            @foreach($request->availability as $day => $schedule)
                                                <div class="availability-item">
                                                    <span class="day-name">{{ ucfirst($day) }}:</span>
                                                    <span class="time">{{ $schedule['start_time'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($request->status == 'rejected' && $request->rejection_reason)
                                    <div class="alert alert-danger">
                                        <small><strong>Motivo del rechazo:</strong> {{ $request->rejection_reason }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-grid gap-2">
                                    @if($request->status == 'pending')
                                        <span class="text-muted text-center">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Esperando aprobación del administrador
                                        </span>
                                    @elseif($request->status == 'approved')
                                        <span class="text-success text-center">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Solicitud aprobada
                                        </span>
                                    @elseif($request->status == 'rejected')
                                        <a href="{{ route('student.available-courses') }}" class="btn btn-primary">
                                            <i class="fas fa-redo me-2"></i>
                                            Solicitar Otro Curso
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @else
        <!-- No Courses State -->
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h3 class="empty-state-title">No tienes cursos inscritos</h3>
            <p class="empty-state-description">
                Actualmente no estás inscrito en ningún curso. Contacta con la administración para inscribirte en un curso.
            </p>
            <div class="empty-state-actions">
                <a href="{{ route('student.available-courses') }}" class="btn btn-primary">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Ver Cursos Disponibles
                </a>
            </div>
        </div>
    @endif

    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="misc-wrapper">
                <h2 class="mb-2 mx-2">Acceso Denegado</h2>
                <p class="mb-4 mx-2">No tienes permisos para ver los cursos.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al Dashboard</a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('js-link')
<script>
function refreshCourses() {
    location.reload();
}

function contactSupport() {
    alert('Función de soporte en desarrollo. Por favor contacta a la administración.');
}
</script>
@endpush