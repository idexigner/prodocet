
<!DOCTYPE html>
<html lang="es">
<head>
    @include('layouts.head')
    
</head>
<body>
    @include('layouts.top-header')

    <!-- Main Container -->
    <div class="main-container">
        <!-- Mobile sidebar backdrop -->
        <div class="sidebar-backdrop" onclick="toggleSidebar()"></div>
       
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="main-content">
            @yield('main-section')
        </main>
    </div>

    <!-- Color Customization Modal -->
    <div class="modal fade" id="colorCustomizationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-palette me-2"></i>
                        Personalizar Colores del Sistema
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="color-option">
                                <label class="form-label">Color Primario</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="primaryColorPicker" value="#4f46e5">
                                    <input type="text" class="form-control" id="primaryColorText" value="#4f46e5">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="color-option">
                                <label class="form-label">Color Secundario</label>
                                <div class="color-picker-group">
                                    <input type="color" class="form-control form-control-color" id="secondaryColorPicker" value="#10b981">
                                    <input type="text" class="form-control" id="secondaryColorText" value="#10b981">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="color-presets mt-4">
                        <h6>Esquemas Predefinidos</h6>
                        <div class="preset-options">
                            <div class="preset-option" data-primary="#4f46e5" data-secondary="#10b981">
                                <div class="preset-colors">
                                    <span style="background: #4f46e5;"></span>
                                    <span style="background: #10b981;"></span>
                                </div>
                                <span>Azul/Verde</span>
                            </div>
                            <div class="preset-option" data-primary="#dc2626" data-secondary="#f59e0b">
                                <div class="preset-colors">
                                    <span style="background: #dc2626;"></span>
                                    <span style="background: #f59e0b;"></span>
                                </div>
                                <span>Rojo/Amarillo</span>
                            </div>
                            <div class="preset-option" data-primary="#7c3aed" data-secondary="#ec4899">
                                <div class="preset-colors">
                                    <span style="background: #7c3aed;"></span>
                                    <span style="background: #ec4899;"></span>
                                </div>
                                <span>Púrpura/Rosa</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="color-preview mt-4">
                        <h6>Vista Previa</h6>
                        <div class="preview-card">
                            <div class="preview-header">
                                <span>Encabezado del Sistema</span>
                                <button class="btn btn-sm preview-btn-primary">Botón Primario</button>
                            </div>
                            <div class="preview-content">
                                <div class="preview-sidebar">
                                    <div class="preview-nav-item active">Dashboard</div>
                                    <div class="preview-nav-item">Grupos</div>
                                </div>
                                <div class="preview-main">
                                    <div class="preview-stats">
                                        <div class="preview-stat">
                                            <span class="preview-number">24</span>
                                            <span>Grupos</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="applyColorScheme()">Aplicar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
