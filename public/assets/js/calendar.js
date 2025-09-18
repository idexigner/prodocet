/**
 * PRODOCET LMS - Calendar JavaScript
 * Author: Assistant
 * Description: JavaScript functionality for calendar and scheduling page
 */

// Global variables for calendar management
let calendar = null;
let currentEventData = null;
let filteredEvents = [];

// Sample calendar events data
const CALENDAR_EVENTS = [
    {
        id: 'evt001',
        title: 'Inglés Básico A1',
        start: new Date(2024, 0, 15, 9, 0),
        end: new Date(2024, 0, 15, 10, 30),
        teacher: 'Prof. García',
        teacherId: 'garcia',
        group: 'GRP001',
        type: 'class',
        language: 'english',
        level: 'A1',
        room: 'Aula 1',
        students: 8,
        color: '#4f46e5',
        description: 'Clase de introducción al inglés básico',
        recurring: true,
        daysOfWeek: [1, 3], // Monday and Wednesday
        startRecur: '2024-01-15',
        endRecur: '2024-04-15'
    },
    {
        id: 'evt002',
        title: 'Francés Intermedio B1',
        start: new Date(2024, 0, 16, 14, 0),
        end: new Date(2024, 0, 16, 15, 30),
        teacher: 'Prof. Martín',
        teacherId: 'martin',
        group: 'GRP002',
        type: 'class',
        language: 'french',
        level: 'B1',
        room: 'Aula 2',
        students: 6,
        color: '#8b5cf6',
        description: 'Práctica de conversación en francés',
        recurring: true,
        daysOfWeek: [2, 4], // Tuesday and Thursday
        startRecur: '2024-01-16',
        endRecur: '2024-05-16'
    },
    {
        id: 'evt003',
        title: 'Examen Alemán C1',
        start: new Date(2024, 0, 19, 16, 0),
        end: new Date(2024, 0, 19, 18, 0),
        teacher: 'Prof. Schmidt',
        teacherId: 'schmidt',
        group: 'GRP003',
        type: 'exam',
        language: 'german',
        level: 'C1',
        room: 'Aula 3',
        students: 4,
        color: '#10b981',
        description: 'Examen final de certificación C1'
    },
    {
        id: 'evt004',
        title: 'Reunión Departamental',
        start: new Date(2024, 0, 22, 10, 0),
        end: new Date(2024, 0, 22, 11, 30),
        teacher: 'Todos',
        teacherId: 'all',
        type: 'meeting',
        room: 'Sala de Juntas',
        color: '#f59e0b',
        description: 'Reunión mensual del departamento de idiomas'
    },
    {
        id: 'evt005',
        title: 'Día de Reyes',
        start: new Date(2024, 0, 6),
        end: new Date(2024, 0, 7),
        type: 'holiday',
        color: '#ef4444',
        description: 'Día festivo - Centro cerrado',
        allDay: true
    },
    {
        id: 'evt006',
        title: 'Italiano Elemental A2',
        start: new Date(2024, 0, 15, 11, 0),
        end: new Date(2024, 0, 15, 12, 30),
        teacher: 'Prof. Rossi',
        teacherId: 'rossi',
        group: 'GRP004',
        type: 'class',
        language: 'italian',
        level: 'A2',
        room: 'Aula 4',
        students: 7,
        color: '#10b981',
        description: 'Gramática italiana intermedia',
        recurring: true,
        daysOfWeek: [1, 3], // Monday and Wednesday
        startRecur: '2024-01-15',
        endRecur: '2024-06-15'
    }
];

// Initialize calendar page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('calendar.html')) {
        initializeCalendarPage();
    }
});

/**
 * Initialize the calendar page
 */
function initializeCalendarPage() {
    initializeFullCalendar();
    loadTodayClasses();
    loadUpcomingEvents();
    setupCalendarEventListeners();
    
    // Check URL parameters for specific date
    const urlParams = new URLSearchParams(window.location.search);
    const targetDate = urlParams.get('date');
    if (targetDate && calendar) {
        calendar.gotoDate(targetDate);
    }
    
    const groupFilter = urlParams.get('group');
    if (groupFilter) {
        filterEventsByGroup(groupFilter);
    }
}

/**
 * Initialize FullCalendar
 */
function initializeFullCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: '' // We'll use custom view controls
        },
        locale: 'es',
        firstDay: 1, // Monday
        height: 'auto',
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6], // Monday through Saturday
            startTime: '08:00',
            endTime: '20:00'
        },
        events: generateCalendarEvents(),
        eventDisplay: 'block',
        dayMaxEvents: true,
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            openNewEventModal(info.dateStr);
        },
        eventDrop: function(info) {
            handleEventDrop(info);
        },
        eventResize: function(info) {
            handleEventResize(info);
        },
        select: function(info) {
            openNewEventModal(info.startStr, info.endStr);
        },
        selectable: true,
        selectMirror: true,
        editable: true,
        eventDidMount: function(info) {
            // Add custom classes based on event type
            info.el.classList.add(`event-${info.event.extendedProps.type}`);
            
            // Add tooltip
            info.el.title = `${info.event.title}\n${info.event.start.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            })} - ${info.event.end?.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            })}\n${info.event.extendedProps.description || ''}`;
        },
        eventClassNames: function(arg) {
            return [`event-${arg.event.extendedProps.type || 'other'}`];
        }
    });

    calendar.render();
    
    // Apply saved color scheme to calendar
    updateCalendarColors();
}

/**
 * Generate calendar events from sample data
 */
function generateCalendarEvents() {
    const events = [];
    const today = new Date();
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 2, 0);
    
    CALENDAR_EVENTS.forEach(eventTemplate => {
        if (eventTemplate.recurring && eventTemplate.daysOfWeek) {
            // Generate recurring events
            const startDate = new Date(eventTemplate.startRecur);
            const endDate = new Date(eventTemplate.endRecur);
            
            for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                if (eventTemplate.daysOfWeek.includes(d.getDay())) {
                    const eventStart = new Date(d);
                    eventStart.setHours(eventTemplate.start.getHours(), eventTemplate.start.getMinutes());
                    
                    const eventEnd = new Date(d);
                    eventEnd.setHours(eventTemplate.end.getHours(), eventTemplate.end.getMinutes());
                    
                    events.push({
                        ...eventTemplate,
                        id: `${eventTemplate.id}_${d.toISOString().split('T')[0]}`,
                        start: eventStart,
                        end: eventEnd,
                        extendedProps: {
                            ...eventTemplate,
                            originalId: eventTemplate.id
                        }
                    });
                }
            }
        } else {
            // Single event
            events.push({
                ...eventTemplate,
                extendedProps: eventTemplate
            });
        }
    });
    
    return events;
}

/**
 * Setup calendar event listeners
 */
function setupCalendarEventListeners() {
    // Filter change events
    const filters = ['teacherFilter', 'languageFilter', 'eventTypeFilter'];
    filters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.addEventListener('change', applyCalendarFilters);
        }
    });
    
    // Auto-fill end time when start time changes
    const startTimeField = document.getElementById('eventStartTime');
    const endTimeField = document.getElementById('eventEndTime');
    
    if (startTimeField && endTimeField) {
        startTimeField.addEventListener('change', function() {
            if (!endTimeField.value && this.value) {
                const startTime = new Date(`1970-01-01T${this.value}`);
                const endTime = new Date(startTime.getTime() + 90 * 60000); // Add 90 minutes
                endTimeField.value = endTime.toTimeString().slice(0, 5);
            }
        });
    }
}

/**
 * Change calendar view
 */
function changeCalendarView(view) {
    if (calendar) {
        calendar.changeView(view);
        
        // Update active button
        const buttons = document.querySelectorAll('.calendar-view-controls .btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        showNotification(`Vista cambiada a: ${getViewName(view)}`, 'info', 2000);
    }
}

/**
 * Get view name in Spanish
 */
function getViewName(view) {
    const names = {
        'dayGridMonth': 'Mes',
        'timeGridWeek': 'Semana',
        'timeGridDay': 'Día',
        'listWeek': 'Lista'
    };
    return names[view] || view;
}

/**
 * Apply calendar filters
 */
function applyCalendarFilters() {
    const teacherFilter = document.getElementById('teacherFilter')?.value;
    const languageFilter = document.getElementById('languageFilter')?.value;
    const typeFilter = document.getElementById('eventTypeFilter')?.value;
    
    let allEvents = generateCalendarEvents();
    
    // Apply filters
    if (teacherFilter) {
        allEvents = allEvents.filter(event => 
            event.extendedProps.teacherId === teacherFilter
        );
    }
    
    if (languageFilter) {
        allEvents = allEvents.filter(event => 
            event.extendedProps.language === languageFilter
        );
    }
    
    if (typeFilter) {
        allEvents = allEvents.filter(event => 
            event.extendedProps.type === typeFilter
        );
    }
    
    // Update calendar
    if (calendar) {
        calendar.removeAllEvents();
        calendar.addEventSource(allEvents);
    }
    
    filteredEvents = allEvents;
    
    showNotification(`Filtros aplicados. Mostrando ${allEvents.length} evento(s).`, 'info');
}

/**
 * Load today's classes
 */
function loadTodayClasses() {
    const container = document.getElementById('todayClasses');
    if (!container) return;
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const todayEvents = generateCalendarEvents().filter(event => {
        const eventDate = new Date(event.start);
        return eventDate >= today && eventDate < tomorrow && event.extendedProps.type === 'class';
    });
    
    if (todayEvents.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h6>No hay clases hoy</h6>
                <p>Disfruta tu día libre</p>
            </div>
        `;
        return;
    }
    
    // Sort by start time
    todayEvents.sort((a, b) => new Date(a.start) - new Date(b.start));
    
    container.innerHTML = todayEvents.map(event => {
        const startTime = new Date(event.start);
        const isNow = isEventHappening(event);
        const isPast = new Date() > new Date(event.end);
        
        return `
            <div class="today-class-item ${isNow ? 'happening-now' : ''} ${isPast ? 'past-event' : ''}" 
                 onclick="showEventDetails('${event.id}')">
                <div class="class-time" style="background-color: ${event.color}">
                    <div class="time">${startTime.toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</div>
                    <div class="period">${startTime.getHours() < 12 ? 'AM' : 'PM'}</div>
                </div>
                <div class="class-info">
                    <div class="class-title">${event.title}</div>
                    <div class="class-details">
                        <div class="class-teacher">
                            <i class="fas fa-user"></i>
                            <span>${event.extendedProps.teacher}</span>
                        </div>
                        <div class="class-students">
                            <i class="fas fa-users"></i>
                            <span>${event.extendedProps.students || 0} estudiantes</span>
                        </div>
                        <div class="class-room">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${event.extendedProps.room || 'Por definir'}</span>
                        </div>
                    </div>
                </div>
                ${isNow ? '<div class="live-indicator"><i class="fas fa-circle"></i> En curso</div>' : ''}
            </div>
        `;
    }).join('');
}

/**
 * Load upcoming events
 */
function loadUpcomingEvents() {
    const container = document.getElementById('upcomingEvents');
    if (!container) return;
    
    const today = new Date();
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
    
    const upcomingEvents = generateCalendarEvents().filter(event => {
        const eventDate = new Date(event.start);
        return eventDate > today && eventDate <= nextWeek;
    });
    
    if (upcomingEvents.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <h6>No hay eventos próximos</h6>
                <p>Los próximos 7 días están libres</p>
            </div>
        `;
        return;
    }
    
    // Sort by date
    upcomingEvents.sort((a, b) => new Date(a.start) - new Date(b.start));
    
    // Show only next 5 events
    const eventsToShow = upcomingEvents.slice(0, 5);
    
    container.innerHTML = eventsToShow.map(event => {
        const eventDate = new Date(event.start);
        const isToday = eventDate.toDateString() === today.toDateString();
        const isTomorrow = eventDate.toDateString() === new Date(today.getTime() + 24 * 60 * 60 * 1000).toDateString();
        
        let dateLabel;
        if (isToday) {
            dateLabel = 'Hoy';
        } else if (isTomorrow) {
            dateLabel = 'Mañana';
        } else {
            dateLabel = eventDate.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
        }
        
        return `
            <div class="upcoming-event-item" onclick="showEventDetails('${event.id}')">
                <div class="event-time" style="background-color: ${event.color}">
                    <div class="date">${dateLabel}</div>
                    <div class="time">${eventDate.toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</div>
                </div>
                <div class="event-info">
                    <div class="event-title">${event.title}</div>
                    <div class="event-details">
                        ${event.extendedProps.teacher ? `
                        <div class="event-teacher">
                            <i class="fas fa-user"></i>
                            <span>${event.extendedProps.teacher}</span>
                        </div>
                        ` : ''}
                        <div class="event-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${event.extendedProps.room || 'Ubicación por definir'}</span>
                        </div>
                        <div class="event-type">
                            <i class="fas fa-tag"></i>
                            <span>${getEventTypeName(event.extendedProps.type)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Check if event is currently happening
 */
function isEventHappening(event) {
    const now = new Date();
    const start = new Date(event.start);
    const end = new Date(event.end);
    
    return now >= start && now <= end;
}

/**
 * Get event type name in Spanish
 */
function getEventTypeName(type) {
    const types = {
        'class': 'Clase',
        'exam': 'Examen',
        'meeting': 'Reunión',
        'holiday': 'Día Festivo',
        'other': 'Otro'
    };
    return types[type] || 'Otro';
}

/**
 * Open new event modal
 */
function openNewEventModal(startDate = null, endDate = null) {
    const modal = new bootstrap.Modal(document.getElementById('newEventModal'));
    modal.show();
    
    // Reset form
    document.getElementById('newEventForm').reset();
    
    // Pre-fill date and time if provided
    if (startDate) {
        const date = new Date(startDate);
        document.getElementById('eventDate').value = date.toISOString().split('T')[0];
        
        if (startDate.includes('T')) {
            document.getElementById('eventStartTime').value = date.toTimeString().slice(0, 5);
        }
    }
    
    if (endDate) {
        const date = new Date(endDate);
        if (endDate.includes('T')) {
            document.getElementById('eventEndTime').value = date.toTimeString().slice(0, 5);
        }
    }
    
    // Focus on title field
    setTimeout(() => {
        document.getElementById('eventTitle')?.focus();
    }, 300);
}

/**
 * Save new event
 */
function saveEvent() {
    const form = document.getElementById('newEventForm');
    const formData = new FormData(form);
    
    // Validate required fields
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // Create event object
    const startDate = formData.get('eventDate');
    const startTime = formData.get('eventStartTime');
    const endTime = formData.get('eventEndTime');
    
    const eventStart = new Date(`${startDate}T${startTime}`);
    const eventEnd = new Date(`${startDate}T${endTime}`);
    
    const newEvent = {
        id: 'evt_' + Date.now(),
        title: formData.get('eventTitle'),
        start: eventStart,
        end: eventEnd,
        color: formData.get('eventColor'),
        extendedProps: {
            teacher: getTeacherName(formData.get('eventTeacher')),
            teacherId: formData.get('eventTeacher'),
            group: formData.get('eventGroup'),
            type: formData.get('eventType'),
            room: formData.get('eventRoom'),
            description: formData.get('eventDescription'),
            recurring: formData.has('eventRecurring'),
            notifications: formData.has('eventNotification')
        }
    };
    
    // Add to calendar
    if (calendar) {
        calendar.addEvent(newEvent);
    }
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('newEventModal'));
    modal.hide();
    
    // Show success message
    showNotification('Evento creado exitosamente', 'success');
    
    // Refresh sidebar
    loadTodayClasses();
    loadUpcomingEvents();
}

/**
 * Show event details
 */
function showEventDetails(eventId) {
    let event;
    
    if (typeof eventId === 'string') {
        // Find event by ID
        const events = calendar ? calendar.getEvents() : generateCalendarEvents();
        event = events.find(e => e.id === eventId);
    } else {
        // Event object passed directly
        event = eventId;
    }
    
    if (!event) return;
    
    currentEventData = event;
    
    const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    const content = document.getElementById('eventDetailsContent');
    
    const startTime = new Date(event.start);
    const endTime = event.end ? new Date(event.end) : null;
    const props = event.extendedProps || {};
    
    content.innerHTML = `
        <div class="event-detail-header">
            <div class="event-detail-title">
                <h4>${event.title}</h4>
                <div class="event-detail-badges">
                    <span class="badge bg-primary">${getEventTypeName(props.type)}</span>
                    ${props.language ? `<span class="language-badge language-${props.language}">${getLanguageName(props.language)}</span>` : ''}
                    ${props.level ? `<span class="level-badge">${props.level}</span>` : ''}
                </div>
            </div>
        </div>
        
        <div class="event-detail-info">
            <div class="info-group">
                <h6>Información Básica</h6>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span>${startTime.toLocaleDateString('es-ES', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    })}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>${startTime.toLocaleTimeString('es-ES', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    })}${endTime ? ` - ${endTime.toLocaleTimeString('es-ES', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    })}` : ''}</span>
                </div>
                ${props.room ? `
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${props.room}</span>
                </div>
                ` : ''}
            </div>
            
            ${props.teacher ? `
            <div class="info-group">
                <h6>Detalles de la Clase</h6>
                <div class="info-item">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>${props.teacher}</span>
                </div>
                ${props.students ? `
                <div class="info-item">
                    <i class="fas fa-users"></i>
                    <span>${props.students} estudiantes</span>
                </div>
                ` : ''}
                ${props.group ? `
                <div class="info-item">
                    <i class="fas fa-tag"></i>
                    <span>Grupo: ${props.group}</span>
                </div>
                ` : ''}
            </div>
            ` : ''}
        </div>
        
        ${props.description ? `
        <div class="info-section">
            <h6>Descripción</h6>
            <p>${props.description}</p>
        </div>
        ` : ''}
        
        <div class="event-actions mt-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <button class="btn btn-outline-primary w-100" onclick="markAttendance('${event.id}')">
                        <i class="fas fa-check-circle me-2"></i>Tomar Asistencia
                    </button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-outline-success w-100" onclick="viewGroupDetails('${props.group}')">
                        <i class="fas fa-users me-2"></i>Ver Grupo
                    </button>
                </div>
            </div>
        </div>
    `;
    
    modal.show();
}

/**
 * Handle event drop (drag and drop)
 */
function handleEventDrop(info) {
    const event = info.event;
    const oldStart = info.oldEvent.start;
    const newStart = event.start;
    
    // Show confirmation
    if (confirm(`¿Confirmar mover "${event.title}" de ${oldStart.toLocaleString('es-ES')} a ${newStart.toLocaleString('es-ES')}?`)) {
        showNotification('Evento movido exitosamente', 'success');
        
        // If this is a recurring event, ask if they want to move all instances
        if (event.extendedProps.recurring) {
            if (confirm('¿Quieres mover todas las instancias de este evento recurrente?')) {
                // Handle moving all recurring instances
                moveRecurringEvent(event, oldStart, newStart);
            }
        }
        
        // Refresh sidebar
        loadTodayClasses();
        loadUpcomingEvents();
    } else {
        // Revert the change
        info.revert();
    }
}

/**
 * Handle event resize
 */
function handleEventResize(info) {
    const event = info.event;
    const oldEnd = info.oldEvent.end;
    const newEnd = event.end;
    
    const duration = Math.round((newEnd - event.start) / (1000 * 60)); // Duration in minutes
    
    if (confirm(`¿Confirmar cambiar la duración de "${event.title}" a ${duration} minutos?`)) {
        showNotification('Duración del evento actualizada', 'success');
    } else {
        info.revert();
    }
}

/**
 * Move recurring event instances
 */
function moveRecurringEvent(event, oldStart, newStart) {
    // This would typically involve an API call to update all recurring instances
    showNotification('Todas las instancias del evento recurrente han sido movidas', 'info');
}

/**
 * Reschedule event
 */
function rescheduleEvent() {
    if (!currentEventData) return;
    
    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    
    // Pre-fill current event data
    const startDate = new Date(currentEventData.start);
    const endDate = currentEventData.end ? new Date(currentEventData.end) : null;
    
    document.getElementById('rescheduleDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('rescheduleStartTime').value = startDate.toTimeString().slice(0, 5);
    
    if (endDate) {
        document.getElementById('rescheduleEndTime').value = endDate.toTimeString().slice(0, 5);
    }
    
    // Close details modal and show reschedule modal
    bootstrap.Modal.getInstance(document.getElementById('eventDetailsModal')).hide();
    modal.show();
}

/**
 * Confirm reschedule
 */
function confirmReschedule() {
    const formData = new FormData(document.getElementById('rescheduleForm'));
    
    const newDate = formData.get('rescheduleDate');
    const newStartTime = formData.get('rescheduleStartTime');
    const newEndTime = formData.get('rescheduleEndTime');
    const reason = formData.get('rescheduleReason');
    const notifyStudents = formData.has('notifyStudents');
    
    if (!newDate || !newStartTime || !newEndTime) {
        showNotification('Por favor, completa todos los campos requeridos', 'warning');
        return;
    }
    
    // Update event
    if (currentEventData && calendar) {
        const newStart = new Date(`${newDate}T${newStartTime}`);
        const newEnd = new Date(`${newDate}T${newEndTime}`);
        
        currentEventData.setStart(newStart);
        currentEventData.setEnd(newEnd);
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('rescheduleModal'));
        modal.hide();
        
        // Show success message
        showNotification('Evento reprogramado exitosamente', 'success');
        
        if (notifyStudents) {
            showNotification('Notificaciones enviadas a los estudiantes', 'info');
        }
        
        // Refresh sidebar
        loadTodayClasses();
        loadUpcomingEvents();
    }
}

/**
 * Edit event
 */
function editEvent() {
    showNotification('Función de edición en desarrollo', 'info');
}

/**
 * Mark attendance for event
 */
function markAttendance(eventId) {
    showNotification('Redirigiendo a toma de asistencia...', 'info');
    setTimeout(() => {
        window.location.href = `attendance.html?event=${eventId}`;
    }, 1000);
}

/**
 * View group details
 */
function viewGroupDetails(groupId) {
    if (groupId) {
        showNotification('Redirigiendo a detalles del grupo...', 'info');
        setTimeout(() => {
            window.location.href = `groups.html?id=${groupId}`;
        }, 1000);
    }
}

/**
 * Export calendar
 */
function exportCalendar() {
    const events = calendar ? calendar.getEvents() : generateCalendarEvents();
    
    const exportData = events.map(event => ({
        'Título': event.title,
        'Fecha': event.start.toLocaleDateString('es-ES'),
        'Hora Inicio': event.start.toLocaleTimeString('es-ES'),
        'Hora Fin': event.end ? event.end.toLocaleTimeString('es-ES') : '',
        'Profesor': event.extendedProps.teacher || '',
        'Aula': event.extendedProps.room || '',
        'Tipo': getEventTypeName(event.extendedProps.type),
        'Descripción': event.extendedProps.description || ''
    }));
    
    const csv = convertToCSV(exportData);
    downloadCSV(csv, `calendario-${new Date().toISOString().split('T')[0]}.csv`);
    
    showNotification('Calendario exportado exitosamente', 'success');
}

/**
 * Update calendar colors when theme changes
 */
function updateCalendarColors() {
    if (!calendar) return;
    
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
    
    // Update calendar button colors
    const style = document.createElement('style');
    style.textContent = `
        .fc-button-primary {
            background-color: ${primaryColor} !important;
            border-color: ${primaryColor} !important;
        }
        .fc-button-primary:hover {
            background-color: ${darkenColor(primaryColor, 10)} !important;
            border-color: ${darkenColor(primaryColor, 10)} !important;
        }
    `;
    
    // Remove existing style if any
    const existingStyle = document.getElementById('calendar-theme-style');
    if (existingStyle) {
        existingStyle.remove();
    }
    
    style.id = 'calendar-theme-style';
    document.head.appendChild(style);
}

/**
 * Helper functions
 */

function getLanguageName(code) {
    const languages = {
        'english': 'Inglés',
        'french': 'Francés',
        'german': 'Alemán',
        'italian': 'Italiano',
        'portuguese': 'Portugués',
        'spanish': 'Español'
    };
    return languages[code] || code;
}

function getTeacherName(id) {
    const teachers = {
        'garcia': 'Prof. García',
        'martin': 'Prof. Martín',
        'lopez': 'Prof. López',
        'schmidt': 'Prof. Schmidt',
        'rossi': 'Prof. Rossi'
    };
    return teachers[id] || 'Sin asignar';
}

function darkenColor(hex, percent) {
    const num = parseInt(hex.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) - amt;
    const G = (num >> 8 & 0x00FF) - amt;
    const B = (num & 0x0000FF) - amt;
    return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
}

function convertToCSV(data) {
    if (!data.length) return '';
    
    const headers = Object.keys(data[0]).join(',');
    const rows = data.map(row => 
        Object.values(row).map(value => 
            typeof value === 'string' && value.includes(',') ? `"${value}"` : value
        ).join(',')
    );
    
    return [headers, ...rows].join('\n');
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Add live indicator CSS
const liveIndicatorStyle = document.createElement('style');
liveIndicatorStyle.textContent = `
    .happening-now {
        border-left: 4px solid var(--success-color) !important;
        background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 0%, transparent 100%);
    }
    
    .live-indicator {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: var(--success-color);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: var(--border-radius-sm);
        font-size: 0.7rem;
        font-weight: 600;
        animation: pulse 2s infinite;
    }
    
    .live-indicator i {
        color: #ff4444;
        margin-right: 0.25rem;
        animation: blink 1s infinite;
    }
    
    .past-event {
        opacity: 0.7;
        background: var(--background-tertiary);
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }
`;
document.head.appendChild(liveIndicatorStyle);