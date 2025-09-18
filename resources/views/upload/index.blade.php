@extends('layouts.app')

@section('title', 'Upload')

@push('styles')
<style>
        .upload-area {
            border: 2px dashed #4f46e5;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: rgba(79, 70, 229, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: #3730a3;
            background: rgba(79, 70, 229, 0.1);
        }
        
        .upload-area.dragover {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }
        
        .file-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #ffffff;
        }
        
        .file-item .file-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
        }
        
        .file-item .file-info {
            flex: 1;
        }
        
        .file-item .file-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .file-item .file-size {
            font-size: 12px;
            color: #64748b;
        }
        
        .file-item .file-actions {
            display: flex;
            gap: 5px;
        }
        
        .upload-progress {
            margin-top: 10px;
        }
        
        .deadline-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .deadline-warning .warning-icon {
            color: #d97706;
            font-size: 24px;
            margin-right: 10px;
        }
        
        .class-selector {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
    @endpush

@section('content')

            <div class="content-header">
                <div class="content-title">
                    <h1>Subir Datos de Clase</h1>
                    <p class="content-subtitle">Sube asistencia, calificaciones y documentos relacionados con tus clases</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-primary" onclick="submitAllData()">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Todo
                    </button>
                    <button class="btn btn-outline-secondary" onclick="viewUploadHistory()">
                        <i class="fas fa-history"></i>
                        Historial
                    </button>
                </div>
            </div>

            <!-- Deadline Warning -->
            <div class="deadline-warning">
                <div class="d-flex align-items-center">
                    <div class="warning-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Recordatorio Importante</h6>
                        <p class="mb-0">Los profesores deben subir los datos de clase (asistencia, calificaciones y documentos) dentro de las <strong>48 horas</strong> posteriores a la finalizaciÃ³n de la clase. Los datos no subidos despuÃ©s de este perÃ­odo serÃ¡n marcados como tardÃ­os.</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-upload"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">8</div>
                                <div class="stats-label">Clases Pendientes</div>
                                <div class="stats-change negative">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>-2 esta semana</span>
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
                                <div class="stats-number">24</div>
                                <div class="stats-label">Clases Completadas</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+3 esta semana</span>
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
                                <div class="stats-number">2</div>
                                <div class="stats-label">Atrasadas</div>
                                <div class="stats-change negative">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+1 esta semana</span>
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
                                <div class="stats-number">36h</div>
                                <div class="stats-label">Tiempo Promedio</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>-2h este mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class Selector -->
            <div class="class-selector">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Seleccionar Clase</label>
                            <select class="form-select" id="classSelector" onchange="loadClassData()">
                                <option value="">Seleccionar una clase...</option>
                                <option value="1">InglÃ©s BÃ¡sico A1 - 15 Nov 2024 09:00</option>
                                <option value="2">FrancÃ©s Intermedio B1 - 14 Nov 2024 11:00</option>
                                <option value="3">InglÃ©s Avanzado C1 - 13 Nov 2024 15:00</option>
                                <option value="4">AlemÃ¡n BÃ¡sico A2 - 12 Nov 2024 10:00</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <div class="mt-2">
                                <span class="badge bg-warning">Pendiente</span>
                                <span class="badge bg-success ms-2">Completada</span>
                                <span class="badge bg-danger ms-2">Atrasada</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Tiempo Restante</label>
                            <div class="mt-2">
                                <span class="text-warning fw-bold" id="timeRemaining">23:45:12</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Sections -->
            <div class="row">
                <!-- Attendance Upload -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-check-circle me-2"></i>
                                Asistencia
                            </h5>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-outline-primary" onclick="markAttendanceManually()">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Estudiante</th>
                                            <th>Estado</th>
                                            <th>JustificaciÃ³n</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>MarÃ­a GonzÃ¡lez</td>
                                            <td><span class="badge bg-success">Presente</span></td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>Carlos PÃ©rez</td>
                                            <td><span class="badge bg-success">Presente</span></td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>Ana RodrÃ­guez</td>
                                            <td><span class="badge bg-warning">Tardanza</span></td>
                                            <td>TrÃ¡fico</td>
                                        </tr>
                                        <tr>
                                            <td>Luis MartÃ­nez</td>
                                            <td><span class="badge bg-danger">Ausente</span></td>
                                            <td>Enfermedad</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grades Upload -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-star me-2"></i>
                                Calificaciones
                            </h5>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-outline-primary" onclick="editGrades()">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Estudiante</th>
                                            <th>CA</th>
                                            <th>CL</th>
                                            <th>IEO</th>
                                            <th>EE</th>
                                            <th>Promedio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>MarÃ­a GonzÃ¡lez</td>
                                            <td>85</td>
                                            <td>88</td>
                                            <td>82</td>
                                            <td>86</td>
                                            <td><strong>85.3</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Carlos PÃ©rez</td>
                                            <td>78</td>
                                            <td>82</td>
                                            <td>75</td>
                                            <td>80</td>
                                            <td><strong>78.8</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Ana RodrÃ­guez</td>
                                            <td>92</td>
                                            <td>90</td>
                                            <td>88</td>
                                            <td>91</td>
                                            <td><strong>90.3</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Luis MartÃ­nez</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td><strong>-</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Upload Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-file-upload me-2"></i>
                                Documentos y Materiales
                            </h5>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-outline-primary" onclick="createFolder()">
                                    <i class="fas fa-folder-plus"></i>
                                    Nueva Carpeta
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Upload Area -->
                            <div class="upload-area" onclick="document.getElementById('fileInput').click()" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                                <div class="upload-icon mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                </div>
                                <h5>Arrastra archivos aquÃ­ o haz clic para seleccionar</h5>
                                <p class="text-muted">Puedes subir archivos PDF, Word, Excel, imÃ¡genes y otros materiales de clase</p>
                                <input type="file" id="fileInput" multiple style="display: none;" onchange="handleFileSelect(event)">
                            </div>

                            <!-- File List -->
                            <div class="mt-4">
                                <h6>Archivos Seleccionados</h6>
                                <div id="fileList">
                                    <div class="file-item">
                                        <div class="file-icon bg-primary">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="file-info">
                                            <div class="file-name">Asistencia_15_Nov_2024.pdf</div>
                                            <div class="file-size">245 KB</div>
                                        </div>
                                        <div class="file-actions">
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeFile(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="file-item">
                                        <div class="file-icon bg-success">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <div class="file-info">
                                            <div class="file-name">Calificaciones_Noviembre.xlsx</div>
                                            <div class="file-size">1.2 MB</div>
                                        </div>
                                        <div class="file-actions">
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeFile(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="file-item">
                                        <div class="file-icon bg-warning">
                                            <i class="fas fa-file-image"></i>
                                        </div>
                                        <div class="file-info">
                                            <div class="file-name">Material_Clase_15.jpg</div>
                                            <div class="file-size">856 KB</div>
                                        </div>
                                        <div class="file-actions">
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeFile(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Progress -->
                            <div class="upload-progress" id="uploadProgress" style="display: none;">
                                <div class="progress mb-2">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">Subiendo archivos...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-comment me-2"></i>
                                Comentarios y Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Comentarios sobre la clase</label>
                                <textarea class="form-control" rows="4" placeholder="Describe el desarrollo de la clase, dificultades encontradas, logros de los estudiantes, etc."></textarea>
                            </div>
                            <div class="form-group mt-3">
                                <label class="form-label">Observaciones especiales</label>
                                <textarea class="form-control" rows="3" placeholder="Observaciones sobre estudiantes especÃ­ficos, problemas tÃ©cnicos, etc."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h5>Â¿Todo listo para enviar?</h5>
                                <p class="text-muted">Revisa que todos los datos estÃ©n completos antes de enviar</p>
                            </div>
                            <div class="d-flex justify-content-center gap-3">
                                <button class="btn btn-outline-secondary" onclick="previewData()">
                                    <i class="fas fa-eye me-2"></i>
                                    Vista Previa
                                </button>
                                <button class="btn btn-primary" onclick="submitAllData()">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Enviar Datos de Clase
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
    </div>

    <!-- Bootstrap JS -->
    @endsection

@push('scripts')
    <script>

        // File upload functions
        function handleFileSelect(event) {
            const files = event.target.files;
            addFilesToList(files);
        }

        function handleDrop(event) {
            event.preventDefault();
            const uploadArea = event.currentTarget;
            uploadArea.classList.remove('dragover');
            
            const files = event.dataTransfer.files;
            addFilesToList(files);
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.classList.add('dragover');
        }

        function handleDragLeave(event) {
            event.preventDefault();
            event.currentTarget.classList.remove('dragover');
        }

        function addFilesToList(files) {
            const fileList = document.getElementById('fileList');
            
            Array.from(files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                
                const fileIcon = getFileIcon(file.type);
                const fileSize = formatFileSize(file.size);
                
                fileItem.innerHTML = `
                    <div class="file-icon ${fileIcon.color}">
                        <i class="${fileIcon.icon}"></i>
                    </div>
                    <div class="file-info">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${fileSize}</div>
                    </div>
                    <div class="file-actions">
                        <button class="btn btn-sm btn-outline-danger" onclick="removeFile(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                
                fileList.appendChild(fileItem);
            });
        }

        function getFileIcon(fileType) {
            if (fileType.includes('pdf')) {
                return { icon: 'fas fa-file-pdf', color: 'bg-primary' };
            } else if (fileType.includes('excel') || fileType.includes('spreadsheet')) {
                return { icon: 'fas fa-file-excel', color: 'bg-success' };
            } else if (fileType.includes('word') || fileType.includes('document')) {
                return { icon: 'fas fa-file-word', color: 'bg-info' };
            } else if (fileType.includes('image')) {
                return { icon: 'fas fa-file-image', color: 'bg-warning' };
            } else {
                return { icon: 'fas fa-file', color: 'bg-secondary' };
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeFile(button) {
            button.closest('.file-item').remove();
        }

        // Other functions
        function loadClassData() {
            const selectedClass = document.getElementById('classSelector').value;
            if (selectedClass) {
                alert('Cargando datos de la clase seleccionada...');
            }
        }

        function markAttendanceManually() {
            alert('Abriendo editor de asistencia...');
        }

        function editGrades() {
            alert('Abriendo editor de calificaciones...');
        }

        function createFolder() {
            alert('Creando nueva carpeta...');
        }

        function previewData() {
            alert('Mostrando vista previa de los datos...');
        }

        function submitAllData() {
            alert('Enviando datos de clase...');
        }

        function viewUploadHistory() {
            alert('Mostrando historial de subidas...');
        }

        // Update countdown timer
        function updateCountdown() {
            // This would be calculated based on the selected class deadline
            const timeRemaining = document.getElementById('timeRemaining');
            // Implementation for countdown timer
        }

        // Initialize
        $(document).ready(function() {
            // Start countdown timer
            setInterval(updateCountdown, 1000);
        });
        </script>
@endpush
