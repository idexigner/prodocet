@extends('layouts.app')

@section('title', __('groups.title'))

@push('css-link')
    @include('partials.common.datatable_style')
@endpush

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('groups.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('groups.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('groups.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('groups.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="groups-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('groups.id') }}</th>
                                        <th>{{ __('groups.name') }}</th>
                                        <th>{{ __('groups.description') }}</th>
                                        <th>{{ __('groups.course') }}</th>
                                        <th>{{ __('groups.teacher') }}</th>
                                        <th>{{ __('groups.max_students') }}</th>
                                        <th>{{ __('groups.current_students') }}</th>
                                        <th>{{ __('groups.status') }}</th>
                                        <th>{{ __('groups.start_date') }}</th>
                                        <th>{{ __('groups.created_at') }}</th>
                                        <th>{{ __('groups.action') }}</th>
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
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3>{{ __('groups.access_denied_title') }}</h3>
                            <p>{{ __('groups.access_denied_message') }}</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('groups.back_to_dashboard') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupModalLabel">{{ __('groups.add_new') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="groupModalBody">
                <!-- Form will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewGroupModal" tabindex="-1" aria-labelledby="viewGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewGroupModalLabel">{{ __('groups.view_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewGroupModalBody">
                <!-- Group details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Hidden form template -->
<div id="group-form-template" style="display: none;">
    <form id="groupForm">
        @csrf
        <input type="hidden" id="group_id" name="id">
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('groups.form.name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="code" class="form-label">{{ __('groups.form.code') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="course_id" class="form-label">{{ __('groups.form.course') }} <span class="text-danger">*</span></label>
                    <select class="form-control" id="course_id" name="course_id" required>
                        <option value="">{{ __('groups.form.select_course') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">{{ __('groups.form.teacher') }} <span class="text-danger">*</span></label>
                    <select class="form-control" id="teacher_id" name="teacher_id" required>
                        <option value="">{{ __('groups.form.select_teacher') }}</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="max_students" class="form-label">{{ __('groups.form.max_students') }} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="max_students" name="max_students" min="1" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="current_students" class="form-label">{{ __('groups.form.current_students') }}</label>
                    <input type="number" class="form-control" id="current_students" name="current_students" min="0" value="0">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="start_date" class="form-label">{{ __('groups.form.start_date') }} <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="end_date" class="form-label">{{ __('groups.form.end_date') }}</label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="classroom" class="form-label">{{ __('groups.form.classroom') }}</label>
                    <input type="text" class="form-control" id="classroom" name="classroom">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="virtual_link" class="form-label">{{ __('groups.form.virtual_link') }}</label>
                    <input type="url" class="form-control" id="virtual_link" name="virtual_link">
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">{{ __('groups.form.description') }}</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="status" class="form-label">{{ __('groups.form.status') }} <span class="text-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="">{{ __('groups.form.select_status') }}</option>
                        <option value="active">{{ __('groups.form.active') }}</option>
                        <option value="inactive">{{ __('groups.form.inactive') }}</option>
                        <option value="completed">{{ __('groups.form.completed') }}</option>
                        <option value="cancelled">{{ __('groups.form.cancelled') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">
                            {{ __('groups.form.is_active') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('groups.close') }}</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('groups.submit') }}</button>
        </div>
    </form>
</div>


@endsection

@push('js-link')
    @include('partials.common.datatable_script')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#groups-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('groups.index') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'course_name', name: 'course_name' },
                    { data: 'teacher_name', name: 'teacher_name' },
                    { data: 'max_students', name: 'max_students' },
                    { data: 'current_students', name: 'current_students' },
                    { data: 'status', name: 'status' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                responsive: true,
                language: {
                    url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
                }
            });

            // Add new button click
            $('.add_new').click(function() {
                $('#groupModalLabel').text('{{ __("groups.add_new") }}');
                $('#submitBtn').text('{{ __("groups.submit") }}');
                $('#groupForm')[0].reset();
                $('#group_id').val('');
                $('#groupModalBody').html($('#group-form-template').html());
                loadFormData();
                $('#groupModal').modal('show');
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('groups.edit', '__id__') }}".replace('__id__', id),
                    type: 'GET',
                    success: function(response) {
                        $('#groupModalLabel').text('{{ __("groups.edit_title") }}');
                        $('#submitBtn').text('{{ __("groups.update") }}');
                        
                        // Use the same form template
                        $('#groupModalBody').html($('#group-form-template').html());
                        
                        // Load form data (courses, teachers) first
                        loadFormData();
                        
                        // Populate form with data
                        if (response.success && response.data) {
                            var group = response.data;
                            $('#group_id').val(group.id);
                            $('#name').val(group.name);
                            $('#code').val(group.code);
                            $('#description').val(group.description);
                            $('#course_id').val(group.course_id);
                            $('#teacher_id').val(group.teacher_id);
                            $('#max_students').val(group.max_students);
                            $('#current_students').val(group.current_students);
                            $('#status').val(group.status);
                            
                            // Format dates for HTML date input
                            if (group.start_date) {
                                const startDate = new Date(group.start_date);
                                const formattedStartDate = startDate.toISOString().split('T')[0];
                                $('#start_date').val(formattedStartDate);
                            } else {
                                $('#start_date').val('');
                            }
                            
                            if (group.end_date) {
                                const endDate = new Date(group.end_date);
                                const formattedEndDate = endDate.toISOString().split('T')[0];
                                $('#end_date').val(formattedEndDate);
                            } else {
                                $('#end_date').val('');
                            }
                            
                            $('#classroom').val(group.classroom);
                            $('#virtual_link').val(group.virtual_link);
                            $('#is_active').prop('checked', group.is_active);
                        }
                        
                        $('#groupModal').modal('show');
                    },
                    error: function(xhr) {
                        alert('{{ __("groups.messages.error_fetching") }}');
                    }
                });
            });

            // View button click
            $(document).on('click', '.view-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('groups.show', '__id__') }}".replace('__id__', id),
                    type: 'GET',
                    success: function(response) {
                        $('#viewGroupModalBody').html(response);
                        $('#viewGroupModal').modal('show');
                    },
                    error: function(xhr) {
                        alert('{{ __("groups.messages.error_fetching") }}');
                    }
                });
            });

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('{{ __("groups.messages.delete_confirm") }}')) {
                    $.ajax({
                        url: "{{ route('groups.destroy', '__id__') }}".replace('__id__', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('{{ __("groups.messages.deleted") }}');
                                table.ajax.reload();
                            } else {
                                alert('{{ __("groups.messages.error_deleting") }}');
                            }
                        },
                        error: function(xhr) {
                            alert('{{ __("groups.messages.error_deleting") }}');
                        }
                    });
                }
            });

            // Restore button click
            $(document).on('click', '.restore-btn', function() {
                var id = $(this).data('id');
                if (confirm('{{ __("groups.messages.restore_confirm") }}')) {
                    $.ajax({
                        url: "{{ route('groups.restore', '__id__') }}".replace('__id__', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('{{ __("groups.messages.restored") }}');
                                table.ajax.reload();
                            } else {
                                alert('{{ __("groups.messages.error_restoring") }}');
                            }
                        },
                        error: function(xhr) {
                            alert('{{ __("groups.messages.error_restoring") }}');
                        }
                    });
                }
            });

            // Form submit
            $(document).on('submit', '#groupForm', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                var url = $('#group_id').val() ? 
                    "{{ route('groups.update', '__id__') }}".replace('__id__', $('#group_id').val()) : 
                    "{{ route('groups.store') }}";
                
                var method = $('#course_id').val() ? 'POST' : 'POST';
                if ($('#group_id').val()) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert($('#group_id').val() ? '{{ __("groups.messages.updated") }}' : '{{ __("groups.messages.created") }}');
                            $('#groupModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            alert('{{ __("groups.messages.error_saving") }}');
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("groups.messages.error_saving") }}');
                    }
                });
            });

            // Load form data (courses, teachers)
            function loadFormData() {
                // Load courses
                var courseSelect = $('#course_id');
                courseSelect.empty().append('<option value="">{{ __("groups.form.select_course") }}</option>');
                @foreach($courses as $course)
                    courseSelect.append('<option value="{{ $course->id }}">{{ $course->name }}</option>');
                @endforeach

                // Load teachers
                var teacherSelect = $('#teacher_id');
                teacherSelect.empty().append('<option value="">{{ __("groups.form.select_teacher") }}</option>');
                @foreach($teachers as $teacher)
                    teacherSelect.append('<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>');
                @endforeach
            }

            // Export functions
            window.exportToExcel = function() {
                window.open("{{ route('groups.export.excel') }}", '_blank');
            };

            window.exportToPDF = function() {
                window.open("{{ route('groups.export.pdf') }}", '_blank');
            };
        });
    </script>
@endpush