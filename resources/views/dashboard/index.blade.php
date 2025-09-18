@extends('layouts.app')

@section('title', 'Dashboard')

@section('main-section')
<div class="content-header">
    <div class="content-title">
        <h1>Dashboard</h1>
        <p class="content-subtitle">Resumen general del sistema de gestión</p>
    </div>
    <div class="content-actions">
        <button class="btn btn-primary" onclick="refreshDashboard()">
            <i class="fas fa-sync-alt"></i>
            Actualizar
        </button>
        <button class="btn btn-outline-secondary" onclick="exportDashboard()">
            <i class="fas fa-download"></i>
            Exportar
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">156</div>
                    <div class="stats-label">Total Clases</div>
                    <div class="stats-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12 este mes</span>
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
                    <div class="stats-number">42</div>
                    <div class="stats-label">Clases Restantes</div>
                    <div class="stats-change negative">
                        <i class="fas fa-arrow-down"></i>
                        <span>-8 esta semana</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">25000</div>
                    <div class="stats-label">Tarifa por Hora (Pesos)</div>
                    <div class="stats-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+$2,000 este mes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">18</div>
                    <div class="stats-label">Cursos Activos</div>
                    <div class="stats-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+3 este mes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Attendance Trends Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Tendencias de Asistencia
                </h5>
                <div class="card-tools">
                    <select class="form-select form-select-sm" onchange="updateAttendanceChart(this.value)">
                        <option value="week">Esta Semana</option>
                        <option value="month" selected>Este Mes</option>
                        <option value="quarter">Este Trimestre</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Grade Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-pie me-2"></i>
                    Distribución de Calificaciones
                </h5>
            </div>
            <div class="card-body">
                <canvas id="gradeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Alerts and Quick Overview -->
<div class="row mb-4">
    <!-- Alerts -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alertas Importantes
                </h5>
            </div>
            <div class="card-body">
                <div class="alert-item">
                    <div class="alert-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">5 sesiones restantes</div>
                        <div class="alert-text">Grupo Inglés Básico A1</div>
                        <div class="alert-actions">
                            <button class="btn btn-sm btn-primary">Renovar</button>
                        </div>
                    </div>
                </div>

                <div class="alert-item">
                    <div class="alert-icon info">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Clase reprogramada</div>
                        <div class="alert-text">Francés B2 - Viernes 15:00</div>
                        <div class="alert-actions">
                            <button class="btn btn-sm btn-outline-primary">Ver detalles</button>
                        </div>
                    </div>
                </div>

                <div class="alert-item">
                    <div class="alert-icon success">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Nuevos estudiantes</div>
                        <div class="alert-text">3 registros esta semana</div>
                        <div class="alert-actions">
                            <button class="btn btn-sm btn-success">Asignar grupos</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-history me-2"></i>
                    Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="recentActivityTable">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>10:30</td>
                                <td>Prof. García</td>
                                <td><span class="badge bg-success">Asistencia registrada</span></td>
                                <td>Inglés Intermedio B1</td>
                            </tr>
                            <tr>
                                <td>09:45</td>
                                <td>Ana Martínez</td>
                                <td><span class="badge bg-info">Nuevo registro</span></td>
                                <td>Estudiante - Francés A2</td>
                            </tr>
                            <tr>
                                <td>09:15</td>
                                <td>Administrador</td>
                                <td><span class="badge bg-warning">Clase cancelada</span></td>
                                <td>Alemán B2 - Profesor enfermo</td>
                            </tr>
                            <tr>
                                <td>08:30</td>
                                <td>Prof. López</td>
                                <td><span class="badge bg-primary">Calificaciones subidas</span></td>
                                <td>Italiano A1 - 8 estudiantes</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar and Quick Stats -->
<div class="row">
    <!-- Mini Calendar -->
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Calendario de Clases
                </h5>
                <div class="card-tools">
                    <a href="{{ route('calendar') }}" class="btn btn-sm btn-primary">
                        Ver completo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="miniCalendar"></div>
            </div>
        </div>
    </div>

    <!-- Quick Stats and Actions -->
    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="quick-action-card" onclick="openNewGroupModal()">
                            <div class="quick-action-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Nuevo Grupo</div>
                                <div class="quick-action-desc">Crear un nuevo grupo de clases</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="quick-action-card" onclick="openStudentModal()">
                            <div class="quick-action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Registrar Estudiante</div>
                                <div class="quick-action-desc">Añadir nuevo estudiante</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="quick-action-card" onclick="markAttendance()">
                            <div class="quick-action-icon">
                                <i class="fas fa-check-square"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Tomar Asistencia</div>
                                <div class="quick-action-desc">Registrar asistencia de hoy</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="quick-action-card" onclick="generateReport()">
                            <div class="quick-action-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">Generar Reporte</div>
                                <div class="quick-action-desc">Crear reporte mensual</div>
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
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
<script>
// Dashboard specific functions
function refreshDashboard() {
    // Add refresh logic here
    location.reload();
}

function exportDashboard() {
    // Add export logic here
    alert('Función de exportación en desarrollo');
}

function openNewGroupModal() {
    // Add new group modal logic here
    window.location.href = "{{ route('groups') }}";
}

function openStudentModal() {
    // Add student modal logic here
    window.location.href = "{{ route('students.index') }}";
}

function markAttendance() {
    // Add attendance logic here
    window.location.href = "{{ route('attendance') }}";
}

function generateReport() {
    // Add report generation logic here
    window.location.href = "{{ route('reports') }}";
}
</script>
@endpush
