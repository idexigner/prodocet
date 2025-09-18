/**
 * PRODOCET LMS - Admin Panel JavaScript
 * Author: Assistant
 * Description: Core JavaScript functionality for the admin panel
 */

// Global configuration and state management
const ADMIN_CONFIG = {
    sidebarCollapsed: false,
    colorScheme: {
        primary: '#4f46e5',
        secondary: '#10b981',
        accent: '#f59e0b'
    },
    defaultSettings: {
        theme: 'light',
        language: 'es',
        notifications: true,
        autoSave: true
    }
};

// Initialize admin panel when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeAdminPanel();
    setupEventListeners();
    loadUserPreferences();
    // checkAuthentication();
});

/**
 * Initialize the admin panel components
 */
function initializeAdminPanel() {
    // Setup sidebar toggle functionality
    setupSidebarToggle();
    
    // Initialize tooltips and popovers
    initializeBootstrapComponents();
    
    // Setup responsive behavior
    setupResponsiveBehavior();
    
    // Load saved color scheme
    loadColorScheme();
    
    // Initialize charts if on dashboard
    // if (window.location.pathname.includes('dashboard.html') || window.location.pathname === '/') {
        if (typeof initializeDashboard === 'function') {
            initializeDashboard();
        }
    // }
}

/**
 * Setup all event listeners for the admin panel
 */
function setupEventListeners() {
    // Sidebar toggle events
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarCollapseBtn = document.querySelector('.sidebar-collapse-btn');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', toggleSidebarCollapse);
    }
    
    // Color customization events
    setupColorCustomizationEvents();
    
    // Global keyboard shortcuts
    document.addEventListener('keydown', handleKeyboardShortcuts);
    
    // Auto-save functionality
    setupAutoSave();
    
    // Notification events
    setupNotificationEvents();
    
    // Window resize events
    window.addEventListener('resize', handleWindowResize);
    
    // Close sidebar on backdrop click
    const backdrop = document.querySelector('.sidebar-backdrop');
    if (backdrop) {
        backdrop.addEventListener('click', function(e) {
            // Only close if clicking directly on backdrop, not on sidebar
            if (e.target === backdrop && window.innerWidth <= 991.98) {
                toggleSidebar();
            }
        });
    }
}

/**
 * Setup sidebar toggle functionality
 */
function setupSidebarToggle() {
    const sidebar = document.querySelector('.sidebar');
    const mainHeader = document.querySelector('.main-header');
    const mainContent = document.querySelector('.main-content');
    
    // Load saved sidebar state
    const savedState = localStorage.getItem('sidebar_collapsed');
    if (savedState === 'true') {
        ADMIN_CONFIG.sidebarCollapsed = true;
        sidebar?.classList.add('collapsed');
        
        // Apply collapsed state to header and content
        if (mainHeader) {
            mainHeader.style.left = 'var(--sidebar-collapsed-width)';
        }
        if (mainContent) {
            mainContent.style.marginLeft = 'var(--sidebar-collapsed-width)';
        }
    }
}

/**
 * Toggle sidebar visibility (mobile)
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');
    
    if (sidebar) {
        // On mobile, toggle show/hide
        if (window.innerWidth <= 991.98) {
            const isShowing = sidebar.classList.contains('show');
            
            if (isShowing) {
                // Hide sidebar
                sidebar.classList.remove('show');
                if (backdrop) {
                    backdrop.classList.remove('show');
                }
                // Prevent body scroll when sidebar is open
                document.body.style.overflow = '';
            } else {
                // Show sidebar
                sidebar.classList.add('show');
                if (backdrop) {
                    backdrop.classList.add('show');
                }
                // Prevent body scroll when sidebar is open
                document.body.style.overflow = 'hidden';
            }
        } else {
            // On desktop, toggle collapse
            toggleSidebarCollapse();
        }
    }
}

/**
 * Toggle sidebar collapsed state (desktop)
 */
function toggleSidebarCollapse() {
    const sidebar = document.querySelector('.sidebar');
    const mainHeader = document.querySelector('.main-header');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebar) {
        ADMIN_CONFIG.sidebarCollapsed = !ADMIN_CONFIG.sidebarCollapsed;
        sidebar.classList.toggle('collapsed');
        
        // Update main header and content positioning
        if (mainHeader) {
            if (ADMIN_CONFIG.sidebarCollapsed) {
                mainHeader.style.left = 'var(--sidebar-collapsed-width)';
            } else {
                mainHeader.style.left = 'var(--sidebar-width)';
            }
        }
        
        if (mainContent) {
            if (ADMIN_CONFIG.sidebarCollapsed) {
                mainContent.style.marginLeft = 'var(--sidebar-collapsed-width)';
            } else {
                mainContent.style.marginLeft = 'var(--sidebar-width)';
            }
        }
        
        // Save state to localStorage
        localStorage.setItem('sidebar_collapsed', ADMIN_CONFIG.sidebarCollapsed);
        
        // Trigger resize event for charts
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
        }, 300);
    }
}

/**
 * Initialize Bootstrap components
 */
function initializeBootstrapComponents() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Setup responsive behavior
 */
function setupResponsiveBehavior() {
    const mediaQuery = window.matchMedia('(max-width: 991.98px)');
    
    function handleBreakpointChange(e) {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            if (e.matches) {
                // Mobile: Hide sidebar by default
                sidebar.classList.remove('show');
            } else {
                // Desktop: Show sidebar
                sidebar.classList.remove('show');
            }
        }
    }
    
    // Initial check
    handleBreakpointChange(mediaQuery);
    
    // Listen for changes
    mediaQuery.addListener(handleBreakpointChange);
}

/**
 * Handle window resize events
 */
function handleWindowResize() {
    const sidebar = document.querySelector('.sidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');
    
    // Close mobile sidebar on desktop
    if (window.innerWidth > 991.98) {
        if (sidebar) {
            sidebar.classList.remove('show');
        }
        if (backdrop) {
            backdrop.classList.remove('show');
        }
        // Restore body scroll
        document.body.style.overflow = '';
    }
    
    // Trigger chart resize if charts exist
    if (window.attendanceChart) {
        window.attendanceChart.resize();
    }
    if (window.gradeChart) {
        window.gradeChart.resize();
    }
}

/**
 * Setup color customization functionality
 */
function setupColorCustomizationEvents() {
    // Color picker events
    const primaryPicker = document.getElementById('primaryColorPicker');
    const secondaryPicker = document.getElementById('secondaryColorPicker');
    const primaryText = document.getElementById('primaryColorText');
    const secondaryText = document.getElementById('secondaryColorText');
    
    if (primaryPicker && primaryText) {
        primaryPicker.addEventListener('input', function() {
            primaryText.value = this.value;
            updateColorPreview();
        });
        
        primaryText.addEventListener('input', function() {
            if (isValidHexColor(this.value)) {
                primaryPicker.value = this.value;
                updateColorPreview();
            }
        });
    }
    
    if (secondaryPicker && secondaryText) {
        secondaryPicker.addEventListener('input', function() {
            secondaryText.value = this.value;
            updateColorPreview();
        });
        
        secondaryText.addEventListener('input', function() {
            if (isValidHexColor(this.value)) {
                secondaryPicker.value = this.value;
                updateColorPreview();
            }
        });
    }
    
    // Preset color options
    const presetOptions = document.querySelectorAll('.preset-option');
    presetOptions.forEach(option => {
        option.addEventListener('click', function() {
            const primary = this.dataset.primary;
            const secondary = this.dataset.secondary;
            
            if (primaryPicker && primaryText) {
                primaryPicker.value = primary;
                primaryText.value = primary;
            }
            
            if (secondaryPicker && secondaryText) {
                secondaryPicker.value = secondary;
                secondaryText.value = secondary;
            }
            
            // Update active preset
            presetOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            updateColorPreview();
        });
    });
}

/**
 * Update color preview in the customization modal
 */
function updateColorPreview() {
    const primaryColor = document.getElementById('primaryColorPicker')?.value || '#4f46e5';
    const secondaryColor = document.getElementById('secondaryColorPicker')?.value || '#10b981';
    
    // Update preview elements
    const previewHeader = document.querySelector('.preview-header');
    const previewBtnPrimary = document.querySelector('.preview-btn-primary');
    const previewNavActive = document.querySelector('.preview-nav-item.active');
    const previewNumber = document.querySelector('.preview-number');
    
    if (previewHeader) {
        previewHeader.style.background = primaryColor;
    }
    
    if (previewBtnPrimary) {
        previewBtnPrimary.style.background = secondaryColor;
    }
    
    if (previewNavActive) {
        previewNavActive.style.background = primaryColor;
    }
    
    if (previewNumber) {
        previewNumber.style.color = primaryColor;
    }
}

/**
 * Apply the selected color scheme to the entire system
 */
function applyColorScheme() {
    const primaryColor = document.getElementById('primaryColorPicker')?.value || '#4f46e5';
    const secondaryColor = document.getElementById('secondaryColorPicker')?.value || '#10b981';
    
    // Update CSS custom properties
    const root = document.documentElement;
    root.style.setProperty('--primary-color', primaryColor);
    root.style.setProperty('--primary-dark', darkenColor(primaryColor, 20));
    root.style.setProperty('--primary-light', lightenColor(primaryColor, 20));
    root.style.setProperty('--primary-lighter', lightenColor(primaryColor, 40));
    root.style.setProperty('--primary-lightest', lightenColor(primaryColor, 80));
    
    root.style.setProperty('--secondary-color', secondaryColor);
    root.style.setProperty('--secondary-dark', darkenColor(secondaryColor, 20));
    root.style.setProperty('--secondary-light', lightenColor(secondaryColor, 20));
    
    // Save to localStorage
    const colorScheme = {
        primary: primaryColor,
        secondary: secondaryColor,
        timestamp: Date.now()
    };
    
    localStorage.setItem('prodocet_color_scheme', JSON.stringify(colorScheme));
    ADMIN_CONFIG.colorScheme = colorScheme;
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('colorCustomizationModal'));
    if (modal) {
        modal.hide();
    }
    
    // Show success message
    showNotification('Esquema de colores aplicado exitosamente', 'success');
    
    // Update charts if they exist
    if (window.attendanceChart || window.gradeChart) {
        setTimeout(updateChartColors, 300);
    }
}

/**
 * Load saved color scheme from localStorage
 */
function loadColorScheme() {
    const savedScheme = localStorage.getItem('prodocet_color_scheme');
    if (savedScheme) {
        try {
            const scheme = JSON.parse(savedScheme);
            ADMIN_CONFIG.colorScheme = scheme;
            
            // Apply saved colors
            const root = document.documentElement;
            root.style.setProperty('--primary-color', scheme.primary);
            root.style.setProperty('--primary-dark', darkenColor(scheme.primary, 20));
            root.style.setProperty('--primary-light', lightenColor(scheme.primary, 20));
            root.style.setProperty('--primary-lighter', lightenColor(scheme.primary, 40));
            root.style.setProperty('--primary-lightest', lightenColor(scheme.primary, 80));
            
            root.style.setProperty('--secondary-color', scheme.secondary);
            root.style.setProperty('--secondary-dark', darkenColor(scheme.secondary, 20));
            root.style.setProperty('--secondary-light', lightenColor(scheme.secondary, 20));
            
            // Update color pickers if modal exists
            setTimeout(() => {
                const primaryPicker = document.getElementById('primaryColorPicker');
                const secondaryPicker = document.getElementById('secondaryColorPicker');
                const primaryText = document.getElementById('primaryColorText');
                const secondaryText = document.getElementById('secondaryColorText');
                
                if (primaryPicker && primaryText) {
                    primaryPicker.value = scheme.primary;
                    primaryText.value = scheme.primary;
                }
                
                if (secondaryPicker && secondaryText) {
                    secondaryPicker.value = scheme.secondary;
                    secondaryText.value = scheme.secondary;
                }
            }, 100);
            
        } catch (error) {
            console.error('Error loading color scheme:', error);
        }
    }
}

/**
 * Setup notification events and management
 */
function setupNotificationEvents() {
    // Mark all notifications as read
    window.markAllAsRead = function() {
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.style.opacity = '0.6';
        });
        
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.style.display = 'none';
        }
        
        showNotification('Todas las notificaciones marcadas como leídas', 'info');
    };
}

/**
 * Setup auto-save functionality for forms
 */
function setupAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');
    
    forms.forEach(form => {
        const formId = form.id || 'form_' + Math.random().toString(36).substr(2, 9);
        let saveTimeout;
        
        // Listen for form changes
        form.addEventListener('input', function(e) {
            clearTimeout(saveTimeout);
            
            saveTimeout = setTimeout(() => {
                saveFormData(formId, form);
            }, 2000); // Save after 2 seconds of inactivity
        });
        
        // Load saved data on page load
        loadFormData(formId, form);
    });
}

/**
 * Save form data to localStorage
 */
function saveFormData(formId, form) {
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    localStorage.setItem(`autosave_${formId}`, JSON.stringify({
        data: data,
        timestamp: Date.now()
    }));
    
    showNotification('Datos guardados automáticamente', 'info', 2000);
}

/**
 * Load form data from localStorage
 */
function loadFormData(formId, form) {
    const savedData = localStorage.getItem(`autosave_${formId}`);
    
    if (savedData) {
        try {
            const { data, timestamp } = JSON.parse(savedData);
            
            // Only load data if it's less than 24 hours old
            if (Date.now() - timestamp < 24 * 60 * 60 * 1000) {
                Object.entries(data).forEach(([key, value]) => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field) {
                        field.value = value;
                    }
                });
            }
        } catch (error) {
            console.error('Error loading form data:', error);
        }
    }
}

/**
 * Handle global keyboard shortcuts
 */
function handleKeyboardShortcuts(e) {
    // Ctrl+Shift+S: Toggle sidebar
    if (e.ctrlKey && e.shiftKey && e.key === 'S') {
        e.preventDefault();
        toggleSidebarCollapse();
    }
    
    // Ctrl+Shift+C: Open color customization
    if (e.ctrlKey && e.shiftKey && e.key === 'C') {
        e.preventDefault();
        openColorCustomization();
    }
    
    // Ctrl+Shift+N: Open notifications
    if (e.ctrlKey && e.shiftKey && e.key === 'N') {
        e.preventDefault();
        const notificationToggle = document.querySelector('.notification-toggle');
        if (notificationToggle) {
            notificationToggle.click();
        }
    }
    
    // Escape: Close all dropdowns and modals
    if (e.key === 'Escape') {
        // Close dropdowns
        const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
        openDropdowns.forEach(dropdown => {
            const toggle = dropdown.previousElementSibling;
            if (toggle) {
                bootstrap.Dropdown.getInstance(toggle)?.hide();
            }
        });
    }
}

/**
 * Check user authentication and redirect if needed
 */
function checkAuthentication() {
    // const session = getStoredSession();
    
    // if (!session || session.expiry < Date.now()) {
    //     // Session expired or doesn't exist
    //     showNotification('Sesión expirada. Redirigiendo al login...', 'warning');
    //     setTimeout(() => {
    //         // window.location.href = 'index.html';
    //     }, 2000);
    //     return false;
    // }
    
    // // Update user info in header
    // updateUserInfo(session.user);
    return true;
}

/**
 * Get stored user session
 */
function getStoredSession() {
    const sessionData = localStorage.getItem('prodocet_session') || sessionStorage.getItem('prodocet_session');
    return sessionData ? JSON.parse(sessionData) : null;
}

/**
 * Update user information in the header
 */
function updateUserInfo(user) {
    const userNameElements = document.querySelectorAll('.user-name');
    const userEmailElement = document.querySelector('.user-email');
    const userAvatars = document.querySelectorAll('.user-avatar, .user-avatar-large');
    
    userNameElements.forEach(element => {
        element.textContent = user.name;
    });
    
    if (userEmailElement) {
        userEmailElement.textContent = user.email;
    }
    
    userAvatars.forEach(avatar => {
        avatar.src = user.avatar || `https://via.placeholder.com/48x48?text=${user.name.charAt(0)}`;
        avatar.alt = user.name;
    });
}

/**
 * Load user preferences from localStorage
 */
function loadUserPreferences() {
    const preferences = localStorage.getItem('prodocet_preferences');
    
    if (preferences) {
        try {
            const prefs = JSON.parse(preferences);
            Object.assign(ADMIN_CONFIG.defaultSettings, prefs);
        } catch (error) {
            console.error('Error loading preferences:', error);
        }
    }
}

/**
 * Save user preferences to localStorage
 */
function saveUserPreferences(preferences) {
    const current = JSON.parse(localStorage.getItem('prodocet_preferences') || '{}');
    const updated = { ...current, ...preferences };
    
    localStorage.setItem('prodocet_preferences', JSON.stringify(updated));
    Object.assign(ADMIN_CONFIG.defaultSettings, updated);
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'info', duration = 5000) {
    // Remove existing notifications
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-notification fade show`;
    toast.setAttribute('role', 'alert');
    
    const iconMap = {
        success: 'fas fa-check-circle text-success',
        warning: 'fas fa-exclamation-triangle text-warning',
        error: 'fas fa-times-circle text-danger',
        info: 'fas fa-info-circle text-info'
    };
    
    toast.innerHTML = `
        <div class="toast-header">
            <i class="${iconMap[type] || iconMap.info} me-2"></i>
            <strong class="me-auto">PRODOCET LMS</strong>
            <small class="text-muted">ahora</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize Bootstrap toast
    const bsToast = new bootstrap.Toast(toast, {
        delay: duration
    });
    
    bsToast.show();
    
    // Remove from DOM after hiding
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

/**
 * View all alerts function
 */
function viewAllAlerts() {
    // Create a modal to show all alerts
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'allAlertsModal';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-bell me-2"></i>
                        Todas las Alertas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert-list">
                        <div class="alert-item">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">5 sesiones restantes</div>
                                <div class="alert-text">Grupo Inglés Básico A1</div>
                                <div class="alert-time">Hace 2 horas</div>
                            </div>
                        </div>
                        <div class="alert-item">
                            <div class="alert-icon">
                                <i class="fas fa-user-plus text-info"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">Nuevo estudiante registrado</div>
                                <div class="alert-text">María González - Francés B1</div>
                                <div class="alert-time">Hace 4 horas</div>
                            </div>
                        </div>
                        <div class="alert-item">
                            <div class="alert-icon">
                                <i class="fas fa-calendar-times text-danger"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">Clase cancelada</div>
                                <div class="alert-text">Alemán Intermedio - Mañana 10:00</div>
                                <div class="alert-time">Ayer</div>
                            </div>
                        </div>
                        <div class="alert-item">
                            <div class="alert-icon">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">Recordatorio de clase</div>
                                <div class="alert-text">Francés B2 - Mañana 9:00</div>
                                <div class="alert-time">En 30 minutos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="configureAlerts()">
                        <i class="fas fa-cog me-2"></i>
                        Configurar Alertas
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Remove modal from DOM after it's hidden
    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

/**
 * Configure alerts function
 */
function configureAlerts() {
    showNotification('Función de configuración de alertas en desarrollo', 'info');
}

/**
 * Mark all notifications as read
 */
function markAllAsRead() {
    // Remove notification badges
    const badges = document.querySelectorAll('.notification-badge');
    badges.forEach(badge => {
        badge.style.display = 'none';
    });
    
    showNotification('Todas las notificaciones marcadas como leídas', 'success');
}

/**
 * Global action functions
 */

// Open color customization modal
window.openColorCustomization = function() {
    const modal = new bootstrap.Modal(document.getElementById('colorCustomizationModal'));
    modal.show();
    updateColorPreview();
};

// Open profile settings
window.openProfileSettings = function() {
    showNotification('Función de configuración de perfil en desarrollo', 'info');
};

// Open general settings
window.openSettings = function() {
    window.location.href = 'settings.html';
};

// Logout function
window.logout = function() {
    // Clear session data
    localStorage.removeItem('prodocet_session');
    sessionStorage.removeItem('prodocet_session');
    
    // Clear auto-save data
    Object.keys(localStorage).forEach(key => {
        if (key.startsWith('autosave_')) {
            localStorage.removeItem(key);
        }
    });
    
    showNotification('Cerrando sesión...', 'info');
    
    setTimeout(() => {
        window.location.href = 'index.html';
    }, 1500);
};

// Refresh dashboard
window.refreshDashboard = function() {
    showNotification('Actualizando dashboard...', 'info');
    
    // Add loading state
    const refreshBtn = document.querySelector('.content-actions .btn-primary');
    if (refreshBtn) {
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
        refreshBtn.disabled = true;
        
        setTimeout(() => {
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
            showNotification('Dashboard actualizado exitosamente', 'success');
            
            // Refresh charts if they exist
            if (window.attendanceChart) {
                window.attendanceChart.update();
            }
            if (window.gradeChart) {
                window.gradeChart.update();
            }
        }, 2000);
    }
};

// Export dashboard
window.exportDashboard = function() {
    showNotification('Preparando exportación...', 'info');
    
    setTimeout(() => {
        // In a real application, this would generate and download a report
        const data = {
            timestamp: new Date().toISOString(),
            stats: {
                activeGroups: 24,
                activeTeachers: 12,
                totalStudents: 156,
                averageAttendance: 89
            }
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `dashboard-export-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        showNotification('Dashboard exportado exitosamente', 'success');
    }, 1500);
};

/**
 * Utility functions
 */

// Check if a string is a valid hex color
function isValidHexColor(hex) {
    return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
}

// Darken a hex color by a percentage
function darkenColor(hex, percent) {
    const num = parseInt(hex.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) - amt;
    const G = (num >> 8 & 0x00FF) - amt;
    const B = (num & 0x0000FF) - amt;
    return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
}

// Lighten a hex color by a percentage
function lightenColor(hex, percent) {
    const num = parseInt(hex.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) + amt;
    const G = (num >> 8 & 0x00FF) + amt;
    const B = (num & 0x0000FF) + amt;
    return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
}

// Format date for display
function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        ...options
    };
    
    return new Intl.DateTimeFormat('es-ES', defaultOptions).format(new Date(date));
}

// Format number with thousands separator
function formatNumber(num) {
    return new Intl.NumberFormat('es-ES').format(num);
}

// Debounce function for performance optimization
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for performance optimization
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}