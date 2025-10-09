@extends('layouts.app')

@section('title', 'Mi Horario')

@push('css-link')
    @include('partials.common.datatable_style')
    <style>
#scheduleCalendar {
    min-height: 400px;
}

.sessions-timeline {
    position: relative;
    padding-left: 2rem;
}

.sessions-timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    top: 1rem;
    width: 1rem;
    height: 1rem;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.timeline-marker i {
    font-size: 0.5rem;
    color: white;
}

.session-card {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
    transition: all 0.3s ease;
}

.session-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.session-header {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
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

.session-info {
    flex: 1;
}

.session-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.session-details {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.session-details span {
    display: flex;
    align-items: center;
}

.session-body {
    padding: 1rem;
    border-top: 1px solid #e3e6f0;
}

.session-topic, .session-notes {
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #495057;
}

.session-notes {
    font-style: italic;
}

.session-actions {
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #e3e6f0;
    display: flex;
    gap: 0.5rem;
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
    color: #6c757d;
    margin-bottom: 1rem;
}

.empty-state-description {
    color: #6c757d;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* Calendar Styles */
.fc-event {
    border-radius: 4px;
    border: none;
    padding: 2px 4px;
    font-size: 0.875rem;
}

.fc-event-title {
    font-weight: 500;
}

.fc-today {
    background-color: #f8f9fa !important;
}

.fc-daygrid-event {
    margin: 1px 0;
}

.fc-daygrid-day-number {
    color: #495057;
    font-weight: 500;
}

.fc-col-header-cell {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
}

.fc-button-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.fc-button-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.fc-button-primary:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.fc-button-primary:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
@endpush

@section('main-section')
<div class="content-header">
    <div class="content-title">
        <h1>Mi Horario</h1>
        <p class="content-subtitle">Calendario de clases y sesiones programadas</p>
    </div>
    <div class="content-actions">
        <button class="btn btn-primary" onclick="refreshSchedule()">
            <i class="fas fa-sync-alt"></i>
            Actualizar
        </button>
    </div>
</div>

@if($sessions->count() > 0)
    <!-- Schedule Calendar View -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Calendario de Clases
                    </h5>
                </div>
                <div class="card-body">
                    <div id="scheduleCalendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-list me-2"></i>
                        Lista de Sesiones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="sessions-timeline">
                        @foreach($sessions as $session)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="session-card">
                                        <div class="session-header">
                                            <div class="session-date">
                                                <div class="session-day">{{ \Carbon\Carbon::parse($session->date)->format('d') }}</div>
                                                <div class="session-month">{{ \Carbon\Carbon::parse($session->date)->format('M') }}</div>
                                                <div class="session-year">{{ \Carbon\Carbon::parse($session->date)->format('Y') }}</div>
                                            </div>
                                            <div class="session-info">
                                                <h6 class="session-title">{{ $session->group->course->name }}</h6>
                                                <div class="session-details">
                                                    <span class="session-time">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                    </span>
                                                    <span class="session-group">
                                                        <i class="fas fa-users me-1"></i>
                                                        {{ $session->group->name }}
                                                    </span>
                                                    <span class="session-teacher">
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        {{ $session->group->teacher->first_name }} {{ $session->group->teacher->last_name }}
                                                    </span>
                                                </div>
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
                                        @if($session->topic || $session->notes)
                                            <div class="session-body">
                                                @if($session->topic)
                                                    <div class="session-topic">
                                                        <strong>Tema:</strong> {{ $session->topic }}
                                                    </div>
                                                @endif
                                                @if($session->notes)
                                                    <div class="session-notes">
                                                        <strong>Notas:</strong> {{ $session->notes }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="session-actions">
                                            @if($session->group->virtual_link && $session->status !== 'cancelled')
                                                <a href="{{ $session->group->virtual_link }}" target="_blank" class="btn btn-sm btn-success">
                                                    <i class="fas fa-video me-1"></i>
                                                    Unirse a Clase
                                                </a>
                                            @endif
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewSessionDetails({{ $session->id }})">
                                                <i class="fas fa-eye me-1"></i>
                                                Ver Detalles
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- No Sessions State -->
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h3 class="empty-state-title">No hay sesiones programadas</h3>
        <p class="empty-state-description">
            Actualmente no tienes sesiones de clase programadas. Contacta con tu profesor o la administración para más información.
        </p>
        <div class="empty-state-actions">
            <a href="{{ route('student.courses') }}" class="btn btn-primary">
                <i class="fas fa-book me-1"></i>
                Ver Mis Cursos
            </a>
        </div>
    </div>
@endif
@endsection


@push('js-link')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('scheduleCalendar');
    
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: [
                @foreach($sessions as $session)
                {
                    id: {{ $session->id }},
                    title: '{{ $session->group->course->name }}',
                    start: '{{ $session->date }}T{{ $session->start_time }}',
                    end: '{{ $session->date }}T{{ $session->end_time }}',
                    backgroundColor: @if($session->status === 'completed') '#28a745' @elseif($session->status === 'cancelled') '#dc3545' @else '#007bff' @endif,
                    borderColor: @if($session->status === 'completed') '#28a745' @elseif($session->status === 'cancelled') '#dc3545' @else '#007bff' @endif,
                    extendedProps: {
                        group: '{{ $session->group->name }}',
                        teacher: '{{ $session->group->teacher->first_name }} {{ $session->group->teacher->last_name }}',
                        topic: '{{ $session->topic ?? '' }}',
                        notes: '{{ $session->notes ?? '' }}',
                        virtualLink: '{{ $session->group->virtual_link ?? '' }}',
                        status: '{{ $session->status }}'
                    }
                },
                @endforeach
            ],
            eventClick: function(info) {
                showSessionModal(info.event);
            },
            eventDidMount: function(info) {
                // Add tooltip
                info.el.setAttribute('title', 
                    info.event.extendedProps.group + ' - ' + 
                    info.event.extendedProps.teacher
                );
            }
        });
        
        calendar.render();
    }
});

function refreshSchedule() {
    location.reload();
}

function viewSessionDetails(sessionId) {
    // Find the session in the events
    var calendar = document.querySelector('#scheduleCalendar').__fullcalendar;
    var event = calendar.getEventById(sessionId);
    if (event) {
        showSessionModal(event);
    }
}

function showSessionModal(event) {
    var props = event.extendedProps;
    var startTime = new Date(event.start).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
    var endTime = new Date(event.end).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
    var date = new Date(event.start).toLocaleDateString('es-ES');
    
    var modalHtml = `
        <div class="modal fade" id="sessionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${event.title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="session-detail-item">
                            <strong>Fecha:</strong> ${date}
                        </div>
                        <div class="session-detail-item">
                            <strong>Hora:</strong> ${startTime} - ${endTime}
                        </div>
                        <div class="session-detail-item">
                            <strong>Grupo:</strong> ${props.group}
                        </div>
                        <div class="session-detail-item">
                            <strong>Profesor:</strong> ${props.teacher}
                        </div>
                        ${props.topic ? `<div class="session-detail-item"><strong>Tema:</strong> ${props.topic}</div>` : ''}
                        ${props.notes ? `<div class="session-detail-item"><strong>Notas:</strong> ${props.notes}</div>` : ''}
                        <div class="session-detail-item">
                            <strong>Estado:</strong> 
                            <span class="badge ${getStatusBadgeClass(props.status)}">${getStatusText(props.status)}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        ${props.virtualLink && props.status !== 'cancelled' ? 
                            `<a href="${props.virtualLink}" target="_blank" class="btn btn-success">
                                <i class="fas fa-video me-1"></i>
                                Unirse a Clase
                            </a>` : ''
                        }
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    var existingModal = document.getElementById('sessionModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('sessionModal'));
    modal.show();
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'completed': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-primary';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'completed': return 'Completada';
        case 'cancelled': return 'Cancelada';
        default: return 'Programada';
    }
}
</script>
@endpush
