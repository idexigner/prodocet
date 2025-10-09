@extends('layouts.app')

@section('title', __('teachers.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('teachers.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('teachers.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('teachers.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('teachers.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="teachers-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('teachers.id') }}</th>
                                        <th>{{ __('teachers.full_name') }}</th>
                                        <th>{{ __('teachers.email') }}</th>
                                        <th>{{ __('teachers.phone') }}</th>
                                        <th>{{ __('teachers.document_id') }}</th>
                                        <th>{{ __('teachers.status') }}</th>
                                        <th>{{ __('teachers.created_at') }}</th>
                                        <th>{{ __('teachers.action') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="misc-wrapper">
                <h2 class="mb-2 mx-2">{{ __('teachers.access_denied_title') }}</h2>
                <p class="mb-4 mx-2">{{ __('teachers.access_denied_message') }}</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('teachers.back_to_dashboard') }}</a>
            </div>
        </div>
    @endif

    @if(_has_permission('teachers.create') || _has_permission('teachers.edit'))
        <!-- Add/Edit Teacher Modal -->
        <div class="modal fade" id="modal_create_form" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="create-form">
                        <div class="modal-header">
                            <h5 class='modal-title'>{{ __('teachers.add_new') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id">
                            
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="teacherTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info" aria-selected="true">
                                        <i class="fas fa-user"></i> {{ __('teachers.basic_info') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab" aria-controls="courses" aria-selected="false">
                                        <i class="fas fa-book"></i> {{ __('teachers.courses') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="availability-tab" data-bs-toggle="tab" data-bs-target="#availability" type="button" role="tab" aria-controls="availability" aria-selected="false">
                                        <i class="fas fa-calendar-alt"></i> {{ __('teacher_availability.title') }}
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content" id="teacherTabsContent">
                                <!-- Basic Info Tab -->
                                <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.first_name') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="first_name" placeholder="{{ __('teachers.first_name_placeholder') }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.last_name') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="last_name" placeholder="{{ __('teachers.last_name_placeholder') }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.email') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="email" name="email" placeholder="{{ __('teachers.email_placeholder') }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.password') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <input type="password" name="password" id="teacher_password" placeholder="{{ __('teachers.password_placeholder') }}" class="form-control" required>
                                                        <button class="btn btn-outline-secondary password-toggle-btn" type="button" id="toggle_teacher_password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn password-generator-btn" type="button" id="generate_teacher_password" title="Generate Password">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.confirm_password') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <input type="password" name="password_confirmation" id="teacher_password_confirmation" placeholder="{{ __('teachers.confirm_password_placeholder') }}" class="form-control" required>
                                                        <button class="btn btn-outline-secondary password-toggle-btn" type="button" id="toggle_teacher_password_confirmation">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.phone') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="phone" placeholder="{{ __('teachers.phone_placeholder') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.birth_date') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="date" name="birth_date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.address') }}</label>
                                                <div class="col-sm-8">
                                                    <textarea name="address" placeholder="{{ __('teachers.address_placeholder') }}" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.emergency_contact') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="emergency_contact" placeholder="{{ __('teachers.emergency_contact_placeholder') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.emergency_phone') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="emergency_phone" placeholder="{{ __('teachers.emergency_phone_placeholder') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.language_preference') }}</label>
                                                <div class="col-sm-8">
                                                    <select name="language_preference" class="form-select">
                                                        <option value="es">{{ __('teachers.spanish') }}</option>
                                                        <option value="en">{{ __('teachers.english') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('teachers.status') }}</label>
                                                <div class="col-sm-8">
                                                    <select name="is_active" class="form-select">
                                                        <option value="1">{{ __('teachers.status_active') }}</option>
                                                        <option value="0">{{ __('teachers.status_inactive') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Courses Tab -->
                                <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>{{ __('teachers.assigned_courses') }}</h6>
                                            <button type="button" class="btn btn-sm btn-primary" id="save-courses">
                                                <i class="fas fa-save"></i> {{ __('teachers.save_courses') }}
                                            </button>
                                        </div>
                                        
                                        <!-- Course Selection -->
                                        <div class="mb-3">
                                            <label for="teacher-courses" class="form-label">{{ __('teachers.select_courses') }}</label>
                                            <select id="teacher-courses" class="form-select" multiple size="8">
                                                <!-- Courses will be loaded dynamically -->
                                            </select>
                                            <small class="form-text text-muted">{{ __('teachers.courses_help_text') }}</small>
                                        </div>
                                        
                                        <!-- Assigned Courses List -->
                                        <div class="mt-3">
                                            <h6>{{ __('teachers.currently_assigned') }}</h6>
                                            <div id="assigned-courses-list" class="list-group">
                                                <!-- Assigned courses will be displayed here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Availability Tab -->
                                <div class="tab-pane fade" id="availability" role="tabpanel" aria-labelledby="availability-tab">
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>{{ __('teacher_availability.heading') }}</h6>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" id="select-all-availability">
                                                    <i class="fas fa-check-square"></i> {{ __('teacher_availability.select_all') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" id="clear-all-availability">
                                                    <i class="fas fa-square"></i> {{ __('teacher_availability.clear_all') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary" id="save-availability">
                                                    <i class="fas fa-save"></i> {{ __('teacher_availability.save_availability') }}
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Availability Table -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="availability-table">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>{{ __('teacher_availability.day') }}</th>
                                                        <th class="text-center">{{ __('teacher_availability.select_day') }}</th>
                                                        <th class="text-center">{{ __('teacher_availability.clear_day') }}</th>
                                                        <!-- Time slots will be added dynamically -->
                                                    </tr>
                                                </thead>
                                                <tbody id="availability-tbody">
                                                    <!-- Availability rows will be added dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Notes -->
                                        <div class="mt-3">
                                            <label for="availability-notes" class="form-label">{{ __('teacher_availability.notes') }}</label>
                                            <textarea id="availability-notes" class="form-control" rows="3" placeholder="{{ __('teacher_availability.notes_placeholder') }}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('teachers.close') }}</button>
                            <button type="button" data-url="{{ route('teachers.store') }}" id="create_form_btn" class="btn btn-primary">{{ __('teachers.submit') }}</button>
                            <button type="button" data-url="{{ route('teachers.update', ':id') }}" id="update_form_btn" class="btn btn-primary">{{ __('teachers.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('js-link')
<script>
$(document).ready(function() {
    // Initialize DataTable with Yajra
    var table = $('#teachers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('teachers.index') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id', visible: false },
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'document_id', name: 'document_id' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[6, 'desc']], // Order by created_at desc
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
            processing: "{{ __('teachers.processing') }}",
            lengthMenu: "{{ __('teachers.length_menu') }}",
            zeroRecords: "{{ __('teachers.zero_records') }}",
            emptyTable: "{{ __('teachers.empty_table') }}",
            info: "{{ __('teachers.info') }}",
            infoEmpty: "{{ __('teachers.info_empty') }}",
            infoFiltered: "{{ __('teachers.info_filtered') }}",
            search: "{{ __('teachers.search') }}",
            paginate: {
                first: "{{ __('teachers.first') }}",
                last: "{{ __('teachers.last') }}",
                next: "{{ __('teachers.next') }}",
                previous: "{{ __('teachers.previous') }}"
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> {{ __("teachers.export_excel") }}',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> {{ __("teachers.export_pdf") }}',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> {{ __("teachers.export_print") }}',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                }
            }
        ]
    });

    // Handle edit button click
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        // Open edit modal
        $("#modal_create_form .modal-title").text("{{ __('teachers.edit_teacher') }}");
        $("#create_form_btn").hide();
        $("#update_form_btn").show();
        
        // Reset to basic info tab
        $('#basic-info-tab').tab('show');
        
        // Load teacher data for editing
        loadTeacherData(id);
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('teachers.confirm_delete') }}")) {
            $.ajax({
                url: "{{ route('teachers.destroy', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('teachers.error_delete') }}");
                }
            });
        }
    });

    // Handle restore button click
    $(document).on('click', '.restore-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('teachers.confirm_restore') }}")) {
            $.ajax({
                url: "{{ route('teachers.restore', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('teachers.error_restore') }}");
                }
            });
        }
    });

    // Function to load teacher data for editing
    function loadTeacherData(id) {
        $.ajax({
            url: "{{ route('teachers.show', ['id' => '__id__']) }}".replace('__id__', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $("#modal_create_form").modal('show');

                    var teacher = response.data.teacher;
                    
                    // Populate form fields using name attributes
                    $('input[name="id"]').val(teacher.id);
                    currentTeacherId = teacher.id;
                    $('input[name="first_name"]').val(teacher.first_name);
                    $('input[name="last_name"]').val(teacher.last_name);
                    $('input[name="email"]').val(teacher.email);
                    $('input[name="phone"]').val(teacher.phone);
                    $('input[name="document_id"]').val(teacher.document_id);
                    $('input[name="passport_number"]').val(teacher.passport_number);
                    // Format date for HTML date input (YYYY-MM-DD)
                    if (teacher.birth_date) {
                        const birthDate = new Date(teacher.birth_date);
                        const formattedDate = birthDate.toISOString().split('T')[0];
                        $('input[name="birth_date"]').val(formattedDate);
                    } else {
                        $('input[name="birth_date"]').val('');
                    }
                    $('textarea[name="address"]').val(teacher.address);
                    $('input[name="emergency_contact"]').val(teacher.emergency_contact);
                    $('input[name="emergency_phone"]').val(teacher.emergency_phone);
                    $('select[name="language_preference"]').val(teacher.language_preference);
                    $('input[name="is_active"]').prop('checked', teacher.is_active);
                    
                    // Load existing documents
                    loadTeacherForEdit(teacher);
                    
                    // Update the update button URL with the actual teacher ID
                    var updateUrl = "{{ route('teachers.update', ['id' => '__id__']) }}".replace('__id__', id);
                    $('#update_form_btn').attr('data-url', updateUrl);
                    
                    // Hide password fields for edit
                    $('input[name="password"]').prop('required', false);
                    $('input[name="password_confirmation"]').prop('required', false);
                    
                }
            },
            error: function(xhr) {
                showErrorMessage("{{ __('teachers.error_load_data') }}");
            }
        });
    }

    // handle click event for "Add" button
    $('.add_new').on('click', function(){
        $("#create_form_btn").show();
        $("#update_form_btn").hide();
        $("#modal_create_form .modal-title").text("{{ __('teachers.add_new') }}");
        
        // Reset to basic info tab
        $('#basic-info-tab').tab('show');
        
        // Reset form fields using name attributes
        $('input[name="id"]').val('');
        $('input[name="first_name"]').val('');
        $('input[name="last_name"]').val('');
        $('input[name="email"]').val('');
        $('input[name="phone"]').val('');
        $('input[name="document_id"]').val('');
        $('input[name="birth_date"]').val('');
        $('textarea[name="address"]').val('');
        $('input[name="emergency_contact"]').val('');
        $('input[name="emergency_phone"]').val('');
        $('select[name="language_preference"]').val('es');
        $('input[name="is_active"]').prop('checked', true);
        
        // Make password fields required for new teacher
        $('input[name="password"]').prop('required', true);
        $('input[name="password_confirmation"]').prop('required', true);
        
        $("#modal_create_form").modal('show');
    });

    // For Creating and updating the record
    $('#create_form_btn, #update_form_btn').on('click', function() {
        var form = $('#create-form')[0];
        
        if (!form.checkValidity()) {
            var firstInvalidElement = form.querySelector(':invalid');
            if (firstInvalidElement) {
                firstInvalidElement.focus();
                firstInvalidElement.reportValidity();
            }
            return false;
        }
        
        var url = $(this).data('url');
        var isUpdate = $(this).attr('id') === 'update_form_btn';
        var form_data = new FormData(form);
        
        // Add _method field for update requests
        if (isUpdate) {
            form_data.append('_method', 'PUT');
        }

        // Use direct jQuery AJAX for better FormData handling
        $.ajax({
            url: url,
            type: 'POST',
            data: form_data,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                table.ajax.reload();
                $("#modal_create_form").modal('hide');
                _handleSuccess(response.message);
            },
            error: function(xhr) {
                showErrorMessage("{{ __('teachers.error_processing') }}");
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    });

    // Password generation and toggle functionality
    function generatePassword() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    // Teacher password generation
    $('#generate_teacher_password').on('click', function() {
        const generatedPassword = generatePassword();
        $('#teacher_password').val(generatedPassword).addClass('password-generated');
        $('#teacher_password_confirmation').val(generatedPassword).addClass('password-generated');
        
        // Remove animation class after animation completes
        setTimeout(() => {
            $('#teacher_password, #teacher_password_confirmation').removeClass('password-generated');
        }, 600);
    });

    // Teacher password toggle
    $('#toggle_teacher_password').on('click', function() {
        const passwordField = $('#teacher_password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Teacher password confirmation toggle
    $('#toggle_teacher_password_confirmation').on('click', function() {
        const passwordField = $('#teacher_password_confirmation');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Document management for teachers
    let teacherDocuments = [];
    let removedTeacherDocuments = [];
    
    // Function to load existing documents
    function loadExistingTeacherDocuments(documents) {
        const preview = $('#teacher_document_preview');
        preview.empty();
        teacherDocuments = [];
        removedTeacherDocuments = [];
        
        if (documents && documents.length > 0) {
            documents.forEach((doc, index) => {
                const fileId = 'teacher_existing_doc_' + index;
                const fileUrl = '/storage/documents/' + doc;
                
                teacherDocuments.push({
                    id: fileId,
                    name: doc,
                    url: fileUrl,
                    isExisting: true
                });
                
                const documentItem = $(`
                    <div class="document-item" data-id="${fileId}">
                        <div class="document-info">
                            <i class="fas fa-file-alt"></i>
                            <span class="document-name">${doc}</span>
                            <span class="document-size">(Existing)</span>
                        </div>
                        <div class="document-actions">
                            <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Document">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-document" data-id="${fileId}" data-filename="${doc}" title="Remove Document">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `);
                
                preview.append(documentItem);
            });
        }
        updateTeacherDocumentUrls();
    }
    
    $('#teacher_documents').on('change', function() {
        const files = this.files;
        const preview = $('#teacher_document_preview');
        
        // Clear previous new file previews (keep existing ones)
        $('.document-item:not([data-id*="existing"])').remove();
        
        Array.from(files).forEach((file, index) => {
            const fileId = 'teacher_doc_' + Date.now() + '_' + index;
            const fileUrl = URL.createObjectURL(file);
            
            teacherDocuments.push({
                id: fileId,
                name: file.name,
                size: file.size,
                type: file.type,
                url: fileUrl,
                file: file,
                isNew: true
            });
            
            const documentItem = $(`
                <div class="document-item" data-id="${fileId}">
                    <div class="document-info">
                        <i class="fas fa-file-alt"></i>
                        <span class="document-name">${file.name}</span>
                        <span class="document-size">(${(file.size / 1024).toFixed(1)} KB)</span>
                    </div>
                    <div class="document-actions">
                        <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Document">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-document" data-id="${fileId}" title="Remove Document">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `);
            
            preview.append(documentItem);
        });
        
        updateTeacherDocumentUrls();
    });
    
    // Remove document
    $(document).on('click', '.remove-document', function() {
        const fileId = $(this).data('id');
        const filename = $(this).data('filename');
        
        // If it's an existing document, add to removed list
        if (filename) {
            removedTeacherDocuments.push(filename);
        }
        
        // Remove from current documents array
        teacherDocuments = teacherDocuments.filter(doc => doc.id !== fileId);
        $(`.document-item[data-id="${fileId}"]`).remove();
        updateTeacherDocumentUrls();
    });
    
    function updateTeacherDocumentUrls() {
        const existingDocs = teacherDocuments.filter(doc => doc.isExisting).map(doc => doc.name);
        const newDocs = teacherDocuments.filter(doc => doc.isNew).map(doc => doc.url);
        const allDocs = [...existingDocs, ...newDocs];
        $('#teacher_document_urls').val(allDocs.join(','));
        
        // Update removed documents hidden field
        $('#teacher_documents_to_remove').val(removedTeacherDocuments.join(','));
    }
    
    // Clear form when adding new teacher
    $('#modal_create_form').on('show.bs.modal', function() {
        // Clear all form fields
        $('#create-form')[0].reset();
        $('#teacher_document_preview').empty();
        teacherDocuments = [];
        removedTeacherDocuments = [];
        $('#teacher_document_urls').val('');
        $('#teacher_documents_to_remove').val('');
        
        // Reset password fields
        $('#teacher_password').attr('type', 'password');
        $('#teacher_password_confirmation').attr('type', 'password');
        $('#toggle_teacher_password i').removeClass('fa-eye-slash').addClass('fa-eye');
        $('#toggle_teacher_password_confirmation i').removeClass('fa-eye-slash').addClass('fa-eye');
    });
    
    // Load existing documents when editing
    function loadTeacherForEdit(teacherData) {
        if (teacherData.documents && teacherData.documents.length > 0) {
            loadExistingTeacherDocuments(teacherData.documents);
        } else {
            $('#teacher_document_preview').empty();
            teacherDocuments = [];
            $('#teacher_document_urls').val('');
        }
    }

    // Teacher Course Management
    let currentTeacherId = null;
    let allCourses = [];
    let assignedCourses = [];

    // Load courses when editing a teacher
    function loadTeacherCourses(teacherId) {
        currentTeacherId = teacherId;
        
        // Load all available courses
        $.ajax({
            url: "{{ route('courses.index') }}",
            type: 'GET',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    allCourses = response.data;
                    populateCoursesDropdown();
                }
            },
            error: function(xhr) {
                console.error('Failed to load courses:', xhr);
            }
        });
        
        // Load teacher's assigned courses
        $.ajax({
            url: "{{ route('teachers.courses', ['id' => '__id__']) }}".replace('__id__', teacherId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    assignedCourses = response.data;
                    displayAssignedCourses();
                    updateCoursesDropdown();
                }
            },
            error: function(xhr) {
                console.error('Failed to load teacher courses:', xhr);
            }
        });
    }

    // Populate courses dropdown
    function populateCoursesDropdown() {
        const dropdown = $('#teacher-courses');
        dropdown.empty();
        
        allCourses.forEach(course => {
            // Handle different data structures
            const language = course.language?.name || course.language || 'Unknown';
            const level = course.level?.name || course.level || 'Unknown';
            dropdown.append(`<option value="${course.id}">${course.name} (${language} - ${level})</option>`);
        });
    }

    // Update courses dropdown to show assigned courses as selected
    function updateCoursesDropdown() {
        const dropdown = $('#teacher-courses');
        dropdown.find('option').prop('selected', false);
        
        assignedCourses.forEach(assignedCourse => {
            dropdown.find(`option[value="${assignedCourse.course_id}"]`).prop('selected', true);
        });
    }

    // Display assigned courses
    function displayAssignedCourses() {
        const container = $('#assigned-courses-list');
        container.empty();
        
        if (assignedCourses.length === 0) {
            container.html('<p class="text-muted">{{ __("teachers.no_courses_assigned") }}</p>');
            return;
        }
        
        assignedCourses.forEach(assignedCourse => {
            const course = allCourses.find(c => c.id === assignedCourse.course_id);
            if (course) {
                // Handle different data structures
                const language = course.language?.name || course.language || 'Unknown';
                const level = course.level?.name || course.level || 'Unknown';
                
                const courseItem = $(`
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${course.name}</h6>
                            <small class="text-muted">${language} - ${level}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-course-btn" data-course-id="${course.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                container.append(courseItem);
            }
        });
    }

    // Handle courses tab click
    $('#courses-tab').on('click', function() {
        if (currentTeacherId) {
            loadTeacherCourses(currentTeacherId);
        }
    });

    // Handle course selection change
    $('#teacher-courses').on('change', function() {
        const selectedCourseIds = $(this).val() || [];
        assignedCourses = selectedCourseIds.map(courseId => ({
            course_id: parseInt(courseId)
        }));
        displayAssignedCourses();
    });

    // Handle remove course button
    $(document).on('click', '.remove-course-btn', function() {
        const courseId = parseInt($(this).data('course-id'));
        assignedCourses = assignedCourses.filter(ac => ac.course_id !== courseId);
        
        // Update dropdown
        $('#teacher-courses').find(`option[value="${courseId}"]`).prop('selected', false);
        
        displayAssignedCourses();
    });

    // Save courses
    $('#save-courses').on('click', function() {
        if (!currentTeacherId) {
            showErrorMessage('No teacher selected');
            return;
        }

        const saveBtn = $(this);
        const originalText = saveBtn.html();
        
        // Show loading state
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("teachers.saving") }}...');

        const courseIds = assignedCourses.map(ac => ac.course_id);

        $.ajax({
            url: "{{ route('teachers.courses.save', ['id' => '__id__']) }}".replace('__id__', currentTeacherId),
            type: 'POST',
            data: {
                course_ids: courseIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showSuccessMessage(response.message);
                } else {
                    showErrorMessage(response.message);
                }
            },
            error: function(xhr) {
                showErrorMessage('{{ __("teachers.courses_save_failed") }}');
                console.error('Save courses error:', xhr);
            },
            complete: function() {
                // Reset button state
                saveBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Teacher Availability Management
    let availabilityData = {};

    // Load availability data when editing a teacher
    function loadTeacherAvailability(teacherId) {
        
        currentTeacherId = teacherId;
        
        $.ajax({
            url: "{{ route('teachers.availability', ['id' => '__id__']) }}".replace('__id__', teacherId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    availabilityData = response.data;
                    buildAvailabilityTable();
                }
            },
            error: function(xhr) {
                console.error('Failed to load availability data:', xhr);
            }
        });
    }

    // Build the availability table
    function buildAvailabilityTable() {
        const tbody = $('#availability-tbody');
        const thead = $('#availability-table thead tr');
        tbody.empty();
        
        // Clear existing time slot headers
        thead.find('th:gt(2)').remove();
        
        if (!availabilityData.slots || Object.keys(availabilityData.slots).length === 0) {
            tbody.append('<tr><td colspan="3" class="text-center text-muted">No time slots available</td></tr>');
            return;
        }

        // Get all unique time periods (group by time, not by slot ID)
        const timeSlots = new Map();
        Object.values(availabilityData.slots).forEach(daySlots => {
            daySlots.forEach(slot => {
                const timeKey = `${slot.start_time}-${slot.end_time}`;
                if (!timeSlots.has(timeKey)) {
                    timeSlots.set(timeKey, {
                        timeRange: `${slot.start_time} - ${slot.end_time}`,
                        slots: []
                    });
                }
                timeSlots.get(timeKey).slots.push(slot);
            });
        });

        // Convert to array and sort by time
        const sortedTimeSlots = Array.from(timeSlots.values()).sort((a, b) => 
            a.slots[0].start_time.localeCompare(b.slots[0].start_time)
        );

        // Add time slot headers
        sortedTimeSlots.forEach(timeSlot => {
            thead.append(`<th class="text-center">${timeSlot.timeRange}</th>`);
        });

        // Add rows for each day
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        const dayNames = {
            'monday': '{{ __("teacher_availability.days.monday") }}',
            'tuesday': '{{ __("teacher_availability.days.tuesday") }}',
            'wednesday': '{{ __("teacher_availability.days.wednesday") }}',
            'thursday': '{{ __("teacher_availability.days.thursday") }}',
            'friday': '{{ __("teacher_availability.days.friday") }}',
            'saturday': '{{ __("teacher_availability.days.saturday") }}'
        };

        days.forEach(day => {
            const daySlots = availabilityData.slots[day] || [];
            const row = $(`
                <tr data-day="${day}">
                    <td><strong>${dayNames[day]}</strong></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success select-day-btn" data-day="${day}">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-warning clear-day-btn" data-day="${day}">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `);

            // Add checkboxes for each time period
            sortedTimeSlots.forEach(timeSlot => {
                // Find the slot for this day and time period
                const daySlot = daySlots.find(s => 
                    s.start_time === timeSlot.slots[0].start_time && 
                    s.end_time === timeSlot.slots[0].end_time
                );
                
                if (daySlot) {
                    const isAvailable = availabilityData.availability[daySlot.id] || false;
                    row.append(`
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input availability-checkbox" 
                                   data-slot-id="${daySlot.id}" data-day="${day}" 
                                   ${isAvailable ? 'checked' : ''}>
                        </td>
                    `);
                } else {
                    row.append('<td class="text-center"><span class="text-muted">-</span></td>');
                }
            });

            tbody.append(row);
        });
    }

    // Handle availability tab click
    $('#availability-tab').on('click', function() {
        console.log("currentTeacher", currentTeacherId);
        if (currentTeacherId) {
            loadTeacherAvailability(currentTeacherId);
        }
    });

    // Select all availability
    $('#select-all-availability').on('click', function() {
        $('.availability-checkbox').prop('checked', true);
    });

    // Clear all availability
    $('#clear-all-availability').on('click', function() {
        $('.availability-checkbox').prop('checked', false);
    });

    // Select day availability
    $(document).on('click', '.select-day-btn', function() {
        const day = $(this).data('day');
        $(`.availability-checkbox[data-day="${day}"]`).prop('checked', true);
    });

    // Clear day availability
    $(document).on('click', '.clear-day-btn', function() {
        const day = $(this).data('day');
        $(`.availability-checkbox[data-day="${day}"]`).prop('checked', false);
    });

    // Save availability
    $('#save-availability').on('click', function() {
        if (!currentTeacherId) {
            showErrorMessage('No teacher selected');
            return;
        }

        const saveBtn = $(this);
        const originalText = saveBtn.html();
        
        // Show loading state
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("teacher_availability.saving") }}...');

        const availability = {};
        $('.availability-checkbox').each(function() {
            const slotId = $(this).data('slot-id');
            const isChecked = $(this).is(':checked');
            availability[slotId] = isChecked;
        });

        const notes = $('#availability-notes').val();

        // Convert boolean values to strings for form data
        const availabilityData = {};
        Object.keys(availability).forEach(slotId => {
            availabilityData[`availability[${slotId}]`] = availability[slotId] ? '1' : '0';
        });

        $.ajax({
            url: "{{ route('teachers.availability.save', ['id' => '__id__']) }}".replace('__id__', currentTeacherId),
            type: 'POST',
            data: {
                ...availabilityData,
                notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showSuccessMessage(response.message);
                } else {
                    showErrorMessage(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '{{ __("teacher_availability.validation_failed") }}';
                    if (errors.availability) {
                        errorMessage += '<br>' + Object.values(errors.availability).join('<br>');
                    }
                    showErrorMessage(errorMessage);
                } else {
                    showErrorMessage('{{ __("teacher_availability.save_failed") }}');
                }
                console.error('Save availability error:', xhr);
            },
            complete: function() {
                // Reset button state
                saveBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Update loadTeacherData function to load availability
    const originalLoadTeacherData = loadTeacherData;
    loadTeacherData = function(id) {
        originalLoadTeacherData(id);
        currentTeacherId = id;
    };
});
</script>
@endpush