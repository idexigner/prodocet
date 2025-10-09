@extends('layouts.app')

@section('title', 'Solicitudes de Inscripción')

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('enrollment-requests.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>
                    <i class="fas fa-user-plus me-2"></i>
                    Solicitudes de Inscripción
                </h1>
                <p class="text-muted mb-0">Gestiona las solicitudes de inscripción de estudiantes y asigna horarios automáticamente.</p>
            </div>
        </div>

    <!-- Enrollment Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Lista de Solicitudes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="enrollmentRequestsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Estudiante</th>
                                    <th>Email</th>
                                    <th>Curso</th>
                                    <th>Horas</th>
                                    <th>Costo</th>
                                    <th>Estado</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Request Details Modal -->
<div class="modal fade" id="enrollmentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Detalles de la Solicitud
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="enrollmentDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>
                    Rechazar Solicitud
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Motivo del Rechazo</label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="4" 
                                  placeholder="Explica por qué se rechaza esta solicitud..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>
                        Rechazar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Availability Matching Modal -->
<div class="modal fade" id="availabilityMatchingModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check me-2"></i>
                    Asignación Automática de Horarios
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="availabilityMatchingContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="autoAssignBtn" style="display: none;">
                    <i class="fas fa-magic me-2"></i>
                    Asignar Horarios Automáticamente
                </button>
            </div>
        </div>
    </div>
    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="misc-wrapper">
                <h2 class="mb-2 mx-2">Acceso Denegado</h2>
                <p class="mb-4 mx-2">No tienes permisos para ver las solicitudes de inscripción.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al Dashboard</a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('js-link')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#enrollmentRequestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('enrollment-requests.index') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'student_name', name: 'student_name' },
            { data: 'student_email', name: 'student_email' },
            { data: 'course_name', name: 'course_name' },
            { data: 'total_hours', name: 'total_hours' },
            { data: 'total_cost_formatted', name: 'total_cost_formatted' },
            { data: 'status_badge', name: 'status_badge' },
            { data: 'requested_at_formatted', name: 'requested_at_formatted' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[7, 'desc']], // Sort by requested_at descending
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        }
    });

    // View details
    $(document).on('click', '.view-btn', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('enrollment-requests.show', '') }}/" + id)
            .done(function(response) {
                if (response.success) {
                    displayEnrollmentDetails(response.data);
                    $('#enrollmentDetailsModal').modal('show');
                } else {
                    showAlert('Error', response.message, 'error');
                }
            })
            .fail(function() {
                showAlert('Error', 'No se pudieron cargar los detalles', 'error');
            });
    });

    // Approve enrollment
    $(document).on('click', '.approve-btn', function() {
        var id = $(this).data('id');
        
        if (confirm('¿Estás seguro de que quieres aprobar esta solicitud de inscripción?')) {
            $.post("{{ route('enrollment-requests.approve', '') }}/" + id, {
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                if (response.success) {
                    showAlert('Éxito', response.message, 'success');
                    table.ajax.reload();
                } else {
                    showAlert('Error', response.message, 'error');
                }
            })
            .fail(function() {
                showAlert('Error', 'No se pudo aprobar la solicitud', 'error');
            });
        }
    });

    // Reject enrollment
    $(document).on('click', '.reject-btn', function() {
        var id = $(this).data('id');
        $('#rejectForm').data('id', id);
        $('#rejectModal').modal('show');
    });

    // Handle reject form submission
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        var id = $(this).data('id');
        var reason = $('#rejectionReason').val();
        
        $.post("{{ route('enrollment-requests.reject', '') }}/" + id, {
            _token: '{{ csrf_token() }}',
            rejection_reason: reason
        })
        .done(function(response) {
            if (response.success) {
                showAlert('Éxito', response.message, 'success');
                $('#rejectModal').modal('hide');
                $('#rejectForm')[0].reset();
                table.ajax.reload();
            } else {
                showAlert('Error', response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Error', 'No se pudo rechazar la solicitud', 'error');
        });
    });

    // Show availability matching
    $(document).on('click', '.availability-btn', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('enrollment-requests.availability-matching', '') }}/" + id)
            .done(function(response) {
                if (response.success) {
                    displayAvailabilityMatching(response.data);
                    $('#availabilityMatchingModal').modal('show');
                } else {
                    showAlert('Error', response.message, 'error');
                }
            })
            .fail(function() {
                showAlert('Error', 'No se pudo cargar la información de disponibilidad', 'error');
            });
    });

    // Display enrollment details
    function displayEnrollmentDetails(data) {
        var content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Información del Estudiante</h6>
                    <ul class="list-unstyled">
                        <li><strong>Nombre:</strong> ${data.student.name}</li>
                        <li><strong>Email:</strong> ${data.student.email}</li>
                        <li><strong>Teléfono:</strong> ${data.student.phone || 'N/A'}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Información del Curso</h6>
                    <ul class="list-unstyled">
                        <li><strong>Curso:</strong> ${data.course.name}</li>
                        <li><strong>Nivel:</strong> ${data.course.level ? data.course.level.name : 'N/A'}</li>
                        <li><strong>Idioma:</strong> ${data.course.language ? data.course.language.name : 'N/A'}</li>
                        <li><strong>Horas totales:</strong> ${data.total_hours}</li>
                        <li><strong>Costo total:</strong> $${parseFloat(data.total_cost).toFixed(2)}</li>
                    </ul>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h6>Disponibilidad del Estudiante</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Hora de Inicio</th>
                                    <th>Hora de Fin</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        const days = {
            'monday': 'Lunes',
            'tuesday': 'Martes', 
            'wednesday': 'Miércoles',
            'thursday': 'Jueves',
            'friday': 'Viernes',
            'saturday': 'Sábado',
            'sunday': 'Domingo'
        };
        
        Object.entries(data.availability).forEach(([day, times]) => {
            if (times && times.start_time && times.end_time) {
                content += `
                    <tr>
                        <td>${days[day] || day}</td>
                        <td>${times.start_time}</td>
                        <td>${times.end_time}</td>
                    </tr>
                `;
            }
        });
        
        content += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h6>Temas del Curso</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Orden</th>
                                    <th>Tema</th>
                                    <th>Horas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        data.student_topics.forEach(topic => {
            content += `
                <tr>
                    <td>${topic.order}</td>
                    <td>${topic.topic.name}</td>
                    <td>${topic.topic.teaching_hours}</td>
                    <td><span class="badge bg-secondary">${topic.status}</span></td>
                </tr>
            `;
        });
        
        content += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        
        $('#enrollmentDetailsContent').html(content);
    }

    // Display availability matching
    function displayAvailabilityMatching(data) {
        var content = `
            <div class="row">
                <div class="col-12">
                    <h6>Disponibilidad del Estudiante</h6>
                    <div class="alert alert-info">
                        <strong>Estudiante:</strong> ${data.enrollment_request.student.name}
                    </div>
                </div>
            </div>
            
            <div class="row">
        `;
        
        data.teacher_matching.forEach(teacher => {
            content += `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                ${teacher.teacher.name}
                            </h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Email:</strong> ${teacher.teacher.email}</p>
                            
                            <h6>Horarios Disponibles:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Día</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
            `;
            
            Object.entries(teacher.availability).forEach(([day, times]) => {
                if (times && times.start_time && times.end_time) {
                    content += `
                        <tr>
                            <td>${day}</td>
                            <td>${times.start_time}</td>
                            <td>${times.end_time}</td>
                        </tr>
                    `;
                }
            });
            
            content += `
                                    </tbody>
                                </table>
                            </div>
                            
                            <h6>Horarios Compatibles:</h6>
            `;
            
            if (teacher.matching_slots.length > 0) {
                content += `
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                teacher.matching_slots.forEach(slot => {
                    content += `
                        <tr>
                            <td>${slot.day}</td>
                            <td>${slot.start_time}</td>
                            <td>${slot.end_time}</td>
                            <td>
                                <input type="checkbox" class="form-check-input slot-checkbox" 
                                       data-day="${slot.day}" 
                                       data-start="${slot.start_time}" 
                                       data-end="${slot.end_time}"
                                       data-teacher-id="${teacher.teacher.id}">
                            </td>
                        </tr>
                    `;
                });
                
                content += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                content += '<p class="text-muted">No hay horarios compatibles con este profesor.</p>';
            }
            
            content += `
                        </div>
                    </div>
                </div>
            `;
        });
        
        content += `
            </div>
        `;
        
        $('#availabilityMatchingContent').html(content);
        
        // Show auto-assign button if there are matching slots
        var hasMatchingSlots = data.teacher_matching.some(teacher => teacher.matching_slots.length > 0);
        if (hasMatchingSlots) {
            $('#autoAssignBtn').show();
        }
    }

    // Auto-assign slots
    $('#autoAssignBtn').on('click', function() {
        var selectedSlots = [];
        var teacherId = null;
        
        $('.slot-checkbox:checked').each(function() {
            if (!teacherId) {
                teacherId = $(this).data('teacher-id');
            }
            
            selectedSlots.push({
                day: $(this).data('day'),
                start_time: $(this).data('start'),
                end_time: $(this).data('end')
            });
        });
        
        if (selectedSlots.length === 0) {
            showAlert('Advertencia', 'Por favor selecciona al menos un horario', 'warning');
            return;
        }
        
        if (confirm('¿Estás seguro de que quieres asignar estos horarios automáticamente?')) {
            var enrollmentId = $('#autoAssignBtn').data('enrollment-id');
            
            $.post("{{ route('enrollment-requests.auto-assign-slots', '') }}/" + enrollmentId, {
                _token: '{{ csrf_token() }}',
                teacher_id: teacherId,
                slots: selectedSlots
            })
            .done(function(response) {
                if (response.success) {
                    showAlert('Éxito', response.message, 'success');
                    $('#availabilityMatchingModal').modal('hide');
                    table.ajax.reload();
                } else {
                    showAlert('Error', response.message, 'error');
                }
            })
            .fail(function() {
                showAlert('Error', 'No se pudieron asignar los horarios', 'error');
            });
        }
    });

    // Show alert function
    function showAlert(title, message, type) {
        // You can implement your preferred alert system here
        alert(title + ': ' + message);
    }
});
</script>
@endpush

