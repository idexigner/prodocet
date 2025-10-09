@extends('layouts.app')

@section('title', __('students.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('students.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('students.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('students.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('students.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="students-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('students.id') }}</th>
                                        <th>{{ __('students.full_name') }}</th>
                                        <th>{{ __('students.email') }}</th>
                                        <th>{{ __('students.phone') }}</th>
                                        <th>{{ __('students.document_id') }}</th>
                                        <th>{{ __('students.status') }}</th>
                                        <th>{{ __('students.created_at') }}</th>
                                        <th>{{ __('students.action') }}</th>
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
                <h2 class="mb-2 mx-2">{{ __('students.access_denied_title') }}</h2>
                <p class="mb-4 mx-2">{{ __('students.access_denied_message') }}</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('students.back_to_dashboard') }}</a>
            </div>
        </div>
    @endif

    @if(_has_permission('students.create') || _has_permission('students.edit'))
        <!-- Add/Edit Student Modal -->
        <div class="modal fade" id="modal_create_form" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="create-form">
                        <div class="modal-header">
                            <h5 class='modal-title'>{{ __('students.add_new') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id">
                            
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info" aria-selected="true">
                                        <i class="fas fa-user"></i> {{ __('students.basic_info') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false">
                                        <i class="fas fa-id-card"></i> {{ __('student_documents.title') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="availability-tab" data-bs-toggle="tab" data-bs-target="#availability" type="button" role="tab" aria-controls="availability" aria-selected="false">
                                        <i class="fas fa-calendar-alt"></i> {{ __('student_availability.title') }}
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content" id="studentTabsContent">
                                <!-- Basic Info Tab -->
                                <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.first_name') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="first_name" placeholder="{{ __('students.first_name_placeholder') }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.last_name') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="last_name" placeholder="{{ __('students.last_name_placeholder') }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.email') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="email" name="email" placeholder="{{ __('students.email_placeholder') }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.password') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <input type="password" name="password" id="student_password" placeholder="{{ __('students.password_placeholder') }}" class="form-control" required>
                                                        <button class="btn btn-outline-secondary password-toggle-btn" type="button" id="toggle_student_password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn password-generator-btn" type="button" id="generate_student_password" title="Generate Password">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.confirm_password') }} <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <input type="password" name="password_confirmation" id="student_password_confirmation" placeholder="{{ __('students.confirm_password_placeholder') }}" class="form-control" required>
                                                        <button class="btn btn-outline-secondary password-toggle-btn" type="button" id="toggle_student_password_confirmation">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.phone') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="phone" placeholder="{{ __('students.phone_placeholder') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.birth_date') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="date" name="birth_date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.address') }}</label>
                                                <div class="col-sm-8">
                                                    <textarea name="address" placeholder="{{ __('students.address_placeholder') }}" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.emergency_contact') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="emergency_contact" placeholder="{{ __('students.emergency_contact_placeholder') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.emergency_phone') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="emergency_phone" placeholder="{{ __('students.emergency_phone_placeholder') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.language_preference') }}</label>
                                                <div class="col-sm-8">
                                                    <select name="language_preference" class="form-select">
                                                        <option value="es">{{ __('students.spanish') }}</option>
                                                        <option value="en">{{ __('students.english') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">{{ __('students.status') }}</label>
                                                <div class="col-sm-8">
                                                    <select name="is_active" class="form-select">
                                                        <option value="1">{{ __('students.status_active') }}</option>
                                                        <option value="0">{{ __('students.status_inactive') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Documents Tab -->
                                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>{{ __('student_documents.heading') }}</h6>
                                            <button type="button" class="btn btn-sm btn-primary" id="add-document-btn">
                                                <i class="fas fa-plus"></i> {{ __('student_documents.add_document') }}
                                            </button>
                                        </div>
                                        
                                        <!-- Documents List -->
                                        <div id="documents-list" class="documents-container">
                                            <!-- Documents will be loaded here -->
                                        </div>
                                        
                                        <!-- No Documents Message -->
                                        <div id="no-documents-message" class="text-center text-muted py-4" style="display: none;">
                                            <i class="fas fa-id-card fa-3x mb-3"></i>
                                            <p>{{ __('student_documents.no_documents') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Availability Tab -->
                                <div class="tab-pane fade" id="availability" role="tabpanel" aria-labelledby="availability-tab">
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>{{ __('student_availability.heading') }}</h6>
                                            <button type="button" class="btn btn-sm btn-primary" id="loadAvailabilityBtn">
                                                <i class="fas fa-sync"></i> {{ __('student_availability.load_availability') }}
                                            </button>
                                        </div>
                                        
                                        <div id="availability-loading" class="text-center py-3" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">{{ __('student_availability.loading') }}</span>
                                            </div>
                                            <p class="mt-2">{{ __('student_availability.loading_availability') }}</p>
                                        </div>

                                        <div id="availability-container" class="border rounded p-3">
                                            <p class="text-muted text-center">{{ __('student_availability.select_student_first') }}</p>
                                        </div>
                                        
                                        <div class="mt-3 text-center">
                                            <button type="button" class="btn btn-success" id="saveAvailabilityBtn" style="display: none;">
                                                <i class="fas fa-save"></i> {{ __('student_availability.save_availability') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('students.close') }}</button>
                            <button type="button" data-url="{{ route('students.store') }}" id="create_form_btn" class="btn btn-primary">{{ __('students.submit') }}</button>
                            <button type="button" data-url="{{ route('students.update', ':id') }}" id="update_form_btn" class="btn btn-primary">{{ __('students.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Add/Edit Document Modal -->
        <div class="modal fade" id="documentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="document-form">
                        <div class="modal-header">
                            <h5 class="modal-title" id="documentModalTitle">{{ __('student_documents.add_document') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="document_id" id="document_id">
                            
                            <div class="mb-3">
                                <label for="document_type" class="form-label">{{ __('student_documents.document_type') }} <span class="text-danger">*</span></label>
                                <select name="document_type" id="document_type" class="form-select" required>
                                    <option value="">{{ __('student_documents.select_document_type') }}</option>
                                    @foreach(__('student_documents.document_types') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="document_number" class="form-label">{{ __('student_documents.document_number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="document_number" id="document_number" class="form-control" placeholder="{{ __('student_documents.document_number_placeholder') }}" required>
                            </div>
                            
                            <div class="mb-3" id="document_name_group" style="display: none;">
                                <label for="document_name" class="form-label">{{ __('student_documents.document_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="document_name" id="document_name" class="form-control" placeholder="{{ __('student_documents.document_name_placeholder') }}">
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_primary" id="is_primary" class="form-check-input">
                                    <label for="is_primary" class="form-check-label">{{ __('student_documents.is_primary') }}</label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="document_notes" class="form-label">{{ __('student_documents.notes') }}</label>
                                <textarea name="notes" id="document_notes" class="form-control" rows="3" placeholder="{{ __('student_documents.notes_placeholder') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('students.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('students.save') }}</button>
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
    var table = $('#students-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('students.index') }}",
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
            processing: "{{ __('students.processing') }}",
            lengthMenu: "{{ __('students.length_menu') }}",
            zeroRecords: "{{ __('students.zero_records') }}",
            emptyTable: "{{ __('students.empty_table') }}",
            info: "{{ __('students.info') }}",
            infoEmpty: "{{ __('students.info_empty') }}",
            infoFiltered: "{{ __('students.info_filtered') }}",
            search: "{{ __('students.search') }}",
            paginate: {
                first: "{{ __('students.first') }}",
                last: "{{ __('students.last') }}",
                next: "{{ __('students.next') }}",
                previous: "{{ __('students.previous') }}"
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> {{ __("students.export_excel") }}',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> {{ __("students.export_pdf") }}',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> {{ __("students.export_print") }}',
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
        $("#modal_create_form .modal-title").text("{{ __('students.edit_student') }}");
        $("#create_form_btn").hide();
        $("#update_form_btn").show();
        
        // Reset to basic info tab
        $('#basic-info-tab').tab('show');
        
        // Load student data for editing
        loadStudentData(id);
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('students.confirm_delete') }}")) {
            $.ajax({
                url: "{{ route('students.destroy', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#students-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('students.error_delete') }}");
                }
            });
        }
    });

    // Handle restore button click
    $(document).on('click', '.restore-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('students.confirm_restore') }}")) {
            $.ajax({
                url: "{{ route('students.restore', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#students-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('students.error_restore') }}");
                }
            });
        }
    });

    // Function to load student data for editing
    function loadStudentData(id) {
        $.ajax({
            url: "{{ route('students.show', ['id' => '__id__']) }}".replace('__id__', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $("#modal_create_form").modal('show');

                    var student = response.data.student;
                    console.log("student", student);
                    
                    // Populate form fields using name attributes
                    $('input[name="id"]').val(student.id);
                    $('input[name="first_name"]').val(student.first_name);
                    $('input[name="last_name"]').val(student.last_name);
                    $('input[name="email"]').val(student.email);
                    $('input[name="phone"]').val(student.phone);
                    $('input[name="document_id"]').val(student.document_id);
                    $('input[name="passport_number"]').val(student.passport_number);
                    // Format date for HTML date input (YYYY-MM-DD)
                    if (student.birth_date) {
                        const birthDate = new Date(student.birth_date);
                        const formattedDate = birthDate.toISOString().split('T')[0];
                        $('input[name="birth_date"]').val(formattedDate);
                    } else {
                        $('input[name="birth_date"]').val('');
                    }
                    $('textarea[name="address"]').val(student.address);
                    $('input[name="emergency_contact"]').val(student.emergency_contact);
                    $('input[name="emergency_phone"]').val(student.emergency_phone);
                    $('select[name="language_preference"]').val(student.language_preference);
                    $('input[name="is_active"]').prop('checked', student.is_active);
                    
                    // Load existing documents
                    loadStudentForEdit(student);
                    
                    // Update the update button URL with the actual student ID
                    var updateUrl = "{{ route('students.update', ['id' => '__id__']) }}".replace('__id__', id);
                    $('#update_form_btn').attr('data-url', updateUrl);
                    
                    // Hide password fields for edit
                    $('input[name="password"]').prop('required', false);
                    $('input[name="password_confirmation"]').prop('required', false);
                    
                }
            },
            error: function(xhr) {
                showErrorMessage("{{ __('students.error_load_data') }}");
            }
        });
    }

    // handle click event for "Add" button
    $('.add_new').on('click', function(){
        $("#create_form_btn").show();
        $("#update_form_btn").hide();
        $("#modal_create_form .modal-title").text("{{ __('students.add_new') }}");
        
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
        
        // Make password fields required for new student
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
                // Reload the DataTable to fetch latest data
                $('#students-table').DataTable().ajax.reload(null, false);
                $("#modal_create_form").modal('hide');
                _handleSuccess(response.message);
            },
            error: function(xhr) {
                showErrorMessage("{{ __('students.error_processing') }}");
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

    // Student password generation
    $('#generate_student_password').on('click', function() {
        const generatedPassword = generatePassword();
        $('#student_password').val(generatedPassword).addClass('password-generated');
        $('#student_password_confirmation').val(generatedPassword).addClass('password-generated');
        
        // Remove animation class after animation completes
        setTimeout(() => {
            $('#student_password, #student_password_confirmation').removeClass('password-generated');
        }, 600);
    });

    // Student password toggle
    $('#toggle_student_password').on('click', function() {
        const passwordField = $('#student_password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Student password confirmation toggle
    $('#toggle_student_password_confirmation').on('click', function() {
        const passwordField = $('#student_password_confirmation');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Document management for students
    let studentDocuments = [];
    let removedDocuments = [];
    
    // Function to load existing documents
    function loadExistingDocuments(documents) {
        const preview = $('#student_document_preview');
        preview.empty();
        studentDocuments = [];
        removedDocuments = [];
        
        if (documents && documents.length > 0) {
            documents.forEach((doc, index) => {
                const fileId = 'student_existing_doc_' + index;
                const fileUrl = '/storage/documents/' + doc;
                
                studentDocuments.push({
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
        updateStudentDocumentUrls();
    }
    
    $('#student_documents').on('change', function() {
        const files = this.files;
        const preview = $('#student_document_preview');
        
        // Clear previous new file previews (keep existing ones)
        $('.document-item:not([data-id*="existing"])').remove();
        
        Array.from(files).forEach((file, index) => {
            const fileId = 'student_doc_' + Date.now() + '_' + index;
            const fileUrl = URL.createObjectURL(file);
            
            studentDocuments.push({
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
        
        updateStudentDocumentUrls();
    });
    
    // Remove document
    $(document).on('click', '.remove-document', function() {
        const fileId = $(this).data('id');
        const filename = $(this).data('filename');
        
        // If it's an existing document, add to removed list
        if (filename) {
            removedDocuments.push(filename);
        }
        
        // Remove from current documents array
        studentDocuments = studentDocuments.filter(doc => doc.id !== fileId);
        $(`.document-item[data-id="${fileId}"]`).remove();
        updateStudentDocumentUrls();
    });
    
    function updateStudentDocumentUrls() {
        const existingDocs = studentDocuments.filter(doc => doc.isExisting).map(doc => doc.name);
        const newDocs = studentDocuments.filter(doc => doc.isNew).map(doc => doc.url);
        const allDocs = [...existingDocs, ...newDocs];
        $('#student_document_urls').val(allDocs.join(','));
        
        // Update removed documents hidden field
        $('#student_documents_to_remove').val(removedDocuments.join(','));
    }
    
    // Clear form when adding new student
    $('#modal_create_form').on('show.bs.modal', function() {
        // Clear all form fields
        $('#create-form')[0].reset();
        $('#student_document_preview').empty();
        studentDocuments = [];
        removedDocuments = [];
        $('#student_document_urls').val('');
        $('#student_documents_to_remove').val('');
        
        // Reset password fields
        $('#student_password').attr('type', 'password');
        $('#student_password_confirmation').attr('type', 'password');
        $('#toggle_student_password i').removeClass('fa-eye-slash').addClass('fa-eye');
        $('#toggle_student_password_confirmation i').removeClass('fa-eye-slash').addClass('fa-eye');
    });
    
    // Load existing documents when editing
    function loadStudentForEdit(studentData) {
        if (studentData.documents && studentData.documents.length > 0) {
            loadExistingDocuments(studentData.documents);
        } else {
            $('#student_document_preview').empty();
            studentDocuments = [];
            $('#student_document_urls').val('');
        }
    }

    // Student Documents Management
    let currentStudentId = null;

    // Load student documents when editing a student
    function loadStudentDocuments(studentId) {
        currentStudentId = studentId;
        
        $.ajax({
            url: "{{ route('students.documents', ['id' => '__id__']) }}".replace('__id__', studentId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    studentDocuments = response.data;
                    displayStudentDocuments();
                }
            },
            error: function(xhr) {
                console.error('Failed to load student documents:', xhr);
                showErrorMessage('{{ __("student_documents.fetch_failed") }}');
            }
        });
    }

    // Display student documents in the documents tab
    function displayStudentDocuments() {
        const container = $('#documents-list');
        container.empty();

        if (studentDocuments.length === 0) {
            container.html('<p class="text-muted">{{ __("student_documents.no_documents") }}</p>');
            return;
        }

        const documentsHtml = studentDocuments.map(doc => `
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <strong>${doc.document_type}</strong>
                            ${doc.is_primary ? '<span class="badge bg-primary ms-1">{{ __("student_documents.primary") }}</span>' : ''}
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted">${doc.document_number}</span>
                        </div>
                        <div class="col-md-3">
                            ${doc.document_name ? `<small class="text-muted">${doc.document_name}</small>` : ''}
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-document-btn" data-doc-id="${doc.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-document-btn" data-doc-id="${doc.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                            ${!doc.is_primary ? `<button type="button" class="btn btn-sm btn-outline-success set-primary-btn" data-doc-id="${doc.id}">
                                <i class="fas fa-star"></i>
                            </button>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        container.html(documentsHtml);
    }

    // Handle documents tab click
    $('#documents-tab').on('click', function() {
        if (currentStudentId) {
            loadStudentDocuments(currentStudentId);
        }
    });

    // Handle add document button
    $('#add-document-btn').on('click', function() {
        if (!currentStudentId) {
            showErrorMessage('No student selected');
            return;
        }
        resetDocumentForm();
        $('#documentModal').modal('show');
    });

    // Handle edit document button
    $(document).on('click', '.edit-document-btn', function() {
        const docId = $(this).data('doc-id');
        let doc = studentDocuments.find(d => d.id == docId);
        
        if (doc) {
            $('#document_id').val(doc.id);
            $('#document_type').val(doc.document_type);
            $('#document_number').val(doc.document_number);
            $('#document_name').val(doc.document_name || '');
            $('#is_primary').prop('checked', doc.is_primary);
            $('#document_notes').val(doc.notes || '');
            
            $('#documentModal').modal('show');
        }
    });

    // Handle delete document button
    $(document).on('click', '.delete-document-btn', function() {
        const docId = $(this).data('doc-id');
        
        if (confirm('{{ __("student_documents.confirm_delete") }}')) {
            $.ajax({
                url: "{{ route('students.documents.delete', ['id' => '__id__', 'documentId' => '__doc_id__']) }}"
                    .replace('__id__', currentStudentId)
                    .replace('__doc_id__', docId),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccessMessage(response.message);
                        loadStudentDocuments(currentStudentId);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage('{{ __("student_documents.delete_failed") }}');
                    console.error('Delete document error:', xhr);
                }
            });
        }
    });

    // Handle set primary document button
    $(document).on('click', '.set-primary-btn', function() {
        const docId = $(this).data('doc-id');
        
        if (confirm('{{ __("student_documents.confirm_set_primary") }}')) {
            $.ajax({
                url: "{{ route('students.documents.set-primary', ['id' => '__id__', 'documentId' => '__doc_id__']) }}"
                    .replace('__id__', currentStudentId)
                    .replace('__doc_id__', docId),
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccessMessage(response.message);
                        loadStudentDocuments(currentStudentId);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage('{{ __("student_documents.set_primary_failed") }}');
                    console.error('Set primary document error:', xhr);
                }
            });
        }
    });

    // Handle document form submission
    $('#document-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentStudentId) {
            showErrorMessage('No student selected');
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("students.saving") }}...');

        const formData = {
            document_type: $('#document_type').val(),
            document_number: $('#document_number').val(),
            document_name: $('#document_name').val(),
            is_primary: $('#is_primary').is(':checked') ? '1' : '0',
            notes: $('#document_notes').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        const documentId = $('#document_id').val();
        const url = documentId ? 
            "{{ route('students.documents.update', ['id' => '__id__', 'documentId' => '__doc_id__']) }}"
                .replace('__id__', currentStudentId)
                .replace('__doc_id__', documentId) :
            "{{ route('students.documents.store', ['id' => '__id__']) }}".replace('__id__', currentStudentId);
        
        const method = documentId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    showSuccessMessage(response.message);
                    $('#documentModal').modal('hide');
                    loadStudentDocuments(currentStudentId);
                } else {
                    showErrorMessage(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '{{ __("student_documents.validation_failed") }}';
                    if (errors.document_number && errors.document_number.includes('already been taken')) {
                        errorMessage = '{{ __("student_documents.document_exists") }}';
                    }
                    showErrorMessage(errorMessage);
                } else {
                    showErrorMessage(documentId ? '{{ __("student_documents.update_failed") }}' : '{{ __("student_documents.create_failed") }}');
                }
                console.error('Document save error:', xhr);
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html("{{ __('students.save') }}");
            }
        });
    });

    // Reset document form
    function resetDocumentForm() {
        $('#document_id').val('');
        $('#document_type').val('');
        $('#document_number').val('');
        $('#document_name').val('');
        $('#is_primary').prop('checked', false);
        $('#document_notes').val('');
    }

    // Handle document type change
    $('#document_type').on('change', function() {
        const documentNameField = $('#document_name');
        const documentNameGroup = $('#document_name_group');
        if ($(this).val() === 'other') {
            documentNameField.prop('required', true);
            documentNameGroup.show();
        } else {
            documentNameField.prop('required', false);
            documentNameGroup.hide();
        }
    });

    // Update loadStudentData function to load documents
    const originalLoadStudentData = loadStudentData;
    loadStudentData = function(id) {
        originalLoadStudentData(id);
        currentStudentId = id;
    };

    // Availability functionality
    let availabilityData = {};

    // Load availability when availability tab is clicked
    $('#availability-tab').on('click', function() {
        if (currentStudentId) {
            loadStudentAvailability();
        } else {
            $('#availability-container').html('<p class="text-muted text-center">{{ __("student_availability.select_student_first") }}</p>');
        }
    });

    // Load availability button
    $('#loadAvailabilityBtn').on('click', function() {
        if (currentStudentId) {
            loadStudentAvailability();
        } else {
            alert('{{ __("student_availability.select_student_first") }}');
        }
    });

    // Save availability button
    $('#saveAvailabilityBtn').on('click', function() {
        if (currentStudentId) {
            saveStudentAvailability();
        }
    });

    // Load student availability
    function loadStudentAvailability() {
        $('#availability-loading').show();
        $('#availability-container').hide();

        $.ajax({
            url: "{{ route('students.availability', '__id__') }}".replace('__id__', currentStudentId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    availabilityData = response.data.availability_by_day;
                    renderAvailabilityTable();
                } else {
                    $('#availability-container').html('<p class="text-danger">{{ __("student_availability.fetch_failed") }}</p>');
                }
            },
            error: function(xhr) {
                $('#availability-container').html('<p class="text-danger">{{ __("student_availability.fetch_failed") }}</p>');
            },
            complete: function() {
                $('#availability-loading').hide();
                $('#availability-container').show();
            }
        });
    }

    // Render availability table
    function renderAvailabilityTable() {
        let html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
        html += '<thead><tr><th>{{ __("student_availability.day") }}</th>';

        // Get unique time slots
        let timeSlots = [];
        Object.values(availabilityData).forEach(daySlots => {
            daySlots.forEach(slot => {
                const timeKey = slot.start_time + '-' + slot.end_time;
                if (!timeSlots.find(ts => ts.key === timeKey)) {
                    timeSlots.push({
                        key: timeKey,
                        start: slot.start_time,
                        end: slot.end_time
                    });
                }
            });
        });

        // Sort time slots
        timeSlots.sort((a, b) => a.start.localeCompare(b.start));

        // Add time slot headers
        timeSlots.forEach(slot => {
            html += `<th class="text-center">${slot.start}<br>${slot.end}</th>`;
        });
        html += '</tr></thead><tbody>';

        // Add rows for each day
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        days.forEach(day => {
            html += `<tr><td><strong>${day.charAt(0).toUpperCase() + day.slice(1)}</strong></td>`;
            
            timeSlots.forEach(slot => {
                const daySlots = availabilityData[day] || [];
                const matchingSlot = daySlots.find(s => s.start_time === slot.start && s.end_time === slot.end);
                const isAvailable = matchingSlot ? matchingSlot.is_available : false;
                const slotId = matchingSlot ? matchingSlot.id : '';
                
                html += `<td class="text-center">`;
                html += `<input type="checkbox" class="form-check-input availability-checkbox" 
                         data-slot-id="${slotId}" data-day="${day}" data-time="${slot.key}"
                         ${isAvailable ? 'checked' : ''}>`;
                html += `</td>`;
            });
            
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        html += '<div class="mt-3">';
        html += '<label for="availability_notes" class="form-label">{{ __("student_availability.notes") }}</label>';
        html += '<textarea id="availability_notes" class="form-control" rows="3" placeholder="{{ __("student_availability.notes_placeholder") }}"></textarea>';
        html += '</div>';

        $('#availability-container').html(html);
        $('#saveAvailabilityBtn').show();
    }

    // Save student availability
    function saveStudentAvailability() {
        const availability = {};
        const notes = $('#availability_notes').val();

        $('.availability-checkbox:checked').each(function() {
            const slotId = $(this).data('slot-id');
            if (slotId) {
                availability[slotId] = true;
            }
        });

        $.ajax({
            url: "{{ route('students.availability.save', '__id__') }}".replace('__id__', currentStudentId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                availability: availability,
                notes: notes
            },
            success: function(response) {
                if (response.success) {
                    alert('{{ __("student_availability.availability_saved") }}');
                } else {
                    alert('{{ __("student_availability.save_failed") }}');
                }
            },
            error: function(xhr) {
                alert('{{ __("student_availability.save_failed") }}');
            }
        });
    }
});
</script>
@endpush