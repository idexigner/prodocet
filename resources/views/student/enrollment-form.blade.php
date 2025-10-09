@extends('layouts.app')

@section('title', 'Solicitar Inscripción')

@push('css-link')
    @include('partials.common.datatable_style')
    <style>
.day-schedule {
    transition: all 0.3s ease;
}

.card {
    border: 1px solid #e3e6f0;
    transition: box-shadow 0.2s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.form-check-input:checked + .form-check-label {
    color: #0d6efd;
}
</style>
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('student.courses.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>
                    <i class="fas fa-user-plus me-2"></i>
                    Solicitar Inscripción
                </h1>
                <p class="text-muted mb-0">Completa la información para solicitar tu inscripción al curso.</p>
            </div>
        </div>

    <div class="row">
        <!-- Course Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>
                        Información del Curso
                    </h5>
                </div>
                <div class="card-body">
                    <h6>{{ $course->name }}</h6>
                    <div class="mb-3">
                        <small class="text-muted">Nivel:</small>
                        <div class="fw-bold">{{ $course->level->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Idioma:</small>
                        <div class="fw-bold">{{ $course->language->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Duración total:</small>
                        <div class="fw-bold">{{ $totalHours }} horas</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Número de temas:</small>
                        <div class="fw-bold">{{ $course->topics->count() }}</div>
                    </div>
                    <div class="alert alert-success">
                        <h6 class="alert-heading">Costo Total</h6>
                        <h4 class="mb-0">${{ number_format($totalCost, 2) }}</h4>
                        <small>
                            @if($course->rateScheme)
                                ${{ number_format($course->rateScheme->hourly_rate, 2) }}/hora × {{ $totalHours }} horas
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <!-- Topics Preview -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Temas del Curso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
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
            </div>
        </div>

        <!-- Enrollment Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Configurar Disponibilidad
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('student.request-enrollment', $course->id) }}" method="POST" id="enrollmentForm">
                        @csrf
                        
                        <!-- Debug info -->
                        <div class="alert alert-info">
                            <strong>Debug Info:</strong><br>
                            Course ID: {{ $course->id }}<br>
                            Form Action: {{ route('student.request-enrollment', $course->id) }}<br>
                            CSRF Token: {{ csrf_token() }}
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Configura tu disponibilidad para que el administrador pueda asignarte un horario adecuado. 
                            Una vez aprobada tu solicitud, se te asignará una sesión por día.
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Horarios:</strong> Solo puedes seleccionar horarios en intervalos de 30 minutos (ej: 7:00, 7:30, 8:00, 8:30, etc.). 
                            Solo necesitas seleccionar la hora de inicio para cada día.
                        </div>

                        <div class="mb-4">
                            <h6>Disponibilidad Semanal</h6>
                            <p class="text-muted">Selecciona los días y horarios en los que estás disponible para clases.</p>
                            
                            <div class="row">
                                @php
                                    $days = ['monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 
                                            'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado'];
                                @endphp
                                
                                @foreach($days as $dayKey => $dayName)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input day-checkbox" type="checkbox" 
                                                       id="day_{{ $dayKey }}" 
                                                       data-day="{{ $dayKey }}">
                                                <label class="form-check-label fw-bold" for="day_{{ $dayKey }}">
                                                    {{ $dayName }}
                                                </label>
                                            </div>
                                            
                                            <div class="day-schedule" id="schedule_{{ $dayKey }}" style="display: none;">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label class="form-label small">Hora de inicio</label>
                                                        <select class="form-select form-select-sm" 
                                                                name="availability[{{ $dayKey }}][start_time]" 
                                                                id="start_{{ $dayKey }}">
                                                            <option value="">Selecciona una hora</option>
                                                            @for($hour = 7; $hour <= 23; $hour++)
                                                                <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                                                <option value="{{ sprintf('%02d:30', $hour) }}">{{ sprintf('%02d:30', $hour) }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Nota:</strong> Una vez enviada la solicitud, será revisada por el administrador. 
                            Te notificaremos sobre la aprobación y la asignación de horarios.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('student.available-courses') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver a Cursos
                            </a>
                            <div>
                                <button type="button" class="btn btn-warning me-2" onclick="testFormSubmission()">
                                    <i class="fas fa-bug me-2"></i>
                                    Test Submit
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Enviar Solicitud
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="misc-wrapper">
                <h2 class="mb-2 mx-2">Acceso Denegado</h2>
                <p class="mb-4 mx-2">No tienes permisos para solicitar inscripción en cursos.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al Dashboard</a>
            </div>
        </div>
    @endif
</div>


@endsection


@push('js-link')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Handle day checkbox changes
    document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const scheduleDiv = document.getElementById('schedule_' + day);
            
            if (this.checked) {
                scheduleDiv.style.display = 'block';
                // Make time select required
                scheduleDiv.querySelectorAll('select').forEach(function(select) {
                    select.required = true;
                });
            } else {
                scheduleDiv.style.display = 'none';
                // Remove required and clear values
                scheduleDiv.querySelectorAll('select').forEach(function(select) {
                    select.required = false;
                    select.value = '';
                });
            }
        });
    });

    // Form validation
    document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
        console.log('Form submission started');
        console.log('Form action:', this.action);
        console.log('Form method:', this.method);
        
        const checkedDays = document.querySelectorAll('.day-checkbox:checked');
        console.log('Checked days:', checkedDays.length);
        
        if (checkedDays.length === 0) {
            e.preventDefault();
            alert('Por favor selecciona al menos un día de disponibilidad.');
            return;
        }

        // Validate time inputs for checked days
        let isValid = true;
        checkedDays.forEach(function(checkbox) {
            const day = checkbox.dataset.day;
            const startTime = document.getElementById('start_' + day).value;
            
            if (!startTime) {
                isValid = false;
                alert('Por favor selecciona una hora de inicio para ' + checkbox.nextElementSibling.textContent.trim());
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Por favor completa correctamente todos los horarios de disponibilidad.');
            return;
        }

        // Show loading state
        console.log('Form validation passed, submitting...');
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
        submitBtn.disabled = true;
    });
});

// Test function to bypass validation and test form submission
function testFormSubmission() {
    console.log('Test submission started');
    
    // Check if form exists
    const form = document.getElementById('enrollmentForm');
    if (!form) {
        console.error('Form not found!');
        alert('Form not found!');
        return;
    }
    
    console.log('Form found:', form);
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    
    // Check if CSRF token exists
    const csrfToken = document.querySelector('input[name="_token"]');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        alert('CSRF token not found!');
        return;
    }
    
    console.log('CSRF token found:', csrfToken.value);
    
    // Manually set some test data
    const mondayCheckbox = document.getElementById('day_monday');
    const mondayTime = document.getElementById('start_monday');
    
    if (mondayCheckbox && mondayTime) {
        mondayCheckbox.checked = true;
        mondayTime.value = '09:00';
        console.log('Set test data: Monday 09:00');
    }
    
    // Try to submit the form
    console.log('Attempting form submission...');
    
    // Test the actual form submission
    console.log('Submitting form to:', form.action);
    form.submit();
}
</script>
@endpush