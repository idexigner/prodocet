<!-- Common JavaScript Functions -->
<script>
// Initialize DataTable with common configuration
function initializeDataTable(tableId, ajaxUrl = null, columns = null, options = {}) {
    const defaultOptions = {
        responsive: true,
        processing: true,
        serverSide: ajaxUrl ? true : false,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún dato disponible en esta tabla",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            search: "Buscar:",
            url: "",
            thousands: ",",
            loadingRecords: "Cargando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm'
            }
        ]
    };

    // Add AJAX URL if provided
    if (ajaxUrl) {
        defaultOptions.ajax = {
            url: ajaxUrl,
            type: 'GET',
            dataSrc: 'data'
        };
    }

    // Add columns if provided
    if (columns) {
        defaultOptions.columns = columns;
    }

    // Merge default options with provided options
    const finalOptions = { ...defaultOptions, ...options };

    // Check if table exists before initializing
    if ($('#' + tableId).length === 0) {
        console.error('Table with ID "' + tableId + '" not found');
        return null;
    }

    try {
        return $('#' + tableId).DataTable(finalOptions);
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        return null;
    }
}

// Simple DataTable initialization (alternative)
function initializeSimpleDataTable(tableId) {
    if ($('#' + tableId).length === 0) {
        console.error('Table with ID "' + tableId + '" not found');
        return null;
    }

    try {
        return $('#' + tableId).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });
    } catch (error) {
        console.error('Error initializing simple DataTable:', error);
        return null;
    }
}

// Common form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Show loading state
function showLoading(element, text = 'Cargando...') {
    if (element) {
        const originalText = element.innerHTML;
        element.setAttribute('data-original-text', originalText);
        element.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${text}`;
        element.disabled = true;
    }
}

// Hide loading state
function hideLoading(element) {
    if (element) {
        const originalText = element.getAttribute('data-original-text');
        if (originalText) {
            element.innerHTML = originalText;
            element.removeAttribute('data-original-text');
        }
        element.disabled = false;
    }
}

// Show success message
function showSuccessMessage(message) {
    if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else {
        alert(message);
    }
}

// Legacy success handler (for compatibility)
function _handleSuccess(message) {
    showSuccessMessage(message);
}

// Show error message
function showErrorMessage(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert(message);
    }
}

// Show warning message
function showWarningMessage(message) {
    if (typeof toastr !== 'undefined') {
        toastr.warning(message);
    } else {
        alert(message);
    }
}

// Show info message
function showInfoMessage(message) {
    if (typeof toastr !== 'undefined') {
        toastr.info(message);
    } else {
        alert(message);
    }
}

// Confirm dialog
function confirmDialog(message, callback) {
    if (confirm(message)) {
        if (typeof callback === 'function') {
            callback();
        }
        return true;
    }
    return false;
}

// Legacy confirm delete function (for compatibility)
function _confirmDelete(options) {
    const message = options.message || '¿Estás seguro de que quieres eliminar este elemento?';
    const callback = options.callback || function() {};
    const deleteData = options.deleteData || {};
    const url = options.url;
    
    if (confirm(message)) {
        _ajaxRequest(url, 'DELETE', deleteData, function(response) {
            if (response.success) {
                callback(response);
            } else {
                showErrorMessage(response.message || 'Error al eliminar');
            }
        });
    }
}

// Format date for display
function formatDate(date, format = 'DD/MM/YYYY') {
    if (!date) return '';
    
    const d = new Date(date);
    if (isNaN(d.getTime())) return '';
    
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    
    return format.replace('DD', day).replace('MM', month).replace('YYYY', year);
}

// Format currency
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('es-CL', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Debounce function
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

// AJAX request helper
function makeAjaxRequest(url, method = 'GET', data = null, successCallback = null, errorCallback = null) {
    const options = {
        url: url,
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };

    if (data) {
        options.data = data;
    }

    $.ajax(options)
        .done(function(response) {
            if (successCallback) successCallback(response);
        })
        .fail(function(xhr, status, error) {
            if (errorCallback) {
                errorCallback(xhr, status, error);
            } else {
                showErrorMessage('Error en la solicitud: ' + error);
            }
        });
}

// Legacy AJAX request function (for compatibility)
function _ajaxRequest(url, method = 'GET', data = null, successCallback = null, errorCallback = null) {
    const options = {
        url: url,
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    };

    // Handle form data
    if (data) {
        if (data instanceof FormData) {
            options.data = data;
            options.processData = false;
            options.contentType = false;
        } else {
            options.data = data;
            options.contentType = 'application/json';
        }
    }

    $.ajax(options)
        .done(function(response) {
            if (successCallback) successCallback(response);
        })
        .fail(function(xhr, status, error) {
            if (errorCallback) {
                errorCallback(xhr, status, error);
            } else {
                showErrorMessage('Error en la solicitud: ' + error);
            }
        });
}

// Initialize tooltips
function initializeTooltips() {
    $('[data-bs-toggle="tooltip"]').tooltip();
}

// Initialize popovers
function initializePopovers() {
    $('[data-bs-toggle="popover"]').popover();
}

// Close modal
function closeModal(modalId) {
    $('#' + modalId).modal('hide');
}

// Open modal
function openModal(modalId) {
    $('#' + modalId).modal('show');
}

// Reset form
function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        // Remove validation classes
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => field.classList.remove('is-invalid'));
    }
}

// Language switching function
function changeLanguage(locale) {
    _ajaxRequest('/language/' + locale, 'POST', null, function(response) {
        if (response.success) {
            // Update the language text in header
            $('.language-text').text(locale.toUpperCase());
            
            // Update active language option
            $('.language-option').removeClass('active');
            $('.language-option[onclick*="' + locale + '"]').addClass('active');
            
            // Reload the page to apply new language
            window.location.reload();
        } else {
            showErrorMessage('Error al cambiar el idioma');
        }
    });
}

// Initialize common functionality when document is ready
$(document).ready(function() {
    // Initialize tooltips and popovers
    initializeTooltips();
    initializePopovers();
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
    
    // Handle form submissions with loading states
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        if (submitBtn.length) {
            showLoading(submitBtn[0], 'Procesando...');
        }
    });
});
</script>
