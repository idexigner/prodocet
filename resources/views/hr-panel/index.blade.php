@extends('layouts.app')

@section('title', 'Hr panel')

@push('styles')

<style>
        .employee-card {
            background: #ffffff;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
            transition: var(--transition-normal);
        }
        
        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .employee-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .progress-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            margin: 0 auto;
        }
        
        .progress-excellent {
            background: conic-gradient(var(--success-color) 0deg 324deg, #e5e7eb 324deg 360deg);
        }
        
        .progress-good {
            background: conic-gradient(var(--secondary-color) 0deg 288deg, #e5e7eb 288deg 360deg);
        }
        
        .progress-average {
            background: conic-gradient(var(--warning-color) 0deg 252deg, #e5e7eb 252deg 360deg);
        }
        
        .progress-poor {
            background: conic-gradient(var(--error-color) 0deg 216deg, #e5e7eb 216deg 360deg);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        .justification-modal {
            max-width: 600px;
        }
        
        .class-schedule {
            background: var(--background-secondary);
            border-radius: var(--border-radius-md);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .class-time {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .class-progress {
            margin-top: 0.5rem;
        }
    </style>

@endpush

@section('content')

            <div class="content-header">
                <div class="content-title">
                    <h1>Panel RRHH</h1>
                    <p class="content-subtitle">GestiÃ³n de empleados y seguimiento de asistencia</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-primary" onclick="exportEmployeeReport()">
                        <i class="fas fa-download"></i>
                        Exportar Reporte
                    </button>
                    <button class="btn btn-outline-secondary" onclick="addNewEmployee()">
                        <i class="fas fa-user-plus"></i>
                        Agregar Empleado
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">24</div>
                                <div class="stats-label">Empleados Activos</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+2 este mes</span>
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
                                <div class="stats-number">92%</div>
                                <div class="stats-label">Asistencia Promedio</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+3% este mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">85.7</div>
                                <div class="stats-label">Promedio General</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+2.1 este mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">3</div>
                                <div class="stats-label">Empleados en Riesgo</div>
                                <div class="stats-change negative">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+1 este mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Departamento</label>
                                <select class="form-select" id="departmentFilter">
                                    <option value="">Todos los departamentos</option>
                                    <option value="it">TecnologÃ­a</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="sales">Ventas</option>
                                    <option value="hr">Recursos Humanos</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">Todos los estados</option>
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                    <option value="pending">Pendiente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Empleado</label>
                                <input type="text" class="form-control" id="employeeSearch" placeholder="Buscar empleado...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-primary" onclick="applyFilters()">
                                        <i class="fas fa-search"></i>
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employees Grid -->
            <div class="row">
                <!-- Employee Card 1 -->
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="employee-card">
                        <div class="employee-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">MarÃ­a GonzÃ¡lez</h5>
                                    <p class="mb-0">Desarrolladora Frontend</p>
                                    <small>ID: EMP001</small>
                                </div>
                                <span class="status-badge status-active">Activo</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="progress-circle progress-excellent">90%</div>
                                    <small class="text-muted">Asistencia</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-good">85%</div>
                                    <small class="text-muted">Rendimiento</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-excellent">92%</div>
                                    <small class="text-muted">SatisfacciÃ³n</small>
                                </div>
                            </div>
                            
                            <div class="class-schedule">
                                <h6><i class="fas fa-calendar me-2"></i>Clases Asignadas</h6>
                                <div class="class-time">InglÃ©s BÃ¡sico A1 - Lunes 09:00</div>
                                <div class="class-progress">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: 75%"></div>
                                    </div>
                                    <small class="text-muted">75% completado</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewEmployeeDetails(1)">
                                    <i class="fas fa-eye"></i>
                                    Ver Detalles
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="justifyAbsence(1)">
                                    <i class="fas fa-edit"></i>
                                    Justificar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Card 2 -->
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="employee-card">
                        <div class="employee-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">Carlos PÃ©rez</h5>
                                    <p class="mb-0">Analista de Datos</p>
                                    <small>ID: EMP002</small>
                                </div>
                                <span class="status-badge status-active">Activo</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="progress-circle progress-average">78%</div>
                                    <small class="text-muted">Asistencia</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-good">82%</div>
                                    <small class="text-muted">Rendimiento</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-average">79%</div>
                                    <small class="text-muted">SatisfacciÃ³n</small>
                                </div>
                            </div>
                            
                            <div class="class-schedule">
                                <h6><i class="fas fa-calendar me-2"></i>Clases Asignadas</h6>
                                <div class="class-time">InglÃ©s Intermedio B1 - MiÃ©rcoles 11:00</div>
                                <div class="class-progress">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: 60%"></div>
                                    </div>
                                    <small class="text-muted">60% completado</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewEmployeeDetails(2)">
                                    <i class="fas fa-eye"></i>
                                    Ver Detalles
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="justifyAbsence(2)">
                                    <i class="fas fa-edit"></i>
                                    Justificar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Card 3 -->
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="employee-card">
                        <div class="employee-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">Ana RodrÃ­guez</h5>
                                    <p class="mb-0">DiseÃ±adora UX/UI</p>
                                    <small>ID: EMP003</small>
                                </div>
                                <span class="status-badge status-active">Activo</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="progress-circle progress-excellent">95%</div>
                                    <small class="text-muted">Asistencia</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-excellent">88%</div>
                                    <small class="text-muted">Rendimiento</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-excellent">94%</div>
                                    <small class="text-muted">SatisfacciÃ³n</small>
                                </div>
                            </div>
                            
                            <div class="class-schedule">
                                <h6><i class="fas fa-calendar me-2"></i>Clases Asignadas</h6>
                                <div class="class-time">FrancÃ©s BÃ¡sico A2 - Viernes 15:00</div>
                                <div class="class-progress">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: 85%"></div>
                                    </div>
                                    <small class="text-muted">85% completado</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewEmployeeDetails(3)">
                                    <i class="fas fa-eye"></i>
                                    Ver Detalles
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="justifyAbsence(3)">
                                    <i class="fas fa-edit"></i>
                                    Justificar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Card 4 -->
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="employee-card">
                        <div class="employee-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">Luis MartÃ­nez</h5>
                                    <p class="mb-0">Desarrollador Backend</p>
                                    <small>ID: EMP004</small>
                                </div>
                                <span class="status-badge status-pending">Pendiente</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="progress-circle progress-poor">65%</div>
                                    <small class="text-muted">Asistencia</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-average">75%</div>
                                    <small class="text-muted">Rendimiento</small>
                                </div>
                                <div class="col-4">
                                    <div class="progress-circle progress-poor">68%</div>
                                    <small class="text-muted">SatisfacciÃ³n</small>
                                </div>
                            </div>
                            
                            <div class="class-schedule">
                                <h6><i class="fas fa-calendar me-2"></i>Clases Asignadas</h6>
                                <div class="class-time">AlemÃ¡n Intermedio B2 - Martes 13:00</div>
                                <div class="class-progress">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: 45%"></div>
                                    </div>
                                    <small class="text-muted">45% completado</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewEmployeeDetails(4)">
                                    <i class="fas fa-eye"></i>
                                    Ver Detalles
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="justifyAbsence(4)">
                                    <i class="fas fa-edit"></i>
                                    Justificar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Employee Table -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-table me-2"></i>
                        Detalle de Empleados
                    </h5>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportDetailedReport()">
                            <i class="fas fa-download"></i>
                            Exportar Detalle
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="employeeTable">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Departamento</th>
                                    <th>Clases</th>
                                    <th>Asistencia</th>
                                    <th>Promedio</th>
                                    <th>Ãšltima Clase</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm me-2" title="MarÃ­a GonzÃ¡lez"></div>
                                            <div>
                                                <div class="fw-bold">MarÃ­a GonzÃ¡lez</div>
                                                <small class="text-muted">EMP001</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>TecnologÃ­a</td>
                                    <td>InglÃ©s BÃ¡sico A1</td>
                                    <td>90%</td>
                                    <td>85.3</td>
                                    <td>15 Nov 2024</td>
                                    <td><span class="status-badge status-active">Activo</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewEmployeeDetails(1)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="justifyAbsence(1)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" onclick="downloadReport(1)">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm me-2" title="Carlos PÃ©rez"></div>
                                            <div>
                                                <div class="fw-bold">Carlos PÃ©rez</div>
                                                <small class="text-muted">EMP002</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>TecnologÃ­a</td>
                                    <td>InglÃ©s Intermedio B1</td>
                                    <td>78%</td>
                                    <td>78.8</td>
                                    <td>14 Nov 2024</td>
                                    <td><span class="status-badge status-active">Activo</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewEmployeeDetails(2)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="justifyAbsence(2)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" onclick="downloadReport(2)">
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

    <!-- Justification Modal -->
    <div class="modal fade" id="justificationModal" tabindex="-1">
        <div class="modal-dialog justification-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Justificar Ausencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="justificationForm">
                        <div class="mb-3">
                            <label class="form-label">Empleado</label>
                            <input type="text" class="form-control" id="employeeName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Ausencia</label>
                            <input type="date" class="form-control" id="absenceDate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de JustificaciÃ³n</label>
                            <select class="form-select" id="justificationType" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="medical">MÃ©dica</option>
                                <option value="personal">Personal</option>
                                <option value="work">Trabajo</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">DescripciÃ³n</label>
                            <textarea class="form-control" id="justificationDescription" rows="3" placeholder="Describe la razÃ³n de la ausencia..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Documento de Soporte</label>
                            <input type="file" class="form-control" id="supportDocument">
                            <small class="text-muted">Opcional: Sube un documento que respalde la justificaciÃ³n</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="submitJustification()">Guardar JustificaciÃ³n</button>
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
            $('#employeeTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[0, 'asc']]
            });
        });

        // Functions
        function applyFilters() {
            const department = document.getElementById('departmentFilter').value;
            const status = document.getElementById('statusFilter').value;
            const employee = document.getElementById('employeeSearch').value;
            
            console.log('Applying filters:', { department, status, employee });
        }

        function addNewEmployee() {
            alert('Agregando nuevo empleado...');
        }

        function viewEmployeeDetails(id) {
            alert(`Viendo detalles del empleado ${id}...`);
        }

        function justifyAbsence(id) {
            // Set employee name in modal
            const employeeNames = {
                1: 'MarÃ­a GonzÃ¡lez',
                2: 'Carlos PÃ©rez',
                3: 'Ana RodrÃ­guez',
                4: 'Luis MartÃ­nez'
            };
            
            document.getElementById('employeeName').value = employeeNames[id] || 'Empleado';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('justificationModal'));
            modal.show();
        }

        function submitJustification() {
            const form = document.getElementById('justificationForm');
            if (form.checkValidity()) {
                alert('JustificaciÃ³n guardada exitosamente');
                bootstrap.Modal.getInstance(document.getElementById('justificationModal')).hide();
                form.reset();
            } else {
                form.reportValidity();
            }
        }

        function exportEmployeeReport() {
            alert('Exportando reporte de empleados...');
        }

        function exportDetailedReport() {
            alert('Exportando reporte detallado...');
        }

        function downloadReport(id) {
            alert(`Descargando reporte del empleado ${id}...`);
        }
        </script>
@endpush
