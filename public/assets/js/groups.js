/**
 * PRODOCET LMS - Groups Management JavaScript
 * Author: Assistant
 * Description: JavaScript functionality for groups management page
 */

// Global variables for groups management
let groupsTable = null;
let currentView = 'table';
let groupsData = [];

// Sample groups data (in a real app, this would come from API)
const SAMPLE_GROUPS = [
    {
        id: 'GRP001',
        name: 'Inglés Básico A1 - Mañana',
        language: 'Inglés',
        level: 'A1',
        teacher: 'Prof. García',
        teacherId: 'garcia',
        students: 8,
        maxStudents: 10,
        schedule: 'Lunes, Miércoles 09:00',
        status: 'Activo',
        hourlyRate: 25000,
        totalSessions: 20,
        completedSessions: 12,
        startDate: '2024-01-15',
        endDate: '2024-04-15',
        description: 'Grupo de inglés para principiantes absolutos',
        allowRotation: true,
        autoEnroll: false
    },
    {
        id: 'GRP002',
        name: 'Francés Intermedio B1',
        language: 'Francés',
        level: 'B1',
        teacher: 'Prof. Martín',
        teacherId: 'martin',
        students: 6,
        maxStudents: 8,
        schedule: 'Martes, Jueves 14:00',
        status: 'Activo',
        hourlyRate: 30000,
        totalSessions: 24,
        completedSessions: 15,
        startDate: '2024-01-10',
        endDate: '2024-05-10',
        description: 'Grupo intermedio con enfoque en conversación',
        allowRotation: false,
        autoEnroll: true
    },
    {
        id: 'GRP003',
        name: 'Alemán Avanzado C1',
        language: 'Alemán',
        level: 'C1',
        teacher: 'Prof. Schmidt',
        teacherId: 'schmidt',
        students: 4,
        maxStudents: 6,
        schedule: 'Viernes 16:00',
        status: 'Activo',
        hourlyRate: 40000,
        totalSessions: 16,
        completedSessions: 8,
        startDate: '2024-02-01',
        endDate: '2024-06-01',
        description: 'Preparación para certificación C1',
        allowRotation: false,
        autoEnroll: false
    },
    {
        id: 'GRP004',
        name: 'Italiano Elemental A2',
        language: 'Italiano',
        level: 'A2',
        teacher: 'Prof. Rossi',
        teacherId: 'rossi',
        students: 7,
        maxStudents: 10,
        schedule: 'Lunes, Miércoles 11:00',
        status: 'Activo',
        hourlyRate: 28000,
        totalSessions: 20,
        completedSessions: 5,
        startDate: '2024-02-15',
        endDate: '2024-06-15',
        description: 'Continuación del nivel A1',
        allowRotation: true,
        autoEnroll: true
    },
    {
        id: 'GRP005',
        name: 'Inglés Intermedio B2 - Tarde',
        language: 'Inglés',
        level: 'B2',
        teacher: 'Prof. López',
        teacherId: 'lopez',
        students: 9,
        maxStudents: 12,
        schedule: 'Martes, Jueves 18:00',
        status: 'Activo',
        hourlyRate: 32000,
        totalSessions: 22,
        completedSessions: 18,
        startDate: '2024-01-08',
        endDate: '2024-05-08',
        description: 'Preparación para First Certificate',
        allowRotation: false,
        autoEnroll: true
    },
    {
        id: 'GRP006',
        name: 'Francés Básico A1 - Fin de Semana',
        language: 'Francés',
        level: 'A1',
        teacher: 'Prof. Martín',
        teacherId: 'martin',
        students: 5,
        maxStudents: 8,
        schedule: 'Sábado 10:00',
        status: 'Completado',
        hourlyRate: 25000,
        totalSessions: 16,
        completedSessions: 16,
        startDate: '2023-10-01',
        endDate: '2024-01-31',
        description: 'Curso intensivo de fin de semana',
        allowRotation: false,
        autoEnroll: false
    }
];

// Initialize groups page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // if (window.location.pathname.includes('groups.html')) {
        initializeGroupsPage();
    // }
});

/**
 * Initialize the groups management page
 */
function initializeGroupsPage() {
    loadGroupsData();
    initializeGroupsTable();
    setupGroupsEventListeners();
    
    // Check if we need to open new group modal
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'new') {
        setTimeout(() => openNewGroupModal(), 500);
    }
}

/**
 * Load groups data (simulate API call)
 */
function loadGroupsData() {
    groupsData = [...SAMPLE_GROUPS];
    populateGroupsTable();
    populateGroupsCards();
}

/**
 * Initialize DataTables for groups
 */
function initializeGroupsTable() {
    if ($.fn.DataTable) {
        groupsTable = $('#groupsTable').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            columnDefs: [
                { targets: [9], orderable: false } // Actions column
            ],
            pageLength: 10,
            order: [[0, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }
}

/**
 * Populate groups table with data
 */
function populateGroupsTable() {
    const tableBody = document.querySelector('#groupsTable tbody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    groupsData.forEach(group => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${group.id}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="group-avatar me-2" title="${group.name}">
                        ${group.language.charAt(0)}
                    </div>
                    <div>
                        <div class="fw-bold">${group.name}</div>
                        <small class="text-muted">${group.description || 'Sin descripción'}</small>
                    </div>
                </div>
            </td>
            <td>
                <span class="language-badge language-${group.language.toLowerCase()}">
                    ${group.language}
                </span>
            </td>
            <td>
                <span class="level-badge level-${group.level}">
                    ${group.level}
                </span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="teacher-avatar me-2" title="${group.teacher}">
                        ${group.teacher.charAt(group.teacher.lastIndexOf(' ') + 1)}
                    </div>
                    <span>${group.teacher}</span>
                </div>
            </td>
            <td>
                <div class="student-count">
                    <span class="current">${group.students}</span>
                    <span class="separator">/</span>
                    <span class="max">${group.maxStudents}</span>
                </div>
                <div class="progress mt-1" style="height: 4px;">
                    <div class="progress-bar" style="width: ${(group.students / group.maxStudents) * 100}%"></div>
                </div>
            </td>
            <td>
                <div class="schedule-info">
                    <div class="schedule-time">${group.schedule}</div>
                </div>
            </td>
            <td>
                <span class="badge ${getStatusBadgeClass(group.status)}">
                    ${group.status}
                </span>
            </td>
            <td>
                <div class="rate-info">
                    <span class="rate">$${group.hourlyRate.toLocaleString()}</span>
                    <small class="text-muted">/hora</small>
                </div>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewGroupDetails('${group.id}')" 
                            title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="editGroup('${group.id}')" 
                            title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="manageStudents('${group.id}')" 
                            title="Gestionar estudiantes">
                        <i class="fas fa-users"></i>
                    </button>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                data-bs-toggle="dropdown" title="Más opciones">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" onclick="duplicateGroup('${group.id}')">
                                <i class="fas fa-copy me-2"></i>Duplicar
                            </a></li>
                            <li><a class="dropdown-item" onclick="archiveGroup('${group.id}')">
                                <i class="fas fa-archive me-2"></i>Archivar
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" onclick="deleteGroup('${group.id}')">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </a></li>
                        </ul>
                    </div>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
    
    // Refresh DataTable if it exists
    if (groupsTable) {
        groupsTable.clear().rows.add($(tableBody).find('tr')).draw();
    }
}

/**
 * Populate groups cards view
 */
function populateGroupsCards() {
    const cardsContainer = document.getElementById('groupsCards');
    if (!cardsContainer) return;
    
    cardsContainer.innerHTML = '';
    
    groupsData.forEach(group => {
        const card = document.createElement('div');
        card.className = 'col-lg-4 col-md-6 mb-4';
        
        const sessionsProgress = (group.completedSessions / group.totalSessions) * 100;
        const studentsProgress = (group.students / group.maxStudents) * 100;
        
        card.innerHTML = `
            <div class="card group-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="group-header-info">
                            <h6 class="card-title mb-1">${group.name}</h6>
                            <div class="group-badges">
                                <span class="language-badge language-${group.language.toLowerCase()}">${group.language}</span>
                                <span class="level-badge level-${group.level}">${group.level}</span>
                            </div>
                        </div>
                        <span class="badge ${getStatusBadgeClass(group.status)}">${group.status}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="group-info mb-3">
                        <div class="info-item">
                            <i class="fas fa-chalkboard-teacher text-primary"></i>
                            <span>${group.teacher}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock text-info"></i>
                            <span>${group.schedule}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-dollar-sign text-success"></i>
                            <span>$${group.hourlyRate.toLocaleString()}/hora</span>
                        </div>
                    </div>
                    
                    <div class="progress-section mb-3">
                        <div class="progress-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Estudiantes</small>
                                <small class="text-muted">${group.students}/${group.maxStudents}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: ${studentsProgress}%"></div>
                            </div>
                        </div>
                        
                        <div class="progress-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Sesiones</small>
                                <small class="text-muted">${group.completedSessions}/${group.totalSessions}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: ${sessionsProgress}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    ${group.description ? `<p class="card-text text-muted small">${group.description}</p>` : ''}
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100" role="group">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewGroupDetails('${group.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="editGroup('${group.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="manageStudents('${group.id}')">
                            <i class="fas fa-users"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" onclick="duplicateGroup('${group.id}')">
                                <i class="fas fa-copy me-2"></i>Duplicar
                            </a></li>
                            <li><a class="dropdown-item" onclick="archiveGroup('${group.id}')">
                                <i class="fas fa-archive me-2"></i>Archivar
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" onclick="deleteGroup('${group.id}')">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
        
        cardsContainer.appendChild(card);
    });
}

/**
 * Setup event listeners for groups page
 */
function setupGroupsEventListeners() {
    // Filter change events
    const filters = ['languageFilter', 'levelFilter', 'statusFilter'];
    filters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.addEventListener('change', applyFilters);
        }
    });
    
    // Group code auto-generation
    const groupNameField = document.getElementById('groupName');
    const groupCodeField = document.getElementById('groupCode');
    
    if (groupNameField && groupCodeField) {
        groupNameField.addEventListener('input', function() {
            if (!groupCodeField.value) {
                const code = generateGroupCode(this.value);
                groupCodeField.value = code;
            }
        });
    }
}

/**
 * Get appropriate badge class for status
 */
function getStatusBadgeClass(status) {
    const statusClasses = {
        'Activo': 'bg-success',
        'Inactivo': 'bg-secondary',
        'Completado': 'bg-primary',
        'Suspendido': 'bg-warning'
    };
    return statusClasses[status] || 'bg-secondary';
}

/**
 * Generate group code from name
 */
function generateGroupCode(name) {
    if (!name) return '';
    
    // Extract language and level
    const words = name.split(' ');
    const language = words[0] ? words[0].substring(0, 3).toUpperCase() : 'GRP';
    const level = words.find(word => /^[ABC][12]?$/.test(word)) || '';
    const number = String(groupsData.length + 1).padStart(3, '0');
    
    return `${language}${level}${number}`;
}

/**
 * Switch between table and cards view
 */
function switchView(view) {
    currentView = view;
    
    const tableView = document.getElementById('tableView');
    const cardsView = document.getElementById('cardsView');
    const viewButtons = document.querySelectorAll('.card-tools .btn-group button');
    
    if (view === 'table') {
        tableView?.classList.remove('d-none');
        cardsView?.classList.add('d-none');
    } else {
        tableView?.classList.add('d-none');
        cardsView?.classList.remove('d-none');
    }
    
    // Update active button
    viewButtons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('button').classList.add('active');
}

/**
 * Apply filters to groups data
 */
function applyFilters() {
    const languageFilter = document.getElementById('languageFilter')?.value.toLowerCase();
    const levelFilter = document.getElementById('levelFilter')?.value;
    const statusFilter = document.getElementById('statusFilter')?.value;
    
    let filteredData = [...SAMPLE_GROUPS];
    
    if (languageFilter) {
        filteredData = filteredData.filter(group => 
            group.language.toLowerCase().includes(languageFilter)
        );
    }
    
    if (levelFilter) {
        filteredData = filteredData.filter(group => group.level === levelFilter);
    }
    
    if (statusFilter) {
        const statusMap = {
            'active': 'Activo',
            'inactive': 'Inactivo',
            'completed': 'Completado',
            'suspended': 'Suspendido'
        };
        filteredData = filteredData.filter(group => group.status === statusMap[statusFilter]);
    }
    
    groupsData = filteredData;
    populateGroupsTable();
    populateGroupsCards();
    
    showNotification(`Filtros aplicados. Mostrando ${filteredData.length} grupo(s).`, 'info');
}

/**
 * Open new group modal
 */
function openNewGroupModal() {
    const modal = new bootstrap.Modal(document.getElementById('newGroupModal'));
    modal.show();
    
    // Reset form
    document.getElementById('newGroupForm').reset();
    
    // Focus on first field
    setTimeout(() => {
        document.getElementById('groupName')?.focus();
    }, 300);
}

/**
 * Save new group
 */
function saveGroup() {
    const form = document.getElementById('newGroupForm');
    const formData = new FormData(form);
    
    // Validate required fields
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // Create group object
    const newGroup = {
        id: formData.get('groupCode') || generateGroupCode(formData.get('groupName')),
        name: formData.get('groupName'),
        language: getLanguageName(formData.get('language')),
        level: formData.get('level'),
        teacher: getTeacherName(formData.get('teacher')),
        teacherId: formData.get('teacher'),
        students: 0,
        maxStudents: parseInt(formData.get('maxStudents')) || 10,
        schedule: formatSchedule(formData.get('scheduleDay'), formData.get('scheduleTime')),
        status: 'Activo',
        hourlyRate: parseFloat(formData.get('hourlyRate')) || 0,
        totalSessions: parseInt(formData.get('totalSessions')) || 20,
        completedSessions: 0,
        startDate: new Date().toISOString().split('T')[0],
        endDate: '',
        description: formData.get('description') || '',
        allowRotation: formData.has('allowRotation'),
        autoEnroll: formData.has('autoEnroll')
    };
    
    // Add to groups data
    groupsData.unshift(newGroup);
    SAMPLE_GROUPS.unshift(newGroup);
    
    // Refresh views
    populateGroupsTable();
    populateGroupsCards();
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('newGroupModal'));
    modal.hide();
    
    // Show success message
    showNotification('Grupo creado exitosamente', 'success');
    
    // Clear auto-save data
    localStorage.removeItem('autosave_newGroupForm');
}

/**
 * View group details
 */
function viewGroupDetails(groupId) {
    const group = groupsData.find(g => g.id === groupId);
    if (!group) return;
    
    const modal = new bootstrap.Modal(document.getElementById('groupDetailsModal'));
    const content = document.getElementById('groupDetailsContent');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-8">
                <div class="group-details-main">
                    <div class="group-header mb-4">
                        <h4>${group.name}</h4>
                        <div class="group-badges">
                            <span class="language-badge language-${group.language.toLowerCase()}">${group.language}</span>
                            <span class="level-badge level-${group.level}">${group.level}</span>
                            <span class="badge ${getStatusBadgeClass(group.status)}">${group.status}</span>
                        </div>
                    </div>
                    
                    <div class="group-info-grid">
                        <div class="info-section">
                            <h6>Información Básica</h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Código del Grupo:</label>
                                    <span>${group.id}</span>
                                </div>
                                <div class="info-item">
                                    <label>Profesor Asignado:</label>
                                    <span>${group.teacher}</span>
                                </div>
                                <div class="info-item">
                                    <label>Horario:</label>
                                    <span>${group.schedule}</span>
                                </div>
                                <div class="info-item">
                                    <label>Tarifa por Hora:</label>
                                    <span>$${group.hourlyRate.toLocaleString()}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-section">
                            <h6>Progreso y Estadísticas</h6>
                            <div class="progress-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Estudiantes Inscritos</div>
                                    <div class="stat-value">${group.students} / ${group.maxStudents}</div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: ${(group.students / group.maxStudents) * 100}%"></div>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-label">Sesiones Completadas</div>
                                    <div class="stat-value">${group.completedSessions} / ${group.totalSessions}</div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: ${(group.completedSessions / group.totalSessions) * 100}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${group.description ? `
                        <div class="info-section">
                            <h6>Descripción</h6>
                            <p>${group.description}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="group-sidebar">
                    <div class="sidebar-section">
                        <h6>Acciones Rápidas</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="manageStudents('${group.id}')">
                                <i class="fas fa-users me-2"></i>Gestionar Estudiantes
                            </button>
                            <button class="btn btn-outline-success" onclick="viewAttendance('${group.id}')">
                                <i class="fas fa-check-circle me-2"></i>Ver Asistencia
                            </button>
                            <button class="btn btn-outline-info" onclick="viewSchedule('${group.id}')">
                                <i class="fas fa-calendar me-2"></i>Ver Horario
                            </button>
                            <button class="btn btn-outline-warning" onclick="generateReport('${group.id}')">
                                <i class="fas fa-file-alt me-2"></i>Generar Reporte
                            </button>
                        </div>
                    </div>
                    
                    <div class="sidebar-section">
                        <h6>Configuración</h6>
                        <div class="config-items">
                            <div class="config-item">
                                <span>Rotación de Estudiantes:</span>
                                <span class="badge ${group.allowRotation ? 'bg-success' : 'bg-secondary'}">
                                    ${group.allowRotation ? 'Habilitada' : 'Deshabilitada'}
                                </span>
                            </div>
                            <div class="config-item">
                                <span>Inscripción Automática:</span>
                                <span class="badge ${group.autoEnroll ? 'bg-success' : 'bg-secondary'}">
                                    ${group.autoEnroll ? 'Habilitada' : 'Deshabilitada'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modal.show();
}

/**
 * Edit group
 */
function editGroup(groupId) {
    showNotification('Función de edición en desarrollo', 'info');
}

/**
 * Manage students in group
 */
function manageStudents(groupId) {
    showNotification('Redirigiendo a gestión de estudiantes...', 'info');
    setTimeout(() => {
        window.location.href = `students.html?group=${groupId}`;
    }, 1000);
}

/**
 * View group attendance
 */
function viewAttendance(groupId) {
    showNotification('Redirigiendo a asistencia del grupo...', 'info');
    setTimeout(() => {
        window.location.href = `attendance.html?group=${groupId}`;
    }, 1000);
}

/**
 * View group schedule
 */
function viewSchedule(groupId) {
    showNotification('Redirigiendo al calendario del grupo...', 'info');
    setTimeout(() => {
        window.location.href = `calendar.html?group=${groupId}`;
    }, 1000);
}

/**
 * Generate group report
 */
function generateReport(groupId) {
    showNotification('Generando reporte del grupo...', 'info');
    setTimeout(() => {
        window.location.href = `reports.html?type=group&id=${groupId}`;
    }, 1000);
}

/**
 * Duplicate group
 */
function duplicateGroup(groupId) {
    const group = groupsData.find(g => g.id === groupId);
    if (!group) return;
    
    const newGroup = {
        ...group,
        id: generateGroupCode(group.name + ' (Copia)'),
        name: group.name + ' (Copia)',
        students: 0,
        completedSessions: 0,
        status: 'Inactivo'
    };
    
    groupsData.unshift(newGroup);
    SAMPLE_GROUPS.unshift(newGroup);
    
    populateGroupsTable();
    populateGroupsCards();
    
    showNotification('Grupo duplicado exitosamente', 'success');
}

/**
 * Archive group
 */
function archiveGroup(groupId) {
    const group = groupsData.find(g => g.id === groupId);
    if (group) {
        group.status = 'Completado';
        populateGroupsTable();
        populateGroupsCards();
        showNotification('Grupo archivado exitosamente', 'success');
    }
}

/**
 * Delete group
 */
function deleteGroup(groupId) {
    if (confirm('¿Estás seguro de que quieres eliminar este grupo? Esta acción no se puede deshacer.')) {
        const index = groupsData.findIndex(g => g.id === groupId);
        if (index > -1) {
            groupsData.splice(index, 1);
            
            const sampleIndex = SAMPLE_GROUPS.findIndex(g => g.id === groupId);
            if (sampleIndex > -1) {
                SAMPLE_GROUPS.splice(sampleIndex, 1);
            }
            
            populateGroupsTable();
            populateGroupsCards();
            showNotification('Grupo eliminado exitosamente', 'success');
        }
    }
}

/**
 * Export groups data
 */
function exportGroups() {
    const exportData = groupsData.map(group => ({
        'ID': group.id,
        'Nombre': group.name,
        'Idioma': group.language,
        'Nivel': group.level,
        'Profesor': group.teacher,
        'Estudiantes': `${group.students}/${group.maxStudents}`,
        'Horario': group.schedule,
        'Estado': group.status,
        'Tarifa': `$${group.hourlyRate.toLocaleString()}`,
        'Sesiones': `${group.completedSessions}/${group.totalSessions}`
    }));
    
    const csv = convertToCSV(exportData);
    downloadCSV(csv, `grupos-${new Date().toISOString().split('T')[0]}.csv`);
    
    showNotification('Datos de grupos exportados exitosamente', 'success');
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

function formatSchedule(day, time) {
    const days = {
        'monday': 'Lunes',
        'tuesday': 'Martes',
        'wednesday': 'Miércoles',
        'thursday': 'Jueves',
        'friday': 'Viernes',
        'saturday': 'Sábado',
        'sunday': 'Domingo'
    };
    
    if (!day || !time) return 'Por definir';
    
    return `${days[day]} ${time}`;
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