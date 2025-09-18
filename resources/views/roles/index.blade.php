@extends('layouts.app')

@section('title', __('roles.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission(auth()->user(), 'roles.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('roles.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission(auth()->user(), 'roles.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('roles.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="roles-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('roles.id') }}</th>
                                        <th>{{ __('roles.name') }}</th>
                                        <th>{{ __('roles.display_name') }}</th>
                                        <th>{{ __('roles.description') }}</th>
                                        <th>{{ __('roles.permissions') }}</th>
                                        <th>{{ __('roles.status') }}</th>
                                        <th>{{ __('roles.created_at') }}</th>
                                        <th>{{ __('roles.action') }}</th>
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
                <h2 class="mb-2 mx-2">{{ __('roles.access_denied_title') }}</h2>
                <p class="mb-4 mx-2">{{ __('roles.access_denied_message') }}</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('roles.back_to_dashboard') }}</a>
            </div>
        </div>
    @endif

    @if(_has_permission(auth()->user(), 'roles.create') || _has_permission(auth()->user(), 'roles.edit'))
        <!-- Add/Edit Role Modal -->
        <div class="modal fade" id="modal_create_form" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="create-form">
                        <div class="modal-header">
                            <h5 class='modal-title'>{{ __('roles.add_new') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id">
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('roles.role_name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" placeholder="{{ __('roles.role_name_placeholder') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('roles.display_name') }} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="display_name" placeholder="{{ __('roles.display_name_placeholder') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('roles.description') }}</label>
                                <div class="col-sm-9">
                                    <textarea name="description" placeholder="{{ __('roles.description_placeholder') }}" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('roles.status') }}</label>
                                <div class="col-sm-9">
                                    <select name="is_active" class="form-select">
                                        <option value="1">{{ __('roles.status_active') }}</option>
                                        <option value="0">{{ __('roles.status_inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('roles.permissions') }}</label>
                                <div class="col-sm-9">
                                    <div class="permissions-container" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                        <!-- Permissions will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('roles.close') }}</button>
                            <button type="button" data-url="{{ route('roles.store') }}" id="create_form_btn" class="btn btn-primary">{{ __('roles.submit') }}</button>
                            <button type="button" data-url="{{ route('roles.update', ':id') }}" id="update_form_btn" class="btn btn-primary">{{ __('roles.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('js-link')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with Yajra
            var table = $('#roles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('roles.index') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id', visible: false },
                    { data: 'name', name: 'name' },
                    { data: 'display_name', name: 'display_name' },
                    { data: 'description', name: 'description' },
                    { data: 'permissions_count', name: 'permissions_count', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[6, 'desc']], // Order by created_at desc
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    processing: "{{ __('roles.processing') }}",
                    lengthMenu: "{{ __('roles.length_menu') }}",
                    zeroRecords: "{{ __('roles.zero_records') }}",
                    emptyTable: "{{ __('roles.empty_table') }}",
                    info: "{{ __('roles.info') }}",
                    infoEmpty: "{{ __('roles.info_empty') }}",
                    infoFiltered: "{{ __('roles.info_filtered') }}",
                    search: "{{ __('roles.search') }}",
                    paginate: {
                        first: "{{ __('roles.first') }}",
                        last: "{{ __('roles.last') }}",
                        next: "{{ __('roles.next') }}",
                        previous: "{{ __('roles.previous') }}"
                    }
                },
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> {{ __("roles.export_excel") }}',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> {{ __("roles.export_pdf") }}',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> {{ __("roles.export_print") }}',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6] // Exclude ID and Action columns
                        }
                    }
                ]
            });

            // Load permissions when modal opens
            function loadPermissions(selectedPermissions = []) {
                $.get("{{ route('roles.create') }}", function(response) {
                    if (response.success) {
                        var permissionsHtml = '';
                        var permissions = response.data.permissions;
                        
                        // Group permissions by module
                        var groupedPermissions = {};
                        permissions.forEach(function(permission) {
                            var parts = permission.split('.');
                            var module = parts[0];
                            if (!groupedPermissions[module]) {
                                groupedPermissions[module] = [];
                            }
                            groupedPermissions[module].push(permission);
                        });
                        
                        // Create checkboxes for each module
                        Object.keys(groupedPermissions).forEach(function(module) {
                            permissionsHtml += '<div class="permission-group mb-3">';
                            permissionsHtml += '<h6>' + module.charAt(0).toUpperCase() + module.slice(1) + '</h6>';
                            groupedPermissions[module].forEach(function(permission) {
                                var isChecked = selectedPermissions.includes(permission) ? 'checked' : '';
                                permissionsHtml += '<div class="form-check">';
                                permissionsHtml += '<input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="' + permission + '" ' + isChecked + '>';
                                permissionsHtml += '<label class="form-check-label">' + permission + '</label>';
                                permissionsHtml += '</div>';
                            });
                            permissionsHtml += '</div>';
                        });
                        
                        $('.permissions-container').html(permissionsHtml);
                    }
                });
            }

            // Handle edit button click
            $(document).on('click', '.edit-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                // Open edit modal
                $("#modal_create_form .modal-title").text("{{ __('roles.edit_role') }}");
                $("#create_form_btn").hide();
                $("#update_form_btn").show();
                
                // Load role data for editing
                loadRoleData(id);
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                
                if (confirm("{{ __('roles.confirm_delete') }}")) {
                    $.ajax({
                        url: "{{ route('roles.destroy', '') }}/" + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#roles-table').DataTable().ajax.reload(null, false);
                                showSuccessMessage(response.message);
                            } else {
                                showErrorMessage(response.message);
                            }
                        },
                        error: function(xhr) {
                            showErrorMessage("{{ __('roles.error_delete') }}");
                        }
                    });
                }
            });

            // Handle restore button click
            $(document).on('click', '.restore-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                
                if (confirm("{{ __('roles.confirm_restore') }}")) {
                    $.ajax({
                        url: "{{ route('roles.restore', '') }}/" + id,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#roles-table').DataTable().ajax.reload(null, false);
                                showSuccessMessage(response.message);
                            } else {
                                showErrorMessage(response.message);
                            }
                        },
                        error: function(xhr) {
                            showErrorMessage("{{ __('roles.error_restore') }}");
                        }
                    });
                }
            });

            // Function to load role data for editing
            function loadRoleData(id) {
                $.ajax({
                    url: "{{ route('roles.show', '') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var role = response.data.role;
                            
                            // Populate form fields
                            $('input[name="id"]').val(role.id);
                            $('input[name="name"]').val(role.name);
                            $('input[name="display_name"]').val(role.display_name);
                            $('textarea[name="description"]').val(role.description);
                            $('select[name="is_active"]').val(role.is_active ? '1' : '0');
                            
                            // Update the update button URL with the actual role ID
                            var updateUrl = "{{ route('roles.update', '') }}/" + id;
                            $('#update_form_btn').attr('data-url', updateUrl);
                            
                            // Load permissions with selected permissions
                            var selectedPermissions = role.permissions ? role.permissions.map(p => p.name) : [];
                            loadPermissions(selectedPermissions);
                            
                            $("#modal_create_form").modal('show');
                        }
                    },
                    error: function(xhr) {
                        showErrorMessage("{{ __('roles.error_load_data') }}");
                    }
                });
            }

            // handle click event for "Add" button
            $('.add_new').on('click', function(){
                $("#create_form_btn").show();
                $("#update_form_btn").hide();
                $("#modal_create_form .modal-title").text("{{ __('roles.add_new') }}");
                $("#create-form")[0].reset();
                loadPermissions();
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
                
                // No need to add _method field since we're using POST for both create and update

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
                        $('#roles-table').DataTable().ajax.reload(null, false);
                        $("#modal_create_form").modal('hide');
                        _handleSuccess(response.message);
                    },
                    error: function(xhr) {
                        showErrorMessage("{{ __('roles.error_processing') }}");
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            });
            
        });
    </script>
@endpush

