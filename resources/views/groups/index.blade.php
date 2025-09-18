

@extends('layouts.app')

@section('title', 'Groups')


@section('main-section')


   

            <div class="content-header">
                <div class="content-title">
                    <h1>GestiÃ³n de Grupos</h1>
                    <p class="content-subtitle">Administrar grupos, cursos y asignaciones de estudiantes</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-success" onclick="openNewGroupModal()">
                        <i class="fas fa-plus"></i>
                        Nuevo Grupo
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportGroups()">
                        <i class="fas fa-download"></i>
                        Exportar
                    </button>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Idioma</label>
                                    <select class="form-select" id="languageFilter">
                                        <option value="">Todos los idiomas</option>
                                        <option value="english">InglÃ©s</option>
                                        <option value="french">FrancÃ©s</option>
                                        <option value="german">AlemÃ¡n</option>
                                        <option value="italian">Italiano</option>
                                        <option value="portuguese">PortuguÃ©s</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nivel</label>
                                    <select class="form-select" id="levelFilter">
                                        <option value="">Todos los niveles</option>
                                        <option value="A1">A1 - BÃ¡sico</option>
                                        <option value="A2">A2 - Elemental</option>
                                        <option value="B1">B1 - Intermedio</option>
                                        <option value="B2">B2 - Intermedio Alto</option>
                                        <option value="C1">C1 - Avanzado</option>
                                        <option value="C2">C2 - Experto</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" id="statusFilter">
                                        <option value="">Todos los estados</option>
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                        <option value="completed">Completado</option>
                                        <option value="suspended">Suspendido</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                                        <i class="fas fa-filter"></i>
                                        Aplicar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Groups Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-users me-2"></i>
                                Lista de Grupos
                            </h5>
                            <div class="card-tools">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="switchView('table')">
                                        <i class="fas fa-table"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="switchView('cards')">
                                        <i class="fas fa-th"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Table View -->
                            <div id="tableView">
                                <div class="table-responsive">
                                    <table id="groupsTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre del Grupo</th>
                                                <th>Idioma</th>
                                                <th>Nivel</th>
                                                <th>Profesor</th>
                                                <th>Estudiantes</th>
                                                <th>Horario</th>
                                                <th>Estado</th>
                                                <th>Tarifa/Hora</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Cards View (Initially Hidden) -->
                            <div id="cardsView" class="d-none">
                                <div class="row" id="groupsCards">
                                    <!-- Cards will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
    

    <!-- New Group Modal -->
    <div class="modal fade" id="newGroupModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Crear Nuevo Grupo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="newGroupForm" data-autosave="true">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">InformaciÃ³n BÃ¡sica</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="groupName" class="form-label">Nombre del Grupo * (MÃ¡x. 50 caracteres)</label>
                                    <input type="text" class="form-control" id="groupName" name="groupName" maxlength="50" required>
                                    <small class="form-text text-muted">MÃ¡ximo 50 caracteres permitidos</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="groupCode" class="form-label">CÃ³digo del Grupo</label>
                                    <input type="text" class="form-control" id="groupCode" name="groupCode" placeholder="Auto-generado">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="language" class="form-label">Idioma *</label>
                                    <select class="form-select" id="language" name="language" required>
                                        <option value="">Seleccionar idioma</option>
                                        <option value="english">InglÃ©s</option>
                                        <option value="french">FrancÃ©s</option>
                                        <option value="german">AlemÃ¡n</option>
                                        <option value="italian">Italiano</option>
                                        <option value="portuguese">PortuguÃ©s</option>
                                        <option value="spanish">EspaÃ±ol</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Nivel *</label>
                                    <select class="form-select" id="level" name="level" required>
                                        <option value="">Seleccionar nivel</option>
                                        <option value="A1">A1 - BÃ¡sico</option>
                                        <option value="A2">A2 - Elemental</option>
                                        <option value="B1">B1 - Intermedio</option>
                                        <option value="B2">B2 - Intermedio Alto</option>
                                        <option value="C1">C1 - Avanzado</option>
                                        <option value="C2">C2 - Experto</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Teacher and Schedule -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3 mt-3">Profesor y Horario</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="teacher" class="form-label">Profesor Asignado</label>
                                    <select class="form-select" id="teacher" name="teacher">
                                        <option value="">Seleccionar profesor</option>
                                        <option value="garcia">Prof. GarcÃ­a</option>
                                        <option value="martin">Prof. MartÃ­n</option>
                                        <option value="lopez">Prof. LÃ³pez</option>
                                        <option value="schmidt">Prof. Schmidt</option>
                                        <option value="rossi">Prof. Rossi</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maxStudents" class="form-label">MÃ¡ximo de Estudiantes</label>
                                    <input type="number" class="form-control" id="maxStudents" name="maxStudents" min="1" max="20" value="10">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="scheduleDay" class="form-label">DÃ­a de la Semana</label>
                                    <select class="form-select" id="scheduleDay" name="scheduleDay">
                                        <option value="">Seleccionar dÃ­a</option>
                                        <option value="monday">Lunes</option>
                                        <option value="tuesday">Martes</option>
                                        <option value="wednesday">MiÃ©rcoles</option>
                                        <option value="thursday">Jueves</option>
                                        <option value="friday">Viernes</option>
                                        <option value="saturday">SÃ¡bado</option>
                                        <option value="sunday">Domingo</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="scheduleTime" class="form-label">Hora de Inicio</label>
                                    <input type="time" class="form-control" id="scheduleTime" name="scheduleTime">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">DuraciÃ³n (minutos)</label>
                                    <select class="form-select" id="duration" name="duration">
                                        <option value="60">60 minutos</option>
                                        <option value="90" selected>90 minutos</option>
                                        <option value="120">120 minutos</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Financial Information -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3 mt-3">InformaciÃ³n Financiera</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hourlyRate" class="form-label">Tarifa por Hora (Pesos)</label>
                                    <input type="number" class="form-control" id="hourlyRate" name="hourlyRate" step="100" min="0" placeholder="25000">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="totalSessions" class="form-label">Horas AcadÃ©micas</label>
                                    <input type="number" class="form-control" id="totalSessions" name="totalSessions" min="1" value="20" placeholder="Total de horas acadÃ©micas">
                                </div>
                            </div>
                            
                            <!-- Topic Assignment -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3 mt-3">AsignaciÃ³n de Temas</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Materia Principal</label>
                                    <select class="form-select" id="subject" name="subject">
                                        <option value="">Seleccionar materia</option>
                                        <option value="grammar">GramÃ¡tica</option>
                                        <option value="conversation">ConversaciÃ³n</option>
                                        <option value="reading">Lectura</option>
                                        <option value="writing">Escritura</option>
                                        <option value="listening">ComprensiÃ³n Auditiva</option>
                                        <option value="vocabulary">Vocabulario</option>
                                        <option value="business">InglÃ©s de Negocios</option>
                                        <option value="exam_prep">PreparaciÃ³n de ExÃ¡menes</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="difficulty" class="form-label">Nivel de Dificultad</label>
                                    <select class="form-select" id="difficulty" name="difficulty">
                                        <option value="">Seleccionar nivel</option>
                                        <option value="beginner">Principiante</option>
                                        <option value="intermediate">Intermedio</option>
                                        <option value="advanced">Avanzado</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="topics" class="form-label">Temas EspecÃ­ficos</label>
                                    <textarea class="form-control" id="topics" name="topics" rows="3" placeholder="Lista de temas especÃ­ficos a cubrir en el curso"></textarea>
                                </div>
                            </div>
                            
                            <!-- Additional Options -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3 mt-3">Opciones Adicionales</h6>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">DescripciÃ³n</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="DescripciÃ³n opcional del grupo"></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="allowRotation" name="allowRotation">
                                    <label class="form-check-label" for="allowRotation">
                                        Permitir rotaciÃ³n de estudiantes
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autoEnroll" name="autoEnroll">
                                    <label class="form-check-label" for="autoEnroll">
                                        InscripciÃ³n automÃ¡tica habilitada
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveGroup()">
                        <i class="fas fa-save"></i>
                        Crear Grupo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Details Modal -->
    <div class="modal fade" id="groupDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Detalles del Grupo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="groupDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editGroup()">
                        <i class="fas fa-edit"></i>
                        Editar Grupo
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endsection


@push('scripts')
    <script src="{{ asset('assets/js/groups.js') }}"></script>
    
@endpush





