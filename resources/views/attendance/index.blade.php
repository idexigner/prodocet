@extends('layouts.app')

@section('title', 'Attendance')

@push('styles')
<style>
        .attendance-card {
            background: #ffffff;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }
        
        .attendance-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .grade-input {
            width: 60px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 0.25rem;
        }
        
        .grade-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-lightest);
        }
        
        .attendance-status {
            padding: 0.25rem 0.75rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-present {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .status-absent {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .status-late {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        .status-justified {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info-color);
        }
        
        .performance-chart {
            height: 300px;
            margin: 1rem 0;
        }
        
        .skill-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .skill-ca { background: var(--primary-color); }
        .skill-cl { background: var(--secondary-color); }
        .skill-ieo { background: var(--accent-color); }
        .skill-ee { background: var(--info-color); }
    </style>
@endpush

@section('main-section')

            <div class="content-header">
                <div class="content-title">
                    <h1>Asistencia y Rendimiento</h1>
                    <p class="content-subtitle">Gestiona la asistencia y calificaciones de los estudiantes</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-primary" onclick="exportReport()">
                        <i class="fas fa-download"></i>
                        Exportar Reporte
                    </button>
                    <button class="btn btn-outline-secondary" onclick="bulkEdit()">
                        <i class="fas fa-edit"></i>
                        EdiciÃ³n Masiva
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
                                <div class="stats-number">156</div>
                                <div class="stats-label">Total Estudiantes</div>
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
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">89%</div>
                                <div class="stats-label">Asistencia Promedio</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+2% este mes</span>
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
                                <div class="stats-number">82.5</div>
                                <div class="stats-label">Promedio General</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+1.2 este mes</span>
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
                                <div class="stats-number">8</div>
                                <div class="stats-label">Estudiantes en Riesgo</div>
                                <div class="stats-change negative">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>-3 este mes</span>
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
                                <label class="form-label">Grupo</label>
                                <select class="form-select" id="groupFilter">
                                    <option value="">Todos los grupos</option>
                                    <option value="ingles-a1">InglÃ©s BÃ¡sico A1</option>
                                    <option value="ingles-b1">InglÃ©s Intermedio B1</option>
                                    <option value="frances-a2">FrancÃ©s BÃ¡sico A2</option>
                                    <option value="aleman-b2">AlemÃ¡n Intermedio B2</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">PerÃ­odo</label>
                                <select class="form-select" id="periodFilter">
                                    <option value="current">Mes actual</option>
                                    <option value="last">Mes anterior</option>
                                    <option value="quarter">Ãšltimo trimestre</option>
                                    <option value="year">AÃ±o acadÃ©mico</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Estudiante</label>
                                <input type="text" class="form-control" id="studentSearch" placeholder="Buscar estudiante...">
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

            <!-- Attendance Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-table me-2"></i>
                        Registro de Asistencia y Calificaciones
                    </h5>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary" onclick="addNewRecord()">
                            <i class="fas fa-plus"></i>
                            Nuevo Registro
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Grupo</th>
                                    <th>Fecha</th>
                                    <th>Asistencia</th>
                                    <th>CA <span class="skill-indicator skill-ca"></span></th>
                                    <th>CL <span class="skill-indicator skill-cl"></span></th>
                                    <th>IEO <span class="skill-indicator skill-ieo"></span></th>
                                    <th>EE <span class="skill-indicator skill-ee"></span></th>
                                    <th>Promedio</th>
                                    <th>Comentarios</th>
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
                                                <small class="text-muted">maria.gonzalez@email.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>InglÃ©s BÃ¡sico A1</td>
                                    <td>15 Nov 2024</td>
                                    <td>
                                        <span class="attendance-status status-present">Presente</span>
                                    </td>
                                    <td><input type="number" class="grade-input" value="85" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="88" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="82" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="86" min="0" max="50"></td>
                                    <td><strong>85.3</strong></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="addComment(1)">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editRecord(1)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteRecord(1)">
                                                <i class="fas fa-trash"></i>
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
                                                <small class="text-muted">carlos.perez@email.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>InglÃ©s BÃ¡sico A1</td>
                                    <td>15 Nov 2024</td>
                                    <td>
                                        <span class="attendance-status status-present">Presente</span>
                                    </td>
                                    <td><input type="number" class="grade-input" value="78" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="82" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="75" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="80" min="0" max="50"></td>
                                    <td><strong>78.8</strong></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="addComment(2)">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editRecord(2)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteRecord(2)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm me-2" title="Ana RodrÃ­guez"></div>
                                            <div>
                                                <div class="fw-bold">Ana RodrÃ­guez</div>
                                                <small class="text-muted">ana.rodriguez@email.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>InglÃ©s BÃ¡sico A1</td>
                                    <td>15 Nov 2024</td>
                                    <td>
                                        <span class="attendance-status status-late">Tardanza</span>
                                    </td>
                                    <td><input type="number" class="grade-input" value="92" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="90" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="88" min="0" max="50"></td>
                                    <td><input type="number" class="grade-input" value="91" min="0" max="50"></td>
                                    <td><strong>90.3</strong></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="addComment(3)">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editRecord(3)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteRecord(3)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm me-2" title="Luis MartÃ­nez"></div>
                                            <div>
                                                <div class="fw-bold">Luis MartÃ­nez</div>
                                                <small class="text-muted">luis.martinez@email.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>InglÃ©s BÃ¡sico A1</td>
                                    <td>15 Nov 2024</td>
                                    <td>
                                        <span class="attendance-status status-justified">Justificada</span>
                                    </td>
                                    <td><input type="number" class="grade-input" value="" min="0" max="50" disabled></td>
                                    <td><input type="number" class="grade-input" value="" min="0" max="50" disabled></td>
                                    <td><input type="number" class="grade-input" value="" min="0" max="50" disabled></td>
                                    <td><input type="number" class="grade-input" value="" min="0" max="50" disabled></td>
                                    <td><strong>-</strong></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="addComment(4)">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editRecord(4)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteRecord(4)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Performance Charts -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-line me-2"></i>
                                Rendimiento por Habilidad
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="skillsChart" class="performance-chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-pie me-2"></i>
                                DistribuciÃ³n de Asistencia
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceChart" class="performance-chart"></canvas>
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
            $('#attendanceTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[2, 'desc']]
            });

            // Initialize charts
            initializeCharts();
        });

        // Charts initialization
        function initializeCharts() {
            // Skills Performance Chart
            const skillsCtx = document.getElementById('skillsChart').getContext('2d');
            new Chart(skillsCtx, {
                type: 'radar',
                data: {
                    labels: ['CA (Listening)', 'CL (Reading)', 'IEO (Speaking)', 'EE (Writing)'],
                    datasets: [{
                        label: 'Promedio del Grupo',
                        data: [85, 88, 82, 86],
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(79, 70, 229, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 50,
                            ticks: {
                                stepSize: 10
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Attendance Distribution Chart
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(attendanceCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Presente', 'Ausente', 'Tardanza', 'Justificada'],
                    datasets: [{
                        data: [75, 15, 8, 2],
                        backgroundColor: [
                            '#10b981',
                            '#ef4444',
                            '#f59e0b',
                            '#3b82f6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Functions
        function applyFilters() {
            const group = document.getElementById('groupFilter').value;
            const period = document.getElementById('periodFilter').value;
            const student = document.getElementById('studentSearch').value;
            
            // Apply filters logic here
            console.log('Applying filters:', { group, period, student });
        }

        function addNewRecord() {
            alert('Agregando nuevo registro de asistencia...');
        }

        function editRecord(id) {
            alert(`Editando registro ${id}...`);
        }

        function deleteRecord(id) {
            if (confirm('Â¿EstÃ¡s seguro de que quieres eliminar este registro?')) {
                alert(`Eliminando registro ${id}...`);
            }
        }

        function addComment(id) {
            const comment = prompt('Agregar comentario:');
            if (comment) {
                alert(`Comentario agregado al registro ${id}: ${comment}`);
            }
        }

        function exportReport() {
            alert('Exportando reporte de asistencia...');
        }

        function bulkEdit() {
            alert('Abriendo editor masivo...');
        }

        // Auto-calculate averages when grades change
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('grade-input')) {
                const row = e.target.closest('tr');
                const inputs = row.querySelectorAll('.grade-input');
                let sum = 0;
                let count = 0;
                
                inputs.forEach(input => {
                    if (input.value && !input.disabled) {
                        sum += parseFloat(input.value);
                        count++;
                    }
                });
                
                if (count > 0) {
                    const average = (sum / count).toFixed(1);
                    row.querySelector('td:nth-child(9) strong').textContent = average;
                } else {
                    row.querySelector('td:nth-child(9) strong').textContent = '-';
                }
            }
        });
        </script>
@endpush
