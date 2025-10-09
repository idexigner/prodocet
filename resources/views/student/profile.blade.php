@extends('layouts.app')

@section('title', 'Mi Perfil')


@push('css-link')

@include('partials.common.datatable_style')

<style>
.profile-avatar {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 3rem;
    color: white;
}

.profile-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.profile-email {
    color: #6c757d;
    margin-bottom: 1rem;
}

.profile-status {
    margin-bottom: 1rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.stat-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.stat-label {
    color: #6c757d;
    font-size: 0.875rem;
}

.stat-value {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.875rem;
}

.info-item {
    margin-bottom: 1rem;
}

.info-label {
    display: block;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-value {
    color: #2c3e50;
    font-size: 1rem;
}

.info-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}
</style>
@endpush


@section('main-section')
<div class="content-header">
    <div class="content-title">
        <h1>Mi Perfil</h1>
        <p class="content-subtitle">Gestiona tu información personal</p>
    </div>
    <div class="content-actions">
        <button class="btn btn-primary" onclick="editProfile()">
            <i class="fas fa-edit me-1"></i>
            Editar Perfil
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h5 class="profile-name">{{ $student->first_name }} {{ $student->last_name }}</h5>
                <p class="profile-email">{{ $student->email }}</p>
                <div class="profile-status">
                    <span class="badge bg-success">Activo</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Estadísticas Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="stat-item">
                    <div class="stat-label">Miembro desde</div>
                    <div class="stat-value">{{ \Carbon\Carbon::parse($student->created_at)->format('M Y') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Último acceso</div>
                    <div class="stat-value">{{ $student->last_login_at ? \Carbon\Carbon::parse($student->last_login_at)->format('d/m/Y H:i') : 'Nunca' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-user me-2"></i>
                    Información Personal
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">Nombre:</label>
                            <span class="info-value">{{ $student->first_name }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Apellido:</label>
                            <span class="info-value">{{ $student->last_name }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Email:</label>
                            <span class="info-value">{{ $student->email }}</span>
                        </div>
                        @if($student->phone)
                            <div class="info-item">
                                <label class="info-label">Teléfono:</label>
                                <span class="info-value">{{ $student->phone }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($student->birth_date)
                            <div class="info-item">
                                <label class="info-label">Fecha de Nacimiento:</label>
                                <span class="info-value">{{ \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if($student->document_id)
                            <div class="info-item">
                                <label class="info-label">Documento ID:</label>
                                <span class="info-value">{{ $student->document_id }}</span>
                            </div>
                        @endif
                        @if($student->passport_number)
                            <div class="info-item">
                                <label class="info-label">Número de Pasaporte:</label>
                                <span class="info-value">{{ $student->passport_number }}</span>
                            </div>
                        @endif
                        @if($student->language_preference)
                            <div class="info-item">
                                <label class="info-label">Idioma Preferido:</label>
                                <span class="info-value">{{ ucfirst($student->language_preference) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        @if($student->address || $student->emergency_contact || $student->emergency_phone)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-address-book me-2"></i>
                        Información de Contacto
                    </h5>
                </div>
                <div class="card-body">
                    @if($student->address)
                        <div class="info-item">
                            <label class="info-label">Dirección:</label>
                            <span class="info-value">{{ $student->address }}</span>
                        </div>
                    @endif
                    @if($student->emergency_contact)
                        <div class="info-item">
                            <label class="info-label">Contacto de Emergencia:</label>
                            <span class="info-value">{{ $student->emergency_contact }}</span>
                        </div>
                    @endif
                    @if($student->emergency_phone)
                        <div class="info-item">
                            <label class="info-label">Teléfono de Emergencia:</label>
                            <span class="info-value">{{ $student->emergency_phone }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Account Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-cog me-2"></i>
                    Información de Cuenta
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">Estado de la Cuenta:</label>
                            <span class="info-status status-{{ $student->is_active ? 'active' : 'inactive' }}">
                                {{ $student->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Fecha de Registro:</label>
                            <span class="info-value">{{ \Carbon\Carbon::parse($student->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">Última Actualización:</label>
                            <span class="info-value">{{ \Carbon\Carbon::parse($student->updated_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Rol:</label>
                            <span class="info-value">Estudiante</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js-link')
<script>
function editProfile() {
    alert('Función de edición de perfil en desarrollo. Por favor contacta con la administración para actualizar tu información.');
}
</script>
@endpush
