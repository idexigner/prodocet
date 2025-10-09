@extends('layouts.app')

@section('title', __('settings.course_levels.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('course-levels.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('settings.course_levels.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('course-levels.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('settings.course_levels.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="course-levels-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('settings.course_levels.code') }}</th>
                                        <th>{{ __('settings.course_levels.name') }}</th>
                                        <th>{{ __('settings.course_levels.language') }}</th>
                                        <th>{{ __('settings.course_levels.status') }}</th>
                                        <th>{{ __('settings.course_levels.created_at') }}</th>
                                        <th>{{ __('settings.course_levels.action') }}</th>
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
                <h2 class="mb-2 mx-2">{{ __('settings.course_levels.access_denied_title') }}</h2>
                <p class="mb-4 mx-2">{{ __('settings.course_levels.access_denied_message') }}</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('settings.course_levels.back_to_dashboard') }}</a>
            </div>
        </div>
    @endif

    @if(_has_permission('course-levels.create') || _has_permission('course-levels.edit'))
        <!-- Add/Edit Course Level Modal -->
        <div class="modal fade" id="modal_create_form" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="create-form">
                        <div class="modal-header">
                            <h5 class='modal-title'>{{ __('settings.course_levels.add_new') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id">
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.course_levels.form.code') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="code" placeholder="{{ __('settings.course_levels.placeholders.code') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.course_levels.form.name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" placeholder="{{ __('settings.course_levels.placeholders.name') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.course_levels.form.description') }}</label>
                                <div class="col-sm-9">
                                    <textarea name="description" placeholder="{{ __('settings.course_levels.placeholders.description') }}" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.course_levels.form.order_index') }}</label>
                                <div class="col-sm-9">
                                    <input type="number" name="order_index" placeholder="{{ __('settings.course_levels.placeholders.order_index') }}" class="form-control" min="0">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.course_levels.form.language') }}</label>
                                <div class="col-sm-9">
                                    <select name="language_id" class="form-select">
                                        <option value="">{{ __('settings.course_levels.select_language') }}</option>
                                        <!-- Languages will be loaded via AJAX -->
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.course_levels.form.status') }}</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-select">
                                        <option value="active">{{ __('settings.course_levels.status_active') }}</option>
                                        <option value="inactive">{{ __('settings.course_levels.status_inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.course_levels.form.is_active') }}</label>
                                <div class="col-sm-9">
                                    <select name="is_active" class="form-select">
                                        <option value="1">{{ __('settings.course_levels.status_active') }}</option>
                                        <option value="0">{{ __('settings.course_levels.status_inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('settings.course_levels.close') }}</button>
                            <button type="button" data-url="{{ route('settings.course-levels.store') }}" id="create_form_btn" class="btn btn-primary">{{ __('settings.course_levels.submit') }}</button>
                            <button type="button" data-url="{{ route('settings.course-levels.update', ':id') }}" id="update_form_btn" class="btn btn-primary">{{ __('settings.course_levels.update') }}</button>
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
    // Initialize DataTable
    var table = $('#course-levels-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('settings.course-levels.index') }}",
        },
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'language_name', name: 'language.name', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            processing: "{{ __('settings.course_levels.datatable.processing') }}",
            search: "{{ __('settings.course_levels.datatable.search') }}",
            lengthMenu: "{{ __('settings.course_levels.datatable.lengthMenu') }}",
            info: "{{ __('settings.course_levels.datatable.info') }}",
            infoEmpty: "{{ __('settings.course_levels.datatable.infoEmpty') }}",
            infoFiltered: "{{ __('settings.course_levels.datatable.infoFiltered') }}",
            loadingRecords: "{{ __('settings.course_levels.datatable.loadingRecords') }}",
            zeroRecords: "{{ __('settings.course_levels.datatable.zeroRecords') }}",
            emptyTable: "{{ __('settings.course_levels.datatable.emptyTable') }}",
            paginate: {
                first: "{{ __('settings.course_levels.datatable.paginate.first') }}",
                previous: "{{ __('settings.course_levels.datatable.paginate.previous') }}",
                next: "{{ __('settings.course_levels.datatable.paginate.next') }}",
                last: "{{ __('settings.course_levels.datatable.paginate.last') }}"
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> {{ __("settings.course_levels.export_excel") }}',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Exclude Action column
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> {{ __("settings.course_levels.export_pdf") }}',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Exclude Action column
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> {{ __("settings.course_levels.export_print") }}',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Exclude Action column
                }
            }
        ]
    });

    // Load languages for dropdown
    function loadLanguages() {
        $.ajax({
            url: "{{ route('settings.languages.index') }}",
            type: 'GET',
            success: function(response) {
                var languageSelect = $('select[name="language_id"]');
                languageSelect.empty();
                languageSelect.append('<option value="">{{ __("settings.course_levels.select_language") }}</option>');
                
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, language) {
                        languageSelect.append('<option value="' + language.id + '">' + language.name + '</option>');
                    });
                }
            }
        });
    }

    // Handle edit button click
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        // Open edit modal
        $("#modal_create_form .modal-title").text("{{ __('settings.course_levels.edit_title') }}");
        $("#create_form_btn").hide();
        $("#update_form_btn").show();
        
        // Load course level data for editing
        loadCourseLevelData(id);
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('settings.course_levels.messages.confirm_delete') }}")) {
            $.ajax({
                url: "{{ route('settings.course-levels.destroy', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#course-levels-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('settings.course_levels.messages.delete_failed') }}");
                }
            });
        }
    });

    // Handle restore button click
    $(document).on('click', '.restore-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('settings.course_levels.messages.confirm_restore') }}")) {
            $.ajax({
                url: "{{ route('settings.course-levels.restore', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#course-levels-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('settings.course_levels.messages.restore_failed') }}");
                }
            });
        }
    });

    // Function to load course level data for editing
    function loadCourseLevelData(id) {
        $.ajax({
            url: "{{ route('settings.course-levels.show', ['id' => '__id__']) }}".replace('__id__', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var courseLevel = response.data;
                    
                    // Populate form fields using name attributes
                    $('input[name="id"]').val(courseLevel.id);
                    $('input[name="code"]').val(courseLevel.code);
                    $('input[name="name"]').val(courseLevel.name);
                    $('textarea[name="description"]').val(courseLevel.description);
                    $('input[name="order_index"]').val(courseLevel.order_index);
                    $('select[name="status"]').val(courseLevel.status);
                    $('select[name="is_active"]').val(courseLevel.is_active);
                    $('select[name="language_id"]').val(courseLevel.language_id);
                    
                    // Update the update button URL with the actual course level ID
                    var updateUrl = "{{ route('settings.course-levels.update', ['id' => '__id__']) }}".replace('__id__', id);
                    $('#update_form_btn').attr('data-url', updateUrl);
                    
                    $("#modal_create_form").modal('show');
                }
            },
            error: function(xhr) {
                showErrorMessage("{{ __('settings.course_levels.messages.load_failed') }}");
            }
        });
    }

    // handle click event for "Add" button
    $('.add_new').on('click', function(){
        $("#create_form_btn").show();
        $("#update_form_btn").hide();
        $("#modal_create_form .modal-title").text("{{ __('settings.course_levels.add_new') }}");
        
        // Reset form fields using name attributes
        $('input[name="id"]').val('');
        $('input[name="code"]').val('');
        $('input[name="name"]').val('');
        $('textarea[name="description"]').val('');
        $('input[name="order_index"]').val('');
        $('select[name="status"]').val('active');
        $('select[name="is_active"]').val('1');
        $('select[name="language_id"]').val('');
        
        // Load languages for dropdown
        loadLanguages();
        
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
                if (response.success) {
                    $('#course-levels-table').DataTable().ajax.reload(null, false);
                    $("#modal_create_form").modal('hide');
                    showSuccessMessage(response.message);
                } else {
                    showErrorMessage(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    for (var field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                    showErrorMessage(errorMessage);
                } else {
                    showErrorMessage("{{ __('settings.course_levels.messages.operation_failed') }}");
                }
            }
        });
    });
});
</script>
@endpush