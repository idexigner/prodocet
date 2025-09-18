@extends('layouts.app')

@section('title', __('students.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission(auth()->user(), 'students.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('students.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission(auth()->user(), 'students.create'))
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

    @if(_has_permission(auth()->user(), 'students.create') || _has_permission(auth()->user(), 'students.edit'))
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
                            
                            <div class="row">
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
                                            <input type="password" name="password" placeholder="{{ __('students.password_placeholder') }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ __('students.confirm_password') }} <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="password" name="password_confirmation" placeholder="{{ __('students.confirm_password_placeholder') }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ __('students.phone') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="phone" placeholder="{{ __('students.phone_placeholder') }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ __('students.document_id') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="document_id" placeholder="{{ __('students.document_id_placeholder') }}" class="form-control">
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
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('students.close') }}</button>
                            <button type="button" data-url="{{ route('students.store') }}" id="create_form_btn" class="btn btn-primary">{{ __('students.submit') }}</button>
                            <button type="button" data-url="{{ route('students.update', ':id') }}" id="update_form_btn" class="btn btn-primary">{{ __('students.update') }}</button>
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
        
        // Load student data for editing
        loadStudentData(id);
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('students.confirm_delete') }}")) {
            $.ajax({
                url: "{{ route('students.destroy', '') }}/" + id,
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
                url: "{{ route('students.restore', '') }}/" + id,
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
            url: "{{ route('students.show', '') }}/" + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var student = response.data.student;
                    
                    // Populate form fields using name attributes
                    $('input[name="id"]').val(student.id);
                    $('input[name="first_name"]').val(student.first_name);
                    $('input[name="last_name"]').val(student.last_name);
                    $('input[name="email"]').val(student.email);
                    $('input[name="phone"]').val(student.phone);
                    $('input[name="document_id"]').val(student.document_id);
                    $('input[name="birth_date"]').val(student.birth_date);
                    $('textarea[name="address"]').val(student.address);
                    $('input[name="emergency_contact"]').val(student.emergency_contact);
                    $('input[name="emergency_phone"]').val(student.emergency_phone);
                    $('select[name="language_preference"]').val(student.language_preference);
                    $('input[name="is_active"]').prop('checked', student.is_active);
                    
                    // Update the update button URL with the actual student ID
                    var updateUrl = "{{ route('students.update', '') }}/" + id;
                    $('#update_form_btn').attr('data-url', updateUrl);
                    
                    // Hide password fields for edit
                    $('input[name="password"]').prop('required', false);
                    $('input[name="password_confirmation"]').prop('required', false);
                    
                    $("#modal_create_form").modal('show');
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
});
</script>
@endpush