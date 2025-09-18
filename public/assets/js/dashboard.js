/**
 * PRODOCET LMS - Dashboard JavaScript
 * Author: Assistant
 * Description: Dashboard-specific functionality including charts, calendar, and data tables
 */

// Global chart instances
window.attendanceChart = null;
window.gradeChart = null;
window.miniCalendar = null;

// Dashboard data (in a real app, this would come from API)
const DASHBOARD_DATA = {
    attendance: {
        weekly: [85, 92, 88, 94, 89, 91, 87],
        monthly: [89, 91, 87, 93, 88, 92, 90, 89, 94, 87, 91, 89],
        quarterly: [89, 91, 88, 92]
    },
    grades: {
        distribution: [
            { label: 'Excelente (40-50)', value: 25, color: '#10b981' },
            { label: 'Bueno (30-39)', value: 35, color: '#3b82f6' },
            { label: 'Regular (20-29)', value: 28, color: '#f59e0b' },
            { label: 'Necesita Mejora (0-19)', value: 12, color: '#ef4444' }
        ]
    },
    recentActivity: [
        { time: '10:30', user: 'Prof. García', action: 'Asistencia registrada', details: 'Inglés Intermedio B1', type: 'success' },
        { time: '09:45', user: 'Ana Martínez', action: 'Nuevo registro', details: 'Estudiante - Francés A2', type: 'info' },
        { time: '09:15', user: 'Administrador', action: 'Clase cancelada', details: 'Alemán B2 - Profesor enfermo', type: 'warning' },
        { time: '08:30', user: 'Prof. López', action: 'Calificaciones subidas', details: 'Italiano A1 - 8 estudiantes', type: 'primary' },
        { time: '08:00', user: 'María González', action: 'Pago procesado', details: 'Francés B1 - Mes de Enero', type: 'success' }
    ],
    upcomingClasses: [
        { date: new Date(), title: 'Inglés Básico A1', time: '14:00', teacher: 'Prof. García', students: 8 },
        { date: new Date(Date.now() + 86400000), title: 'Francés Intermedio B1', time: '10:00', teacher: 'Prof. Martín', students: 6 },
        { date: new Date(Date.now() + 2 * 86400000), title: 'Alemán Avanzado C1', time: '16:00', teacher: 'Prof. Schmidt', students: 4 },
        { date: new Date(Date.now() + 3 * 86400000), title: 'Italiano Básico A2', time: '11:00', teacher: 'Prof. Rossi', students: 7 }
    ]
};

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // if (window.location.pathname.includes('dashboard.html') || window.location.pathname === '/') {
        initializeDashboard();
    // }
});

/**
 * Initialize all dashboard components
 */
function initializeDashboard() {
    initializeCharts();
    initializeMiniCalendar();
    initializeDataTables();
    loadDashboardData();
    setupDashboardEventListeners();
    startDashboardUpdates();
}

/**
 * Initialize all charts
 */
function initializeCharts() {
    initializeAttendanceChart();
    initializeGradeChart();
}

/**
 * Initialize attendance trends chart
 */
function initializeAttendanceChart() {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;

    // Get current color scheme
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
    const secondaryColor = getComputedStyle(document.documentElement).getPropertyValue('--secondary-color').trim();

    window.attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Asistencia (%)',
                data: DASHBOARD_DATA.attendance.monthly,
                borderColor: primaryColor,
                backgroundColor: primaryColor + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: primaryColor,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: primaryColor,
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `Asistencia: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                y: {
                    min: 0,
                    max: 100,
                    grid: {
                        color: '#f3f4f6',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBorderWidth: 3
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

/**
 * Initialize grade distribution chart
 */
function initializeGradeChart() {
    const ctx = document.getElementById('gradeChart');
    if (!ctx) return;

    window.gradeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: DASHBOARD_DATA.grades.distribution.map(item => item.label),
            datasets: [{
                data: DASHBOARD_DATA.grades.distribution.map(item => item.value),
                backgroundColor: DASHBOARD_DATA.grades.distribution.map(item => item.color),
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        color: '#6b7280'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed / total) * 100);
                            return `${context.label}: ${percentage}%`;
                        }
                    }
                }
            },
            cutout: '60%',
            elements: {
                arc: {
                    borderWidth: 0
                }
            }
        }
    });
}

/**
 * Initialize mini calendar
 */
function initializeMiniCalendar() {
    const calendarEl = document.getElementById('miniCalendar');
    if (!calendarEl) return;

    window.miniCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 300,
        headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },
        locale: 'es',
        firstDay: 1, // Monday
        events: generateCalendarEvents(),
        eventDisplay: 'block',
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            openFullCalendar(info.dateStr);
        },
        eventDidMount: function(info) {
            // Add custom styling
            info.el.style.fontSize = '10px';
            info.el.style.padding = '1px 4px';
        }
    });

    window.miniCalendar.render();
}

/**
 * Generate calendar events
 */
function generateCalendarEvents() {
    const events = [];
    const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
    
    // Generate events for the next 30 days
    for (let i = 0; i < 30; i++) {
        const date = new Date();
        date.setDate(date.getDate() + i);
        
        // Skip weekends for regular classes
        if (date.getDay() === 0 || date.getDay() === 6) continue;
        
        // Add 2-4 random classes per day
        const numClasses = Math.floor(Math.random() * 3) + 2;
        for (let j = 0; j < numClasses; j++) {
            const hour = 9 + j * 2; // Classes at 9, 11, 13, 15
            const languages = ['Inglés', 'Francés', 'Alemán', 'Italiano', 'Portugués'];
            const levels = ['A1', 'A2', 'B1', 'B2', 'C1'];
            
            events.push({
                id: `class_${i}_${j}`,
                title: `${languages[j % languages.length]} ${levels[Math.floor(Math.random() * levels.length)]}`,
                start: new Date(date.getFullYear(), date.getMonth(), date.getDate(), hour, 0),
                end: new Date(date.getFullYear(), date.getMonth(), date.getDate(), hour + 1, 30),
                color: colors[j % colors.length],
                extendedProps: {
                    teacher: `Prof. ${['García', 'Martín', 'López', 'Schmidt', 'Rossi'][j % 5]}`,
                    students: Math.floor(Math.random() * 8) + 4,
                    room: `Aula ${j + 1}`
                }
            });
        }
    }
    
    return events;
}

/**
 * Initialize data tables
 */
function initializeDataTables() {
    // Initialize recent activity table if it exists
    const activityTable = document.getElementById('recentActivityTable');
    if (activityTable && $.fn.DataTable) {
        $(activityTable).DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            language: {
                emptyTable: "No hay actividad reciente"
            }
        });
    }
}

/**
 * Load dashboard data and update UI
 */
function loadDashboardData() {
    // Update statistics cards with animation
    animateStatNumbers();
    
    // Update recent activity table
    updateRecentActivityTable();
    
    // Update alerts
    updateAlertsSection();
}

/**
 * Animate statistics numbers
 */
function animateStatNumbers() {
    const statNumbers = document.querySelectorAll('.stats-number');
    
    statNumbers.forEach(element => {
        const target = parseInt(element.textContent);
        const isPercentage = element.textContent.includes('%');
        
        animateNumber(element, 0, target, 1500, isPercentage);
    });
}

/**
 * Animate a number from start to end
 */
function animateNumber(element, start, end, duration, isPercentage = false) {
    const startTime = performance.now();
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function (ease out cubic)
        const easeOut = 1 - Math.pow(1 - progress, 3);
        
        const current = Math.round(start + (end - start) * easeOut);
        element.textContent = current + (isPercentage ? '%' : '');
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

/**
 * Update recent activity table
 */
function updateRecentActivityTable() {
    const tableBody = document.querySelector('#recentActivityTable tbody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    DASHBOARD_DATA.recentActivity.forEach(activity => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${activity.time}</td>
            <td>${activity.user}</td>
            <td><span class="badge bg-${activity.type}">${activity.action}</span></td>
            <td>${activity.details}</td>
        `;
        tableBody.appendChild(row);
    });
}

/**
 * Update alerts section
 */
function updateAlertsSection() {
    // This would typically fetch real alerts from an API
    // For now, we'll use the existing static alerts
}

/**
 * Setup dashboard-specific event listeners
 */
function setupDashboardEventListeners() {
    // Attendance chart period selector
    const attendanceSelect = document.querySelector('.card:has(#attendanceChart) select');
    if (attendanceSelect) {
        attendanceSelect.addEventListener('change', function() {
            updateAttendanceChart(this.value);
        });
    }
}

/**
 * Update attendance chart based on selected period
 */
function updateAttendanceChart(period) {
    if (!window.attendanceChart) return;
    
    let labels, data;
    
    switch (period) {
        case 'week':
            labels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
            data = DASHBOARD_DATA.attendance.weekly;
            break;
        case 'quarter':
            labels = ['Q1', 'Q2', 'Q3', 'Q4'];
            data = DASHBOARD_DATA.attendance.quarterly;
            break;
        case 'month':
        default:
            labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            data = DASHBOARD_DATA.attendance.monthly;
            break;
    }
    
    window.attendanceChart.data.labels = labels;
    window.attendanceChart.data.datasets[0].data = data;
    window.attendanceChart.update('active');
    
    showNotification(`Gráfico actualizado: ${period === 'week' ? 'Esta Semana' : period === 'month' ? 'Este Mes' : 'Este Trimestre'}`, 'info', 2000);
}

/**
 * Update chart colors when color scheme changes
 */
function updateChartColors() {
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
    const secondaryColor = getComputedStyle(document.documentElement).getPropertyValue('--secondary-color').trim();
    
    // Update attendance chart colors
    if (window.attendanceChart) {
        window.attendanceChart.data.datasets[0].borderColor = primaryColor;
        window.attendanceChart.data.datasets[0].backgroundColor = primaryColor + '20';
        window.attendanceChart.data.datasets[0].pointBackgroundColor = primaryColor;
        window.attendanceChart.options.plugins.tooltip.borderColor = primaryColor;
        window.attendanceChart.update();
    }
    
    // Update grade chart colors with new color scheme
    if (window.gradeChart) {
        const newColors = [
            secondaryColor,    // Excelente
            primaryColor,      // Bueno
            '#f59e0b',        // Regular
            '#ef4444'         // Necesita Mejora
        ];
        
        window.gradeChart.data.datasets[0].backgroundColor = newColors;
        window.gradeChart.update();
    }
}

/**
 * Start dashboard auto-updates
 */
function startDashboardUpdates() {
    // Update dashboard data every 5 minutes
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            refreshDashboardData();
        }
    }, 5 * 60 * 1000);
    
    // Update time displays every minute
    setInterval(updateTimeDisplays, 60 * 1000);
}

/**
 * Refresh dashboard data
 */
function refreshDashboardData() {
    // In a real application, this would fetch fresh data from the API
    console.log('Refreshing dashboard data...');
    
    // Simulate data changes
    DASHBOARD_DATA.attendance.monthly = DASHBOARD_DATA.attendance.monthly.map(val => 
        Math.max(0, Math.min(100, val + (Math.random() - 0.5) * 4))
    );
    
    // Update charts
    if (window.attendanceChart) {
        window.attendanceChart.data.datasets[0].data = DASHBOARD_DATA.attendance.monthly;
        window.attendanceChart.update('none');
    }
}

/**
 * Update time displays
 */
function updateTimeDisplays() {
    const timeElements = document.querySelectorAll('.time-display');
    const now = new Date();
    
    timeElements.forEach(element => {
        element.textContent = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
    });
}

/**
 * Dashboard action functions
 */

// Show event details
function showEventDetails(event) {
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div class="modal fade" id="eventDetailsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${event.title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Profesor:</strong> ${event.extendedProps.teacher}</p>
                        <p><strong>Estudiantes:</strong> ${event.extendedProps.students}</p>
                        <p><strong>Aula:</strong> ${event.extendedProps.room}</p>
                        <p><strong>Horario:</strong> ${event.start.toLocaleString('es-ES')}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary">Ver Detalles Completos</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal.querySelector('.modal'));
    bsModal.show();
    
    // Clean up modal after hiding
    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

// Open full calendar
window.openFullCalendar = function(date = null) {
    const url = date ? `calendar.html?date=${date}` : 'calendar.html';
    window.location.href = url;
};

// Quick action functions
window.openNewGroupModal = function() {
    showNotification('Abriendo formulario de nuevo grupo...', 'info');
    setTimeout(() => {
        window.location.href = 'groups.html?action=new';
    }, 1000);
};

window.openStudentModal = function() {
    showNotification('Abriendo registro de estudiante...', 'info');
    setTimeout(() => {
        window.location.href = 'students.html?action=new';
    }, 1000);
};

window.markAttendance = function() {
    showNotification('Redirigiendo a toma de asistencia...', 'info');
    setTimeout(() => {
        window.location.href = 'attendance.html';
    }, 1000);
};

window.generateReport = function() {
    showNotification('Preparando generador de reportes...', 'info');
    setTimeout(() => {
        window.location.href = 'reports.html';
    }, 1000);
};

/**
 * Helper functions
 */

// Get random data for simulations
function getRandomData(min, max, count) {
    return Array.from({ length: count }, () => 
        Math.floor(Math.random() * (max - min + 1)) + min
    );
}

// Format numbers with locale
function formatNumber(number) {
    return new Intl.NumberFormat('es-ES').format(number);
}

// Format percentages
function formatPercentage(number, decimals = 0) {
    return `${number.toFixed(decimals)}%`;
}