@extends('layouts.app')

@section('title', __('settings.rate_schemes.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('rate-schemes.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('settings.rate_schemes.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('rate-schemes.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('settings.rate_schemes.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="rate-schemes-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('settings.rate_schemes.letter_code') }}</th>
                                        <th>{{ __('settings.rate_schemes.hourly_rate') }}</th>
                                        <th>{{ __('settings.rate_schemes.description') }}</th>
                                        <th>{{ __('settings.rate_schemes.created_at') }}</th>
                                        <th>{{ __('settings.rate_schemes.action') }}</th>
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
                <h2 class="mb-2 mx-2">{{ __('settings.rate_schemes.access_denied_title') }}</h2>
                <p class="mb-4 mx-2">{{ __('settings.rate_schemes.access_denied_message') }}</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('settings.rate_schemes.back_to_dashboard') }}</a>
            </div>
        </div>
    @endif

    @if(_has_permission('rate-schemes.create') || _has_permission('rate-schemes.edit'))
        <!-- Add/Edit Rate Scheme Modal -->
        <div class="modal fade" id="modal_create_form" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="create-form">
                        <div class="modal-header">
                            <h5 class='modal-title'>{{ __('settings.rate_schemes.add_new') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id">
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.rate_schemes.form.letter_code') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="letter_code" placeholder="{{ __('settings.rate_schemes.placeholders.letter_code') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.rate_schemes.form.hourly_rate') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" name="hourly_rate" placeholder="{{ __('settings.rate_schemes.placeholders.hourly_rate') }}" class="form-control" required step="0.01" min="0">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('settings.rate_schemes.form.description') }}</label>
                                <div class="col-sm-9">
                                    <textarea name="description" placeholder="{{ __('settings.rate_schemes.placeholders.description') }}" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('settings.rate_schemes.close') }}</button>
                            <button type="button" data-url="{{ route('settings.rate-schemes.store') }}" id="create_form_btn" class="btn btn-primary">{{ __('settings.rate_schemes.submit') }}</button>
                            <button type="button" data-url="{{ route('settings.rate-schemes.update', ':id') }}" id="update_form_btn" class="btn btn-primary">{{ __('settings.rate_schemes.update') }}</button>
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
    var table = $('#rate-schemes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('settings.rate-schemes.index') }}",
        },
        columns: [
            { data: 'letter_code', name: 'letter_code' },
            { data: 'hourly_rate_formatted', name: 'hourly_rate', orderable: false },
            { data: 'description', name: 'description' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            processing: "{{ __('settings.rate_schemes.datatable.processing') }}",
            search: "{{ __('settings.rate_schemes.datatable.search') }}",
            lengthMenu: "{{ __('settings.rate_schemes.datatable.lengthMenu') }}",
            info: "{{ __('settings.rate_schemes.datatable.info') }}",
            infoEmpty: "{{ __('settings.rate_schemes.datatable.infoEmpty') }}",
            infoFiltered: "{{ __('settings.rate_schemes.datatable.infoFiltered') }}",
            loadingRecords: "{{ __('settings.rate_schemes.datatable.loadingRecords') }}",
            zeroRecords: "{{ __('settings.rate_schemes.datatable.zeroRecords') }}",
            emptyTable: "{{ __('settings.rate_schemes.datatable.emptyTable') }}",
            paginate: {
                first: "{{ __('settings.rate_schemes.datatable.paginate.first') }}",
                previous: "{{ __('settings.rate_schemes.datatable.paginate.previous') }}",
                next: "{{ __('settings.rate_schemes.datatable.paginate.next') }}",
                last: "{{ __('settings.rate_schemes.datatable.paginate.last') }}"
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> {{ __("settings.rate_schemes.export_excel") }}',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3] // Exclude Action column
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> {{ __("settings.rate_schemes.export_pdf") }}',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3] // Exclude Action column
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> {{ __("settings.rate_schemes.export_print") }}',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3] // Exclude Action column
                }
            }
        ]
    });

    // Handle edit button click
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        // Open edit modal
        $("#modal_create_form .modal-title").text("{{ __('settings.rate_schemes.edit_title') }}");
        $("#create_form_btn").hide();
        $("#update_form_btn").show();
        
        // Load rate scheme data for editing
        loadRateSchemeData(id);
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('settings.rate_schemes.messages.confirm_delete') }}")) {
            $.ajax({
                url: "{{ route('settings.rate-schemes.destroy', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#rate-schemes-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('settings.rate_schemes.messages.delete_failed') }}");
                }
            });
        }
    });

    // Handle restore button click
    $(document).on('click', '.restore-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm("{{ __('settings.rate_schemes.messages.confirm_restore') }}")) {
            $.ajax({
                url: "{{ route('settings.rate-schemes.restore', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#rate-schemes-table').DataTable().ajax.reload(null, false);
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function(xhr) {
                    showErrorMessage("{{ __('settings.rate_schemes.messages.restore_failed') }}");
                }
            });
        }
    });

    // Function to load rate scheme data for editing
    function loadRateSchemeData(id) {
        $.ajax({
            url: "{{ route('settings.rate-schemes.show', ['id' => '__id__']) }}".replace('__id__', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var rateScheme = response.data;
                    
                    // Populate form fields using name attributes
                    $('input[name="id"]').val(rateScheme.id);
                    $('input[name="letter_code"]').val(rateScheme.letter_code);
                    $('input[name="hourly_rate"]').val(rateScheme.hourly_rate);
                    $('textarea[name="description"]').val(rateScheme.description);
                    
                    // Update the update button URL with the actual rate scheme ID
                    var updateUrl = "{{ route('settings.rate-schemes.update', ['id' => '__id__']) }}".replace('__id__', id);
                    $('#update_form_btn').attr('data-url', updateUrl);
                    
                    $("#modal_create_form").modal('show');
                }
            },
            error: function(xhr) {
                showErrorMessage("{{ __('settings.rate_schemes.messages.load_failed') }}");
            }
        });
    }

    // handle click event for "Add" button
    $('.add_new').on('click', function(){
        $("#create_form_btn").show();
        $("#update_form_btn").hide();
        $("#modal_create_form .modal-title").text("{{ __('settings.rate_schemes.add_new') }}");
        
        // Reset form fields using name attributes
        $('input[name="id"]').val('');
        $('input[name="letter_code"]').val('');
        $('input[name="hourly_rate"]').val('');
        $('textarea[name="description"]').val('');
        
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
                    $('#rate-schemes-table').DataTable().ajax.reload(null, false);
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
                    showErrorMessage("{{ __('settings.rate_schemes.messages.operation_failed') }}");
                }
            }
        });
    });
});
</script>
@endpush