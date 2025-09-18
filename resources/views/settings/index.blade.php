@extends('layouts.app')

@section('title', 'Configuración')
@push('styles')
<style>
        .settings-card {
            background: #ffffff;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }
        
        .color-picker {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition-normal);
        }
        
        .color-picker:hover {
            transform: scale(1.1);
        }
        
        .color-picker.active {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-lightest);
        }
        
        .notification-item {
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            margin-bottom: 1rem;
            background: var(--background-secondary);
        }
        
        .backup-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            margin-bottom: 1rem;
            background: white;
        }
        
        .backup-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
        }
        
        .backup-success { background: var(--success-color); }
        .backup-warning { background: var(--warning-color); }
        .backup-error { background: var(--error-color); }
    </style>
@endpush
@section('content')

            <div class="content-header">
                <div class="content-title">
                    <h1>ConfiguraciÃ³n del Sistema</h1>
                    <p class="content-subtitle">Personaliza y administra la configuraciÃ³n del LMS</p>
                </div>
                <div class="content-actions">
                    <button class="btn btn-primary" onclick="saveAllSettings()">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                    <button class="btn btn-outline-secondary" onclick="resetToDefaults()">
                        <i class="fas fa-undo"></i>
                        Restaurar Predeterminados
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- General Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="settings-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-cog me-2"></i>
                                ConfiguraciÃ³n General
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre de la InstituciÃ³n</label>
                                <input type="text" class="form-control" value="Centro Europeo De Idiomas" id="institutionName">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Zona Horaria</label>
                                <select class="form-select" id="timezone">
                                    <option value="America/Santiago" selected>America/Santiago (GMT-3)</option>
                                    <option value="America/New_York">America/New_York (GMT-5)</option>
                                    <option value="America/Los_Angeles">America/Los_Angeles (GMT-8)</option>
                                    <option value="Europe/Madrid">Europe/Madrid (GMT+1)</option>
                                    <option value="Europe/London">Europe/London (GMT+0)</option>
                                    <option value="Europe/Paris">Europe/Paris (GMT+1)</option>
                                    <option value="Asia/Tokyo">Asia/Tokyo (GMT+9)</option>
                                    <option value="Australia/Sydney">Australia/Sydney (GMT+10)</option>
                                    <option value="UTC">UTC (GMT+0)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Idioma del Sistema</label>
                                <select class="form-select" id="language">
                                    <option value="es" selected>EspaÃ±ol</option>
                                    <option value="en">English</option>
                                    <option value="fr">FranÃ§ais</option>
                                    <option value="de">Deutsch</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Formato de Fecha</label>
                                <select class="form-select" id="dateFormat">
                                    <option value="DD/MM/YYYY" selected>DD/MM/YYYY</option>
                                    <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                                    <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Customization -->
                <div class="col-lg-6 mb-4">
                    <div class="settings-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-palette me-2"></i>
                                PersonalizaciÃ³n de Colores
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Color Principal</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="color-picker active" style="background: #4f46e5;" onclick="selectColor(this, '#4f46e5')"></div>
                                    <div class="color-picker" style="background: #10b981;" onclick="selectColor(this, '#10b981')"></div>
                                    <div class="color-picker" style="background: #f59e0b;" onclick="selectColor(this, '#f59e0b')"></div>
                                    <div class="color-picker" style="background: #ef4444;" onclick="selectColor(this, '#ef4444')"></div>
                                    <div class="color-picker" style="background: #8b5cf6;" onclick="selectColor(this, '#8b5cf6')"></div>
                                    <div class="color-picker" style="background: #06b6d4;" onclick="selectColor(this, '#06b6d4')"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tema</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeLight" checked>
                                    <label class="form-check-label" for="themeLight">
                                        <i class="fas fa-sun me-2"></i>Claro
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeDark">
                                    <label class="form-check-label" for="themeDark">
                                        <i class="fas fa-moon me-2"></i>Oscuro
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeAuto">
                                    <label class="form-check-label" for="themeAuto">
                                        <i class="fas fa-magic me-2"></i>AutomÃ¡tico
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="col-lg-6 mb-4">
                    <div class="settings-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-bell me-2"></i>
                                ConfiguraciÃ³n de Notificaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="notification-item">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                    <label class="form-check-label" for="emailNotifications">
                                        <strong>Notificaciones por Email</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Recibe notificaciones importantes por correo electrÃ³nico</small>
                            </div>
                            <div class="notification-item">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="pushNotifications" checked>
                                    <label class="form-check-label" for="pushNotifications">
                                        <strong>Notificaciones Push</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Recibe notificaciones en tiempo real en el navegador</small>
                            </div>
                            <div class="notification-item">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="smsNotifications">
                                    <label class="form-check-label" for="smsNotifications">
                                        <strong>Notificaciones SMS</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Recibe alertas importantes por mensaje de texto</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup & Security -->
                <div class="col-lg-6 mb-4">
                    <div class="settings-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-shield-alt me-2"></i>
                                Respaldo y Seguridad
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Frecuencia de Respaldo</label>
                                <select class="form-select" id="backupFrequency">
                                    <option value="daily">Diario</option>
                                    <option value="weekly" selected>Semanal</option>
                                    <option value="monthly">Mensual</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-outline-primary" onclick="createBackup()">
                                    <i class="fas fa-download me-2"></i>
                                    Crear Respaldo Manual
                                </button>
                            </div>
                            <div class="mb-3">
                                <h6>Respaldos Recientes</h6>
                                <div class="backup-item">
                                    <div class="backup-icon backup-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">Respaldo Completo</div>
                                        <small class="text-muted">15 Nov 2024 - 02:00 AM</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary">Descargar</button>
                                </div>
                                <div class="backup-item">
                                    <div class="backup-icon backup-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">Respaldo Completo</div>
                                        <small class="text-muted">08 Nov 2024 - 02:00 AM</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary">Descargar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
@endsection


@push('scripts')
<script>
        function selectColor(element, color) {
            // Remove active class from all color pickers
            document.querySelectorAll('.color-picker').forEach(picker => {
                picker.classList.remove('active');
            });
            
            // Add active class to selected color picker
            element.classList.add('active');
            
            // Update CSS custom property
            document.documentElement.style.setProperty('--primary-color', color);
            
            console.log('Color seleccionado:', color);
        }

        function saveAllSettings() {
            const settings = {
                institutionName: document.getElementById('institutionName').value,
                timezone: document.getElementById('timezone').value,
                language: document.getElementById('language').value,
                dateFormat: document.getElementById('dateFormat').value,
                emailNotifications: document.getElementById('emailNotifications').checked,
                pushNotifications: document.getElementById('pushNotifications').checked,
                smsNotifications: document.getElementById('smsNotifications').checked,
                backupFrequency: document.getElementById('backupFrequency').value
            };
            
            console.log('ConfiguraciÃ³n guardada:', settings);
            alert('ConfiguraciÃ³n guardada exitosamente');
        }

        function resetToDefaults() {
            if (confirm('Â¿EstÃ¡s seguro de que quieres restaurar la configuraciÃ³n predeterminada?')) {
                document.getElementById('institutionName').value = 'Centro Europeo De Idiomas';
                document.getElementById('timezone').value = 'America/Santiago';
                document.getElementById('language').value = 'es';
                document.getElementById('dateFormat').value = 'DD/MM/YYYY';
                document.getElementById('emailNotifications').checked = true;
                document.getElementById('pushNotifications').checked = true;
                document.getElementById('smsNotifications').checked = false;
                document.getElementById('backupFrequency').value = 'weekly';
                
                // Reset color
                selectColor(document.querySelector('.color-picker[style*="#4f46e5"]'), '#4f46e5');
                
                alert('ConfiguraciÃ³n restaurada a valores predeterminados');
            }
        }

        function createBackup() {
            alert('Creando respaldo del sistema...');
        }
    </script>
@endpush