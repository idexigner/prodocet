<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- DataTables Language Files -->
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/{{ app()->getLocale() == 'es' ? 'Spanish' : 'English' }}.json"></script>

<!-- Common DataTable Functions -->
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
            processing: "{{ __('common.processing') }}",
            lengthMenu: "{{ __('common.show_entries') }}",
            zeroRecords: "{{ __('common.no_results') }}",
            emptyTable: "{{ __('common.no_data') }}",
            info: "{{ __('common.showing_entries') }}",
            infoEmpty: "{{ __('common.showing_entries_empty') }}",
            infoFiltered: "{{ __('common.filtered_entries') }}",
            search: "{{ __('common.search') }}:",
            paginate: {
                first: "{{ __('common.first') }}",
                last: "{{ __('common.last') }}",
                next: "{{ __('common.next') }}",
                previous: "{{ __('common.previous') }}"
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> {{ __("common.excel") }}',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> {{ __("common.pdf") }}',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> {{ __("common.print") }}',
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
                processing: "{{ __('common.processing') }}",
                lengthMenu: "{{ __('common.show_entries') }}",
                zeroRecords: "{{ __('common.no_results') }}",
                emptyTable: "{{ __('common.no_data') }}",
                info: "{{ __('common.showing_entries') }}",
                search: "{{ __('common.search') }}:",
                paginate: {
                    first: "{{ __('common.first') }}",
                    last: "{{ __('common.last') }}",
                    next: "{{ __('common.next') }}",
                    previous: "{{ __('common.previous') }}"
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
function showLoading(element, text = '{{ __("common.loading") }}') {
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
                showErrorMessage('{{ __("common.request_error") }}: ' + error);
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
            showLoading(submitBtn[0], '{{ __("common.processing") }}');
        }
    });
});
</script>
