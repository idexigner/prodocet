@extends('layouts.app')

@section('title', 'Cursos Disponibles')

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

    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    </style>

@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('student.courses.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>
                    <i class="fas fa-graduation-cap me-2"></i>
                    Cursos Disponibles
                </h1>
                <p class="text-muted mb-0">Explora y elige el curso que mejor se adapte a tus necesidades de aprendizaje.</p>
            </div>
        </div>

    <!-- Courses Grid -->
    <div class="row">
        @forelse($courses as $course)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 course-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>
                        {{ $course->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Course Info -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Nivel:</small>
                                <div class="fw-bold">{{ $course->level->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Idioma:</small>
                                <div class="fw-bold">{{ $course->language->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Description -->
                    <p class="text-muted small mb-3">
                        {{ Str::limit($course->description, 100) }}
                    </p>

                    <!-- Topics Count -->
                    <div class="mb-3">
                        <span class="badge bg-info">
                            <i class="fas fa-list me-1"></i>
                            {{ $course->topics->count() }} Temas
                        </span>
                        <span class="badge bg-success ms-2">
                            <i class="fas fa-clock me-1"></i>
                            {{ $course->total_hours ?? 0 }} Horas
                        </span>
                    </div>

                    <!-- Teachers -->
                    @if($course->groups->count() > 0)
                    <div class="mb-3">
                        <small class="text-muted">Profesores disponibles:</small>
                        <div class="mt-1">
                            @foreach($course->groups->take(2) as $group)
                                @if($group->teacher)
                                <span class="badge bg-secondary me-1">
                                    {{ $group->teacher->name }}
                                </span>
                                @endif
                            @endforeach
                            @if($course->groups->count() > 2)
                                <span class="badge bg-light text-dark">
                                    +{{ $course->groups->count() - 2 }} más
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Cost Information -->
                    @if($course->rateScheme && $course->total_cost > 0)
                    <div class="mb-3">
                        <small class="text-muted">Costo estimado:</small>
                        <div class="fw-bold text-success">
                            ${{ number_format($course->total_cost, 2) }}
                        </div>
                        <small class="text-muted">
                            (${{ number_format($course->rateScheme->hourly_rate, 2) }}/hora × {{ $course->total_hours }} horas)
                        </small>
                    </div>
                    @else
                    <div class="mb-3">
                        <small class="text-muted">Costo:</small>
                        <div class="fw-bold text-warning">
                            No disponible
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.enroll-form', $course->id) }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            Solicitar Inscripción
                        </a>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#courseModal{{ $course->id }}">
                            <i class="fas fa-eye me-1"></i>
                            Ver Detalles
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Details Modal -->
        <div class="modal fade" id="courseModal{{ $course->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-book me-2"></i>
                            {{ $course->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Course Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Información del Curso</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Nivel:</strong> {{ $course->level->name ?? 'N/A' }}</li>
                                    <li><strong>Idioma:</strong> {{ $course->language->name ?? 'N/A' }}</li>
                                    <li><strong>Duración:</strong> {{ $course->total_hours ?? 0 }} horas</li>
                                    <li><strong>Temas:</strong> {{ $course->topics->count() }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Costo</h6>
                                @if($course->rateScheme && $course->total_cost > 0)
                                <div class="alert alert-success">
                                    <strong>Total: ${{ number_format($course->total_cost, 2) }}</strong>
                                    <br>
                                    <small>{{ $course->total_hours }} horas × ${{ number_format($course->rateScheme->hourly_rate, 2) }}/hora</small>
                                </div>
                                @else
                                <div class="alert alert-warning">
                                    <small>Información de costo no disponible</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        @if($course->description)
                        <div class="mb-4">
                            <h6>Descripción</h6>
                            <p>{{ $course->description }}</p>
                        </div>
                        @endif

                        <!-- Topics -->
                        <div class="mb-4">
                            <h6>Temas del Curso</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Orden</th>
                                            <th>Tema</th>
                                            <th>Horas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($course->topics as $topic)
                                        <tr>
                                            <td>{{ $topic->order_index ?? $loop->iteration }}</td>
                                            <td>{{ $topic->title }}</td>
                                            <td>N/A</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Teachers -->
                        @if($course->groups->count() > 0)
                        <div class="mb-4">
                            <h6>Profesores Disponibles</h6>
                            <div class="row">
                                @foreach($course->groups as $group)
                                    @if($group->teacher)
                                    <div class="col-md-6 mb-2">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $group->teacher->name }}</div>
                                                        <small class="text-muted">{{ $group->teacher->email }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <a href="{{ route('student.enroll-form', $course->id) }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            Solicitar Inscripción
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay cursos disponibles</h4>
                <p class="text-muted">Actualmente no hay cursos disponibles para inscripción.</p>
            </div>
        </div>
        @endforelse
    </div>
    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="misc-wrapper">
                <h2 class="mb-2 mx-2">Acceso Denegado</h2>
                <p class="mb-4 mx-2">No tienes permisos para ver los cursos disponibles.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al Dashboard</a>
            </div>
        </div>
    @endif
</div>


@endsection