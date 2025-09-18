@extends('layouts.app')

@section('title', 'Calendario')

@section('main-section')
<div class="content-header">
    <div class="content-title">
        <h1>Calendario y Programación</h1>
        <p class="content-subtitle">Gestionar horarios, clases y eventos del sistema</p>
    </div>
    <div class="content-actions">
        <button class="btn btn-success" onclick="openNewEventModal()">
            <i class="fas fa-plus"></i>
            Nueva Clase
        </button>
        <button class="btn btn-primary" onclick="openScheduleModal()">
            <i class="fas fa-clock"></i>
            Programar Evento
        </button>
        <button class="btn btn-outline-secondary" onclick="exportCalendar()">
            <i class="fas fa-download"></i>
            Exportar
        </button>
    </div>
</div>

<!-- Calendar Filters and Controls -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Filtrar por Profesor</label>
                        <select class="form-select" id="teacherFilter">
                            <option value="">Todos los profesores</option>
                            <option value="garcia">Prof. García</option>
                            <option value="martin">Prof. Martín</option>
                            <option value="lopez">Prof. López</option>
                            <option value="schmidt">Prof. Schmidt</option>
                            <option value="rossi">Prof. Rossi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filtrar por Idioma</label>
                        <select class="form-select" id="languageFilter">
                            <option value="">Todos los idiomas</option>
                            <option value="english">Inglés</option>
                            <option value="french">Francés</option>
                            <option value="german">Alemán</option>
                            <option value="italian">Italiano</option>
                            <option value="portuguese">Portugués</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Evento</label>
                        <select class="form-select" id="eventTypeFilter">
                            <option value="">Todos los tipos</option>
                            <option value="class">Clases Regulares</option>
                            <option value="exam">Exámenes</option>
                            <option value="holiday">Días Festivos</option>
                            <option value="meeting">Reuniones</option>
                            <option value="other">Otros</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="applyCalendarFilters()">
                            <i class="fas fa-filter"></i>
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar and Sidebar -->
<div class="row">
    <!-- Main Calendar -->
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Calendario de Clases
                    </h5>
                    <div class="calendar-view-controls">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeCalendarView('dayGridMonth')">
                                Mes
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm active" onclick="changeCalendarView('timeGridWeek')">
                                Semana
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeCalendarView('timeGridDay')">
                                Día
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeCalendarView('listWeek')">
                                Lista
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Calendar Sidebar -->
    <div class="col-lg-3">
        <!-- Today's Classes -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Clases de Hoy
                </h6>
            </div>
            <div class="card-body">
                <div id="todayClasses">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calendar-check me-2"></i>
                    Próximos Eventos
                </h6>
            </div>
            <div class="card-body">
                <div id="upcomingEvents">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Calendar Legend -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-palette me-2"></i>
                    Leyenda
                </h6>
            </div>
            <div class="card-body">
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-color" style="background: #4f46e5;"></span>
                        <span>Clases Regulares</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #10b981;"></span>
                        <span>Exámenes</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #f59e0b;"></span>
                        <span>Club de Conversación</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #ef4444;"></span>
                        <span>Días Festivos</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #8b5cf6;"></span>
                        <span>Tutoría</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- New Event Modal -->
<div class="modal fade" id="newEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Nueva Clase
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newEventForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventTitle" class="form-label">Título de la Clase *</label>
                                <input type="text" class="form-control" id="eventTitle" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventGroup" class="form-label">Grupo</label>
                                <select class="form-select" id="eventGroup">
                                    <option value="">Seleccionar grupo</option>
                                    <option value="GRP001">Inglés Básico A1</option>
                                    <option value="GRP002">Francés Intermedio B1</option>
                                    <option value="GRP003">Alemán Avanzado C1</option>
                                    <option value="GRP004">Italiano Elemental A2</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventTeacher" class="form-label">Profesor *</label>
                                <select class="form-select" id="eventTeacher" required>
                                    <option value="">Seleccionar profesor</option>
                                    <option value="garcia">Prof. García</option>
                                    <option value="martin">Prof. Martín</option>
                                    <option value="lopez">Prof. López</option>
                                    <option value="schmidt">Prof. Schmidt</option>
                                    <option value="rossi">Prof. Rossi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventType" class="form-label">Tipo de Evento</label>
                                <select class="form-select" id="eventType">
                                    <option value="class">Clase Regular</option>
                                    <option value="exam">Examen</option>
                                    <option value="meeting">Reunión</option>
                                    <option value="other">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventDate" class="form-label">Fecha *</label>
                                <input type="date" class="form-control" id="eventDate" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="eventStartTime" class="form-label">Hora de Inicio *</label>
                                <input type="time" class="form-control" id="eventStartTime" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="eventEndTime" class="form-label">Hora de Fin *</label>
                                <input type="time" class="form-control" id="eventEndTime" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventRoom" class="form-label">Aula/Ubicación</label>
                                <input type="text" class="form-control" id="eventRoom" placeholder="Ej: Aula 1, Virtual, etc.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventColor" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" id="eventColor" value="#4f46e5">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="eventDescription" class="form-label">Descripción/Notas</label>
                                <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="eventRecurring">
                                <label class="form-check-label" for="eventRecurring">
                                    Evento recurrente
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="eventNotification">
                                <label class="form-check-label" for="eventNotification">
                                    Enviar notificaciones
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveEvent()">
                    <i class="fas fa-save"></i>
                    Guardar Clase
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Detalles del Evento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetailsContent">
                <!-- Content populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="rescheduleEvent()">
                    <i class="fas fa-calendar-alt"></i>
                    Reprogramar
                </button>
                <button type="button" class="btn btn-primary" onclick="editEvent()">
                    <i class="fas fa-edit"></i>
                    Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Reprogramar Clase
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rescheduleForm">
                    <div class="mb-3">
                        <label for="rescheduleDate" class="form-label">Nueva Fecha</label>
                        <input type="date" class="form-control" id="rescheduleDate" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="rescheduleStartTime" class="form-label">Hora de Inicio</label>
                                <input type="time" class="form-control" id="rescheduleStartTime" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="rescheduleEndTime" class="form-label">Hora de Fin</label>
                                <input type="time" class="form-control" id="rescheduleEndTime" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rescheduleReason" class="form-label">Motivo de la Reprogramación</label>
                        <textarea class="form-control" id="rescheduleReason" rows="3" placeholder="Explica por qué se reprograma la clase"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyStudents" checked>
                        <label class="form-check-label" for="notifyStudents">
                            Notificar a los estudiantes
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="confirmReschedule()">
                    <i class="fas fa-check"></i>
                    Confirmar Reprogramación
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/calendar.js') }}"></script>
@endpush
