@extends('layouts.app')

@section('title', __('settings.languages.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('languages.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('settings.languages.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('languages.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('settings.languages.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="languages-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('settings.languages.code') }}</th>
                                        <th>{{ __('settings.languages.name') }}</th>
                                        <th>{{ __('settings.languages.native_name') }}</th>
                                        <th>{{ __('settings.languages.status') }}</th>
                                        <th>{{ __('settings.languages.created_at') }}</th>
                                        <th>{{ __('settings.languages.action') }}</th>
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
                <h2 class="mb-2 mx-2">{{ __('settings.languages.access_denied_title') }}</h2>
                <p class="mb-4 mx-2">{{ __('settings.languages.access_denied_message') }}</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('settings.languages.back_to_dashboard') }}</a>
            </div>
        </div>
    @endif

    @if(_has_permission('languages.create') || _has_permission('languages.edit'))
        <!-- Add/Edit Language Modal -->
        <div class="modal fade" id="modal_create_form" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="create-form">
                        <div class="modal-header">
                            <h5 class='modal-title'>{{ __('settings.languages.add_new') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id">
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.languages.form.code') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="code" placeholder="{{ __('settings.languages.placeholders.code') }}" class="form-control" required maxlength="5">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.languages.form.name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" placeholder="{{ __('settings.languages.placeholders.name') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.languages.form.native_name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="native_name" placeholder="{{ __('settings.languages.placeholders.native_name') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.languages.form.is_active') }}</label>
                                <div class="col-sm-9">
                                    <select name="is_active" class="form-select">
                                        <option value="1">{{ __('settings.languages.status_active') }}</option>
                                        <option value="0">{{ __('settings.languages.status_inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('settings.languages.close') }}</button>
                            <button type="button" data-url="{{ route('settings.languages.store') }}" id="create_form_btn" class="btn btn-primary">{{ __('settings.languages.submit') }}</button>
                            <button type="button" data-url="{{ route('settings.languages.update', ':id') }}" id="update_form_btn" class="btn btn-primary">{{ __('settings.languages.update') }}</button>
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
    var table = $('#languages-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('settings.languages.index') }}",
        },
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'native_name', name: 'native_name' },
            { data: 'status', name: 'is_active', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            processing: "{{ __('settings.languages.datatable.processing') }}",
            search: "{{ __('settings.languages.datatable.search') }}",
            lengthMenu: "{{ __('settings.languages.datatable.lengthMenu') }}",
            info: "{{ __('settings.languages.datatable.info') }}",
            infoEmpty: "{{ __('settings.languages.datatable.infoEmpty') }}",
            infoFiltered: "{{ __('settings.languages.datatable.infoFiltered') }}",
            loadingRecords: "{{ __('settings.languages.datatable.loadingRecords') }}",
            zeroRecords: "{{ __('settings.languages.datatable.zeroRecords') }}",
            emptyTable: "{{ __('settings.languages.datatable.emptyTable') }}",
            paginate: {
                first: "{{ __('settings.languages.datatable.paginate.first') }}",
                previous: "{{ __('settings.languages.datatable.paginate.previous') }}",
                next: "{{ __('settings.languages.datatable.paginate.next') }}",
                last: "{{ __('settings.languages.datatable.paginate.last') }}"
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> {{ __("settings.languages.export_excel") }}',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Exclude Action column
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> {{ __("settings.languages.export_pdf") }}',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Exclude Action column
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> {{ __("settings.languages.export_print") }}',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Exclude Action column
                }
            }
        ]
    });

    // Handle edit button click
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        // Open edit modal
        $("#modal_create_form .modal-title").text("{{ __('settings.languages.edit_title') }}");
        $("#create_form_btn").hide();
        $("#update_form_btn").show();
        
        // Load language data for editing
        loadLanguageData(id);
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('settings.languages.messages.confirm_delete') }}")) {
            $.ajax({
                url: "{{ route('settings.languages.destroy', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#languages-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('settings.languages.messages.delete_failed') }}");
                }
            });
        }
    });

    // Handle restore button click
    $(document).on('click', '.restore-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('settings.languages.messages.confirm_restore') }}")) {
            $.ajax({
                url: "{{ route('settings.languages.restore', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#languages-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('settings.languages.messages.restore_failed') }}");
                }
            });
        }
    });

    // Function to load language data for editing
    function loadLanguageData(id) {
        $.ajax({
            url: "{{ route('settings.languages.show', ['id' => '__id__']) }}".replace('__id__', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var language = response.data;
                    
                    // Populate form fields using name attributes
                    $('input[name="id"]').val(language.id);
                    $('input[name="code"]').val(language.code);
                    $('input[name="name"]').val(language.name);
                    $('input[name="native_name"]').val(language.native_name);
                    $('select[name="is_active"]').val(language.is_active);
                    
                    // Update the update button URL with the actual language ID
                    var updateUrl = "{{ route('settings.languages.update', ['id' => '__id__']) }}".replace('__id__', id);
                    $('#update_form_btn').attr('data-url', updateUrl);
                    
                    $("#modal_create_form").modal('show');
                }
            },
            error: function(xhr) {
                showErrorMessage("{{ __('settings.languages.messages.load_failed') }}");
            }
        });
    }

    // handle click event for "Add" button
    $('.add_new').on('click', function(){
        $("#create_form_btn").show();
        $("#update_form_btn").hide();
        $("#modal_create_form .modal-title").text("{{ __('settings.languages.add_new') }}");
        
        // Reset form fields using name attributes
        $('input[name="id"]').val('');
        $('input[name="code"]').val('');
        $('input[name="name"]').val('');
        $('input[name="native_name"]').val('');
        $('select[name="is_active"]').val('1');
        
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
                    $('#languages-table').DataTable().ajax.reload(null, false);
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
                    showErrorMessage("{{ __('settings.languages.messages.operation_failed') }}");
                }
            }
        });
    });
});
</script>
@endpush