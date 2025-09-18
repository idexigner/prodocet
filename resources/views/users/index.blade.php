@extends('layouts.app')

@section('title', 'Users')

@push('styles')
<style>
        .user-profile-card {
            background: #ffffff;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
            transition: var(--transition-normal);
        }
        
        .user-profile-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            text-align: center;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 1rem;
            background: var(--background-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-color);
        }
        
        .role-badge {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .role-student {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .role-teacher {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }
        
        .role-admin {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .role-hr {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }
        
        .status-active {
            background: var(--success-color);
        }
        
        .status-inactive {
            background: var(--error-color);
        }
        
        .status-pending {
            background: var(--warning-color);
        }
        
        .document-upload {
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius-md);
            padding: 2rem;
            text-align: center;
            background: var(--background-secondary);
            transition: var(--transition-normal);
            cursor: pointer;
        }
        
        .document-upload:hover {
            border-color: var(--primary-color);
            background: var(--primary-lightest);
        }
        
        .document-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            margin-bottom: 0.5rem;
            background: white;
        }
        
        .document-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
        }
        
        .document-pdf { background: var(--error-color); }
        .document-image { background: var(--info-color); }
        .document-doc { background: var(--primary-color); }
        
        .history-item {
            padding: 1rem;
            border-left: 3px solid var(--primary-color);
            background: var(--background-secondary);
            margin-bottom: 1rem;
            border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
        }
        
        .history-date {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .history-action {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .history-details {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .smart-search {
            position: relative;
        }
        
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            display: none;
        }
        
        .suggestion-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-light);
            cursor: pointer;
            transition: var(--transition-fast);
        }
        
        .suggestion-item:hover {
            background: var(--primary-lightest);
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush
@section('main-section')

            <div class="content-header">
                <div class="content-title">
                    <h1>GestiÃ³n de Usuarios</h1>
                    <p class="content-subtitle">Administra perfiles de estudiantes, profesores y personal</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-primary" onclick="createNewUser()">
                        <i class="fas fa-user-plus"></i>
                        Nuevo Usuario
                    </button>
                    <button class="btn btn-outline-secondary" onclick="bulkImport()">
                        <i class="fas fa-upload"></i>
                        Importar Masivo
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">156</div>
                                <div class="stats-label">Estudiantes</div>
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
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">24</div>
                                <div class="stats-label">Profesores</div>
                                <div class="stats-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+3 este mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">8</div>
                                <div class="stats-label">Administradores</div>
                                <div class="stats-change neutral">
                                    <i class="fas fa-minus"></i>
                                    <span>Sin cambios</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="stats-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number">5</div>
                                <div class="stats-label">Pendientes</div>
                                <div class="stats-change negative">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+2 esta semana</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Smart Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group smart-search">
                                <label class="form-label">BÃºsqueda Inteligente</label>
                                <input type="text" class="form-control" id="smartSearch" placeholder="Buscar por nombre, email, telÃ©fono o documento...">
                                <div class="search-suggestions" id="searchSuggestions">
                                    <!-- Suggestions will be populated here -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Rol</label>
                                <select class="form-select" id="roleFilter">
                                    <option value="">Todos</option>
                                    <option value="student">Estudiante</option>
                                    <option value="teacher">Profesor</option>
                                    <option value="admin">Administrador</option>
                                    <option value="hr">RRHH</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">Todos</option>
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                    <option value="pending">Pendiente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-table me-2"></i>
                        Lista de Usuarios
                    </h5>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportUsers()">
                            <i class="fas fa-download"></i>
                            Exportar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Contacto</th>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Ãšltimo Acceso</th>
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
                                    <td><span class="role-badge role-student">Estudiante</span></td>
                                    <td>
                                        <div>+34 612 345 678</div>
                                        <small class="text-muted">Madrid, EspaÃ±a</small>
                                    </td>
                                    <td>DNI: 12345678A</td>
                                    <td>
                                        <span class="status-indicator status-active"></span>
                                        Activo
                                    </td>
                                    <td>Hace 2 horas</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewUserProfile(1)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="editUser(1)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="resetPassword(1)">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm me-2" title="Prof. GarcÃ­a"></div>
                                            <div>
                                                <div class="fw-bold">Prof. MarÃ­a GarcÃ­a</div>
                                                <small class="text-muted">maria.garcia@prodocet.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge role-teacher">Profesor</span></td>
                                    <td>
                                        <div>+34 698 765 432</div>
                                        <small class="text-muted">Barcelona, EspaÃ±a</small>
                                    </td>
                                    <td>DNI: 87654321B</td>
                                    <td>
                                        <span class="status-indicator status-active"></span>
                                        Activo
                                    </td>
                                    <td>Hace 1 hora</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewUserProfile(2)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="editUser(2)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="resetPassword(2)">
                                                <i class="fas fa-key"></i>
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
                                    <td><span class="role-badge role-student">Estudiante</span></td>
                                    <td>
                                        <div>+34 655 123 456</div>
                                        <small class="text-muted">Valencia, EspaÃ±a</small>
                                    </td>
                                    <td>DNI: 11223344C</td>
                                    <td>
                                        <span class="status-indicator status-pending"></span>
                                        Pendiente
                                    </td>
                                    <td>Nunca</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewUserProfile(3)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="editUser(3)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="resetPassword(3)">
                                                <i class="fas fa-key"></i>
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

    <!-- User Profile Modal -->
    <div class="modal fade" id="userProfileModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perfil de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Profile Info -->
                        <div class="col-lg-4">
                            <div class="user-profile-card">
                                <div class="profile-header">
                                    <div class="profile-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h4 id="profileName">MarÃ­a GonzÃ¡lez</h4>
                                    <span class="role-badge role-student" id="profileRole">Estudiante</span>
                                    <div class="mt-2">
                                        <span class="status-indicator status-active"></span>
                                        <span id="profileStatus">Activo</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>InformaciÃ³n de Contacto</h6>
                                        <p><i class="fas fa-envelope me-2"></i><span id="profileEmail">maria.gonzalez@email.com</span></p>
                                        <p><i class="fas fa-phone me-2"></i><span id="profilePhone">+34 612 345 678</span></p>
                                        <p><i class="fas fa-map-marker-alt me-2"></i><span id="profileAddress">Madrid, EspaÃ±a</span></p>
                                    </div>
                                    <div class="mb-3">
                                        <h6>InformaciÃ³n Personal</h6>
                                        <p><i class="fas fa-calendar me-2"></i>Fecha de Nacimiento: <span id="profileDOB">15/03/1990</span></p>
                                        <p><i class="fas fa-id-card me-2"></i>Documento: <span id="profileDocument">DNI: 12345678A</span></p>
                                    </div>
                                    <div class="mb-3">
                                        <h6>Contacto de Emergencia</h6>
                                        <p><i class="fas fa-user me-2"></i><span id="profileEmergency">Juan GonzÃ¡lez (Padre)</span></p>
                                        <p><i class="fas fa-phone me-2"></i><span id="profileEmergencyPhone">+34 699 999 999</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents and History -->
                        <div class="col-lg-8">
                            <!-- Documents Section -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title">
                                        <i class="fas fa-file-alt me-2"></i>
                                        Documentos
                                    </h6>
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-outline-primary" onclick="uploadDocument()">
                                            <i class="fas fa-upload"></i>
                                            Subir
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="document-upload" onclick="triggerDocumentUpload()">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="mb-0">Haz clic para subir documentos (ID, CV, RUT, informaciÃ³n bancaria)</p>
                                    </div>
                                    <input type="file" id="documentUpload" multiple style="display: none;" onchange="handleDocumentUpload(event)">
                                    
                                    <div id="documentList" class="mt-3">
                                        <div class="document-item">
                                            <div class="document-icon document-pdf">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">DNI_Frontal.pdf</div>
                                                <small class="text-muted">Subido el 10 Nov 2024</small>
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeDocument(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="document-item">
                                            <div class="document-icon document-image">
                                                <i class="fas fa-file-image"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">CV_Maria_Gonzalez.jpg</div>
                                                <small class="text-muted">Subido el 08 Nov 2024</small>
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeDocument(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- History Section -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">
                                        <i class="fas fa-history me-2"></i>
                                        Historial de Cambios
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="historyList">
                                        <div class="history-item">
                                            <div class="history-date">15 Nov 2024 - 14:30</div>
                                            <div class="history-action">Perfil actualizado</div>
                                            <div class="history-details">Se modificÃ³ la informaciÃ³n de contacto</div>
                                        </div>
                                        <div class="history-item">
                                            <div class="history-date">10 Nov 2024 - 09:15</div>
                                            <div class="history-action">Documento subido</div>
                                            <div class="history-details">Se agregÃ³ el DNI frontal</div>
                                        </div>
                                        <div class="history-item">
                                            <div class="history-date">08 Nov 2024 - 16:45</div>
                                            <div class="history-action">Usuario creado</div>
                                            <div class="history-details">Nuevo estudiante registrado en el sistema</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editUserFromModal()">Editar Perfil</button>
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
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[0, 'asc']]
            });

            // Smart search functionality
            $('#smartSearch').on('input', function() {
                const query = $(this).val();
                if (query.length >= 2) {
                    showSearchSuggestions(query);
                } else {
                    hideSearchSuggestions();
                }
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.smart-search').length) {
                    hideSearchSuggestions();
                }
            });
        });

        // Search suggestions
        function showSearchSuggestions(query) {
            const suggestions = [
                { name: 'MarÃ­a GonzÃ¡lez', email: 'maria.gonzalez@email.com', type: 'Estudiante' },
                { name: 'Prof. MarÃ­a GarcÃ­a', email: 'maria.garcia@prodocet.com', type: 'Profesor' },
                { name: 'Carlos PÃ©rez', email: 'carlos.perez@email.com', type: 'Estudiante' }
            ].filter(item => 
                item.name.toLowerCase().includes(query.toLowerCase()) ||
                item.email.toLowerCase().includes(query.toLowerCase())
            );

            const suggestionsHtml = suggestions.map(item => `
                <div class="suggestion-item" onclick="selectSuggestion('${item.name}')">
                    <div class="fw-bold">${item.name}</div>
                    <small class="text-muted">${item.email} - ${item.type}</small>
                </div>
            `).join('');

            $('#searchSuggestions').html(suggestionsHtml).show();
        }

        function hideSearchSuggestions() {
            $('#searchSuggestions').hide();
        }

        function selectSuggestion(name) {
            $('#smartSearch').val(name);
            hideSearchSuggestions();
            // Trigger search
            $('#usersTable').DataTable().search(name).draw();
        }

        // Functions
        function createNewUser() {
            alert('Creando nuevo usuario...');
        }

        function bulkImport() {
            alert('Iniciando importaciÃ³n masiva...');
        }

        function viewUserProfile(id) {
            // Populate modal with user data
            const userData = {
                1: {
                    name: 'MarÃ­a GonzÃ¡lez',
                    role: 'Estudiante',
                    email: 'maria.gonzalez@email.com',
                    phone: '+34 612 345 678',
                    address: 'Madrid, EspaÃ±a',
                    dob: '15/03/1990',
                    document: 'DNI: 12345678A',
                    emergency: 'Juan GonzÃ¡lez (Padre)',
                    emergencyPhone: '+34 699 999 999',
                    status: 'Activo'
                },
                2: {
                    name: 'Prof. MarÃ­a GarcÃ­a',
                    role: 'Profesor',
                    email: 'maria.garcia@prodocet.com',
                    phone: '+34 698 765 432',
                    address: 'Barcelona, EspaÃ±a',
                    dob: '22/07/1985',
                    document: 'DNI: 87654321B',
                    emergency: 'Luis GarcÃ­a (Esposo)',
                    emergencyPhone: '+34 688 888 888',
                    status: 'Activo'
                }
            };

            const user = userData[id] || userData[1];
            
            $('#profileName').text(user.name);
            $('#profileRole').text(user.role).removeClass().addClass(`role-badge role-${user.role.toLowerCase()}`);
            $('#profileEmail').text(user.email);
            $('#profilePhone').text(user.phone);
            $('#profileAddress').text(user.address);
            $('#profileDOB').text(user.dob);
            $('#profileDocument').text(user.document);
            $('#profileEmergency').text(user.emergency);
            $('#profileEmergencyPhone').text(user.emergencyPhone);
            $('#profileStatus').text(user.status);

            const modal = new bootstrap.Modal(document.getElementById('userProfileModal'));
            modal.show();
        }

        function editUser(id) {
            alert(`Editando usuario ${id}...`);
        }

        function editUserFromModal() {
            alert('Editando perfil desde el modal...');
        }

        function resetPassword(id) {
            if (confirm('Â¿EstÃ¡s seguro de que quieres restablecer la contraseÃ±a de este usuario?')) {
                alert(`ContraseÃ±a restablecida para el usuario ${id}. Nueva contraseÃ±a: Ãºltimos 4 dÃ­gitos del documento.`);
            }
        }

        function exportUsers() {
            alert('Exportando lista de usuarios...');
        }

        function uploadDocument() {
            document.getElementById('documentUpload').click();
        }

        function triggerDocumentUpload() {
            document.getElementById('documentUpload').click();
        }

        function handleDocumentUpload(event) {
            const files = event.target.files;
            Array.from(files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'document-item';
                
                const fileIcon = getFileIcon(file.type);
                
                fileItem.innerHTML = `
                    <div class="document-icon ${fileIcon.class}">
                        <i class="${fileIcon.icon}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${file.name}</div>
                        <small class="text-muted">Subido el ${new Date().toLocaleDateString()}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeDocument(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                
                document.getElementById('documentList').appendChild(fileItem);
            });
        }

        function getFileIcon(fileType) {
            if (fileType.includes('pdf')) {
                return { icon: 'fas fa-file-pdf', class: 'document-pdf' };
            } else if (fileType.includes('image')) {
                return { icon: 'fas fa-file-image', class: 'document-image' };
            } else {
                return { icon: 'fas fa-file-alt', class: 'document-doc' };
            }
        }

        function removeDocument(button) {
            button.closest('.document-item').remove();
        }
        </script>
@endpush
