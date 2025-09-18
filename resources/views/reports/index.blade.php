@extends('layouts.app')

@section('title', 'Reports')

@section('content')

            <div class="content-header">
                <div class="content-title">
                    <h1>Reportes y FacturaciÃ³n</h1>
                    <p class="content-subtitle">Genera reportes acadÃ©micos y gestiona la facturaciÃ³n de profesores</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-primary" onclick="generateMonthlyReport()">
                        <i class="fas fa-file-alt"></i>
                        Generar Reporte Mensual
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportAllReports()">
                        <i class="fas fa-download"></i>
                        Exportar Todo
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">$2,450,000</div>
                                <div class="stats-label">FacturaciÃ³n Total</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+15% este mes</span>
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
                                <div class="stats-number">1,248</div>
                                <div class="stats-label">Horas Dictadas</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+8% este mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">24</div>
                                <div class="stats-label">Reportes Pendientes</div>
                                <div class="stats-change negative">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>-5 esta semana</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">89%</div>
                                <div class="stats-label">Reportes Completados</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+3% este mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Types and Filters -->
            <div class="row mb-4">
                <!-- Report Types -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-bar me-2"></i>
                                Tipos de Reportes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="report-type-card" onclick="generateAcademicReport()">
                                        <div class="report-type-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="report-type-content">
                                            <div class="report-type-title">Reporte AcadÃ©mico</div>
                                            <div class="report-type-desc">Calificaciones, asistencia y progreso por grupo/estudiante</div>
                                            <div class="report-type-meta">
                                                <span class="badge bg-primary">PDF</span>
                                                <span class="badge bg-success">Excel</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="report-type-card" onclick="generateBillingReport()">
                                        <div class="report-type-icon">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                        <div class="report-type-content">
                                            <div class="report-type-title">Reporte de FacturaciÃ³n</div>
                                            <div class="report-type-desc">Horas trabajadas y pagos por profesor</div>
                                            <div class="report-type-meta">
                                                <span class="badge bg-primary">PDF</span>
                                                <span class="badge bg-success">Excel</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="report-type-card" onclick="generateAttendanceReport()">
                                        <div class="report-type-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="report-type-content">
                                            <div class="report-type-title">Reporte de Asistencia</div>
                                            <div class="report-type-desc">EstadÃ­sticas de asistencia por grupo y perÃ­odo</div>
                                            <div class="report-type-meta">
                                                <span class="badge bg-primary">PDF</span>
                                                <span class="badge bg-success">Excel</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="report-type-card" onclick="generatePerformanceReport()">
                                        <div class="report-type-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="report-type-content">
                                            <div class="report-type-title">Reporte de Rendimiento</div>
                                            <div class="report-type-desc">AnÃ¡lisis de progreso y tendencias acadÃ©micas</div>
                                            <div class="report-type-meta">
                                                <span class="badge bg-primary">PDF</span>
                                                <span class="badge bg-success">Excel</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-filter me-2"></i>
                                Filtros de Reporte
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="form-label">PerÃ­odo</label>
                                <select class="form-select" id="periodFilter">
                                    <option value="current">Mes Actual</option>
                                    <option value="last">Mes Anterior</option>
                                    <option value="quarter">Este Trimestre</option>
                                    <option value="year">Este AÃ±o</option>
                                    <option value="custom">Personalizado</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Profesor</label>
                                <select class="form-select" id="teacherFilter">
                                    <option value="">Todos los Profesores</option>
                                    <option value="1">Prof. MarÃ­a GarcÃ­a</option>
                                    <option value="2">Prof. Carlos LÃ³pez</option>
                                    <option value="3">Prof. Ana RodrÃ­guez</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Grupo</label>
                                <select class="form-select" id="groupFilter">
                                    <option value="">Todos los Grupos</option>
                                    <option value="1">InglÃ©s BÃ¡sico A1</option>
                                    <option value="2">FrancÃ©s Intermedio B1</option>
                                    <option value="3">AlemÃ¡n Avanzado C1</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Tipo de Reporte</label>
                                <select class="form-select" id="reportTypeFilter">
                                    <option value="">Todos los Tipos</option>
                                    <option value="academic">AcadÃ©mico</option>
                                    <option value="billing">FacturaciÃ³n</option>
                                    <option value="attendance">Asistencia</option>
                                    <option value="performance">Rendimiento</option>
                                </select>
                            </div>

                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-search me-2"></i>
                                Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Overview -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                Resumen de FacturaciÃ³n - Noviembre 2024
                            </h5>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-success" onclick="approveAllBilling()">
                                    <i class="fas fa-check"></i>
                                    Aprobar Todo
                                </button>
                                <button class="btn btn-sm btn-primary" onclick="exportBillingReport()">
                                    <i class="fas fa-download"></i>
                                    Exportar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="billingTable">
                                    <thead>
                                        <tr>
                                            <th>Profesor</th>
                                            <th>Horas Regulares</th>
                                            <th>Horas 90min (1.5x)</th>
                                            <th>Horas Espera (0.75x)</th>
                                            <th>Total Horas</th>
                                            <th>Tarifa/Hora</th>
                                            <th>Total a Pagar</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="teacher-info">
                                                    <div class="teacher-name">Prof. MarÃ­a GarcÃ­a</div>
                                                    <div class="teacher-email">maria.garcia@prodocet.com</div>
                                                </div>
                                            </td>
                                            <td>18 horas</td>
                                            <td>6 horas</td>
                                            <td>2 horas</td>
                                            <td>26 horas</td>
                                            <td>$20,000</td>
                                            <td>$520,000</td>
                                            <td><span class="badge bg-warning">Pendiente</span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewBillingDetails(1)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="approveBilling(1)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="downloadBilling(1)">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="teacher-info">
                                                    <div class="teacher-name">Prof. Carlos LÃ³pez</div>
                                                    <div class="teacher-email">carlos.lopez@prodocet.com</div>
                                                </div>
                                            </td>
                                            <td>22 horas</td>
                                            <td>4 horas</td>
                                            <td>1 hora</td>
                                            <td>27 horas</td>
                                            <td>$18,000</td>
                                            <td>$486,000</td>
                                            <td><span class="badge bg-success">Aprobado</span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewBillingDetails(2)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="downloadBilling(2)">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="teacher-info">
                                                    <div class="teacher-name">Prof. Ana RodrÃ­guez</div>
                                                    <div class="teacher-email">ana.rodriguez@prodocet.com</div>
                                                </div>
                                            </td>
                                            <td>16 horas</td>
                                            <td>8 horas</td>
                                            <td>0 horas</td>
                                            <td>24 horas</td>
                                            <td>$22,000</td>
                                            <td>$528,000</td>
                                            <td><span class="badge bg-warning">Pendiente</span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewBillingDetails(3)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="approveBilling(3)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="downloadBilling(3)">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Revenue Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-line me-2"></i>
                                Ingresos por Mes
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Hours Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-pie me-2"></i>
                                DistribuciÃ³n de Horas por Idioma
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="hoursChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        
    </div>

    <!-- Bootstrap JS -->
    @endsection

@push('scripts')
    <script>

        // Initialize DataTable
        $(document).ready(function() {
            $('#billingTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[0, 'asc']]
            });

            // Initialize Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    datasets: [{
                        label: 'Ingresos (Miles $)',
                        data: [1800, 2100, 1950, 2300, 2450, 2200, 2600, 2400, 2800, 2650, 2450, 3000],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value + 'k';
                                }
                            }
                        }
                    }
                }
            });

            // Initialize Hours Chart
            const hoursCtx = document.getElementById('hoursChart').getContext('2d');
            new Chart(hoursCtx, {
                type: 'doughnut',
                data: {
                    labels: ['InglÃ©s', 'FrancÃ©s', 'AlemÃ¡n', 'Italiano'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            '#4f46e5',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });

        // Functions
        function generateMonthlyReport() {
            alert('Generando reporte mensual...');
        }

        function exportAllReports() {
            alert('Exportando todos los reportes...');
        }

        function generateAcademicReport() {
            alert('Generando reporte acadÃ©mico...');
        }

        function generateBillingReport() {
            alert('Generando reporte de facturaciÃ³n...');
        }

        function generateAttendanceReport() {
            alert('Generando reporte de asistencia...');
        }

        function generatePerformanceReport() {
            alert('Generando reporte de rendimiento...');
        }

        function applyFilters() {
            alert('Aplicando filtros...');
        }

        function approveAllBilling() {
            alert('Aprobando toda la facturaciÃ³n...');
        }

        function exportBillingReport() {
            alert('Exportando reporte de facturaciÃ³n...');
        }

        function viewBillingDetails(id) {
            alert('Ver detalles de facturaciÃ³n ' + id);
        }

        function approveBilling(id) {
            alert('Aprobando facturaciÃ³n ' + id);
        }

        function downloadBilling(id) {
            alert('Descargando facturaciÃ³n ' + id);
        }
        </script>
@endpush
