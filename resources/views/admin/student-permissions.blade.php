@extends('layouts.app')

@section('title', 'Gestión de Permisos de Estudiantes')

@push('css')
<style>
    .permission-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #fff;
    }
    
    .permission-group {
        margin-bottom: 1.5rem;
    }
    
    .permission-group h5 {
        color: #5a5c69;
        border-bottom: 2px solid #e3e6f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .permission-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .permission-item input[type="checkbox"] {
        margin-right: 0.5rem;
    }
    
    .student-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #fff;
        transition: all 0.3s ease;
    }
    
    .student-card:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .student-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .student-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .bulk-actions {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 2rem;
    }
    
    .permission-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 4px;
        margin: 0.125rem;
    }
    
    .permission-badge.granted {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .permission-badge.denied {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
@endpush

@section('main-section')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Permisos de Estudiantes</h1>
        <div>
            <button type="button" class="btn btn-primary" onclick="showBulkActions()">
                <i class="fas fa-users"></i> Acciones Masivas
            </button>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div id="bulk-actions-panel" class="bulk-actions" style="display: none;">
        <h5><i class="fas fa-users"></i> Acciones Masivas</h5>
        <div class="row">
            <div class="col-md-6">
                <label>Seleccionar Estudiantes:</label>
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllStudents()">Seleccionar Todos</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllStudents()">Deseleccionar Todos</button>
                </div>
            </div>
            <div class="col-md-6">
                <label>Permisos a Asignar:</label>
                <div id="bulk-permissions" class="permission-group">
                    @foreach($studentPermissions as $permission)
                    <div class="permission-item">
                        <input type="checkbox" id="bulk-{{ $permission->name }}" name="bulk_permissions[]" value="{{ $permission->name }}">
                        <label for="bulk-{{ $permission->name }}">{{ $permission->name }}</label>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-success" onclick="applyBulkPermissions()">
                    <i class="fas fa-check"></i> Aplicar Permisos
                </button>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="row">
        @forelse($students as $student)
        <div class="col-md-6 col-lg-4">
            <div class="student-card">
                <div class="student-info">
                    <div>
                        <h6 class="mb-1">{{ $student->name }}</h6>
                        <small class="text-muted">{{ $student->email }}</small>
                    </div>
                    <div class="student-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editStudentPermissions({{ $student->id }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <input type="checkbox" class="student-checkbox" value="{{ $student->id }}" style="margin-left: 0.5rem;">
                    </div>
                </div>
                
                <div class="permissions-display">
                    <small class="text-muted">Permisos actuales:</small>
                    <div class="mt-1">
                        @forelse($student->permissions as $permission)
                        <span class="permission-badge granted">{{ $permission->name }}</span>
                        @empty
                        <span class="text-muted">Sin permisos específicos</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay estudiantes registrados.
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Edit Permissions Modal -->
<div class="modal fade" id="editPermissionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Permisos de Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPermissionsForm">
                    <input type="hidden" id="edit-student-id" name="student_id">
                    
                    <div class="permission-group">
                        <h5>Permisos Disponibles:</h5>
                        @foreach($studentPermissions as $permission)
                        <div class="permission-item">
                            <input type="checkbox" id="edit-{{ $permission->name }}" name="permissions[]" value="{{ $permission->name }}">
                            <label for="edit-{{ $permission->name }}">{{ $permission->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveStudentPermissions()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
let currentStudentId = null;

function showBulkActions() {
    const panel = document.getElementById('bulk-actions-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function selectAllStudents() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllStudents() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function editStudentPermissions(studentId) {
    currentStudentId = studentId;
    
    // Clear all checkboxes first
    document.querySelectorAll('#editPermissionsForm input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Fetch current permissions
    fetch(`/student-permissions/${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Check the permissions the student currently has
                data.permissions.forEach(permission => {
                    const checkbox = document.getElementById(`edit-${permission}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
                
                // Set student ID
                document.getElementById('edit-student-id').value = studentId;
                
                // Show modal
                $('#editPermissionsModal').modal('show');
            } else {
                alert('Error al cargar los permisos: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los permisos del estudiante');
        });
}

function saveStudentPermissions() {
    const form = document.getElementById('editPermissionsForm');
    const formData = new FormData(form);
    
    fetch('/student-permissions/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Permisos actualizados correctamente');
            $('#editPermissionsModal').modal('hide');
            location.reload(); // Reload to show updated permissions
        } else {
            alert('Error al actualizar permisos: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar los permisos');
    });
}

function applyBulkPermissions() {
    const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
    const selectedPermissions = Array.from(document.querySelectorAll('#bulk-permissions input[type="checkbox"]:checked')).map(cb => cb.value);
    
    if (selectedStudents.length === 0) {
        alert('Por favor selecciona al menos un estudiante');
        return;
    }
    
    if (selectedPermissions.length === 0) {
        alert('Por favor selecciona al menos un permiso');
        return;
    }
    
    if (!confirm(`¿Estás seguro de aplicar estos permisos a ${selectedStudents.length} estudiantes?`)) {
        return;
    }
    
    const formData = new FormData();
    selectedStudents.forEach(id => formData.append('student_ids[]', id));
    selectedPermissions.forEach(permission => formData.append('permissions[]', permission));
    
    fetch('/student-permissions/bulk-update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Reload to show updated permissions
        } else {
            alert('Error al actualizar permisos: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar los permisos');
    });
}
</script>
@endpush
