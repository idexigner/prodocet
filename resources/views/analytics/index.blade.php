@extends('layouts.app')

@section('title', 'Analytics')

@push('styles')
<style>
        .analytics-card {
            background: #ffffff;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }
        
        .metric-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .metric-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .metric-change {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .metric-change.positive {
            color: #a7f3d0;
        }
        
        .metric-change.negative {
            color: #fecaca;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
        }
        
        .trend-indicator {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .trend-up {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .trend-down {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .trend-neutral {
            background: rgba(107, 114, 128, 0.1);
            color: var(--text-secondary);
        }
        
        .performance-bar {
            height: 8px;
            border-radius: var(--border-radius-sm);
            background: var(--background-tertiary);
            overflow: hidden;
            margin: 0.5rem 0;
        }
        
        .performance-fill {
            height: 100%;
            border-radius: var(--border-radius-sm);
            transition: width 0.3s ease;
        }
        
        .performance-excellent { background: var(--success-color); }
        .performance-good { background: var(--secondary-color); }
        .performance-average { background: var(--warning-color); }
        .performance-poor { background: var(--error-color); }
    </style>
@endpush
@section('content')

            <div class="content-header">
                <div class="content-title">
                    <h1>AnÃ¡lisis y MÃ©tricas</h1>
                    <p class="content-subtitle">Insights detallados sobre el rendimiento del sistema</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-primary" onclick="exportAnalytics()">
                        <i class="fas fa-download"></i>
                        Exportar Reporte
                    </button>
                    <button class="btn btn-outline-secondary" onclick="generateCustomReport()">
                        <i class="fas fa-chart-bar"></i>
                        Reporte Personalizado
                    </button>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value">89.2%</div>
                        <div class="metric-label">Tasa de Asistencia</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +2.1% vs mes anterior
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value">82.5</div>
                        <div class="metric-label">Promedio General</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +1.8 vs mes anterior
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value">156</div>
                        <div class="metric-label">Estudiantes Activos</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +12 vs mes anterior
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value">24</div>
                        <div class="metric-label">Profesores</div>
                        <div class="metric-change neutral">
                            <i class="fas fa-minus"></i>
                            Sin cambios
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="analytics-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-line me-2"></i>
                                Rendimiento por Mes
                            </h5>
                            <div class="card-tools">
                                <select class="form-select form-select-sm" id="performanceMetric">
                                    <option value="attendance">Asistencia</option>
                                    <option value="grades">Calificaciones</option>
                                    <option value="satisfaction">SatisfacciÃ³n</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="analytics-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-pie me-2"></i>
                                DistribuciÃ³n por Idioma
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="languageChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Analysis -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-4">
                    <div class="analytics-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-star me-2"></i>
                                Rendimiento por Habilidad
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>CA (Listening)</span>
                                    <span class="fw-bold">85.3%</span>
                                </div>
                                <div class="performance-bar">
                                    <div class="performance-fill performance-excellent" style="width: 85.3%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>CL (Reading)</span>
                                    <span class="fw-bold">88.7%</span>
                                </div>
                                <div class="performance-bar">
                                    <div class="performance-fill performance-excellent" style="width: 88.7%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>IEO (Speaking)</span>
                                    <span class="fw-bold">82.1%</span>
                                </div>
                                <div class="performance-bar">
                                    <div class="performance-fill performance-good" style="width: 82.1%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>EE (Writing)</span>
                                    <span class="fw-bold">86.4%</span>
                                </div>
                                <div class="performance-bar">
                                    <div class="performance-fill performance-excellent" style="width: 86.4%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="analytics-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-users me-2"></i>
                                Top Grupos por Rendimiento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Grupo</th>
                                            <th>Promedio</th>
                                            <th>Tendencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>InglÃ©s Avanzado C1</td>
                                            <td>92.3%</td>
                                            <td><span class="trend-indicator trend-up"><i class="fas fa-arrow-up me-1"></i>+3.2%</span></td>
                                        </tr>
                                        <tr>
                                            <td>FrancÃ©s Intermedio B1</td>
                                            <td>89.7%</td>
                                            <td><span class="trend-indicator trend-up"><i class="fas fa-arrow-up me-1"></i>+1.8%</span></td>
                                        </tr>
                                        <tr>
                                            <td>AlemÃ¡n BÃ¡sico A2</td>
                                            <td>87.4%</td>
                                            <td><span class="trend-indicator trend-neutral"><i class="fas fa-minus me-1"></i>0.0%</span></td>
                                        </tr>
                                        <tr>
                                            <td>InglÃ©s BÃ¡sico A1</td>
                                            <td>85.2%</td>
                                            <td><span class="trend-indicator trend-down"><i class="fas fa-arrow-down me-1"></i>-0.5%</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analytics -->
            <div class="row">
                <div class="col-12">
                    <div class="analytics-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-area me-2"></i>
                                AnÃ¡lisis Detallado de Tendencias
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 400px;">
                                <canvas id="trendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
    </div>

    @endsection

@push('scripts')
    <script>

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        function initializeCharts() {
            // Performance Chart
            const performanceCtx = document.getElementById('performanceChart').getContext('2d');
            new Chart(performanceCtx, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    datasets: [{
                        label: 'Asistencia (%)',
                        data: [85, 87, 86, 88, 89, 90, 88, 89, 91, 90, 89, 89.2],
                        borderColor: 'rgba(79, 70, 229, 1)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 80,
                            max: 100
                        }
                    }
                }
            });

            // Language Distribution Chart
            const languageCtx = document.getElementById('languageChart').getContext('2d');
            new Chart(languageCtx, {
                type: 'doughnut',
                data: {
                    labels: ['InglÃ©s', 'FrancÃ©s', 'AlemÃ¡n', 'EspaÃ±ol'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            '#4f46e5',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
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

            // Trends Chart
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'bar',
                data: {
                    labels: ['Asistencia', 'Calificaciones', 'SatisfacciÃ³n', 'RetenciÃ³n'],
                    datasets: [{
                        label: 'Mes Actual',
                        data: [89.2, 82.5, 91.3, 94.7],
                        backgroundColor: 'rgba(79, 70, 229, 0.8)'
                    }, {
                        label: 'Mes Anterior',
                        data: [87.1, 80.7, 89.8, 93.2],
                        backgroundColor: 'rgba(107, 114, 128, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 75,
                            max: 100
                        }
                    }
                }
            });
        }

        function exportAnalytics() {
            alert('Exportando reporte de anÃ¡lisis...');
        }

        function generateCustomReport() {
            alert('Generando reporte personalizado...');
        }

        // Update performance chart when metric changes
        document.getElementById('performanceMetric').addEventListener('change', function() {
            const metric = this.value;
            console.log('MÃ©trica cambiada a:', metric);
            // Here you would update the chart data based on the selected metric
        });
        </script>
@endpush
