@extends('layouts.app')

@section('title', __('courses.title'))

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('courses.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('courses.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('courses.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('courses.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="courses-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('courses.id') }}</th>
                                        <th>{{ __('courses.name') }}</th>
                                        <th>{{ __('courses.description') }}</th>
                                        <th>{{ __('courses.language') }}</th>
                                        <th>{{ __('courses.level') }}</th>
                                        <th>{{ __('courses.total_hours') }}</th>
                                        <th>{{ __('courses.teaching_hours') }}</th>
                                        <th>{{ __('courses.mode') }}</th>
                                        <th>{{ __('courses.status') }}</th>
                                        <th>{{ __('courses.created_at') }}</th>
                                        <th>{{ __('courses.action') }}</th>
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
                            <h3>{{ __('courses.access_denied_title') }}</h3>
                            <p>{{ __('courses.access_denied_message') }}</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('courses.back_to_dashboard') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Add/Edit Modal with Tabs -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">{{ __('courses.add_new') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="courseTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info" aria-selected="true">
                            {{ __('courses.basic_info') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="topics-tab" data-bs-toggle="tab" data-bs-target="#topics" type="button" role="tab" aria-controls="topics" aria-selected="false">
                            {{ __('courses.topics') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" aria-controls="teachers" aria-selected="false">
                            {{ __('courses.teachers') }}
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="courseTabContent">
                    <!-- Basic Info Tab -->
                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                        <form id="courseForm">
                            @csrf
                            <input type="hidden" id="course_id" name="id">
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ __('courses.form.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">{{ __('courses.form.code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="code" name="code" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="language_id" class="form-label">{{ __('courses.form.language') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="language_id" name="language_id" required>
                                            <option value="">{{ __('courses.form.select_language') }}</option>
                                            @foreach($languages as $language)
                                                <option value="{{ $language->id }}">{{ $language->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="level_id" class="form-label">{{ __('courses.form.level') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="level_id" name="level_id" required>
                                            <option value="">{{ __('courses.form.select_level') }}</option>
                                            @foreach($levels as $level)
                                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rate_scheme_id" class="form-label">{{ __('courses.form.rate_scheme') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="rate_scheme_id" name="rate_scheme_id" required>
                                            <option value="">{{ __('courses.form.select_rate_scheme') }}</option>
                                            @foreach($rateSchemes as $scheme)
                                                <option value="{{ $scheme->id }}">{{ $scheme->letter_code }} - {{ $scheme->formatted_rate }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="mode" class="form-label">{{ __('courses.form.mode') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="mode" name="mode" required>
                                            <option value="">{{ __('courses.form.select_mode') }}</option>
                                            <option value="in_person">{{ __('courses.form.in_person') }}</option>
                                            <option value="virtual">{{ __('courses.form.virtual') }}</option>
                                            <option value="hybrid">{{ __('courses.form.hybrid') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="total_hours" class="form-label">{{ __('courses.form.total_hours') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="total_hours" name="total_hours" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="teaching_hours" class="form-label">{{ __('courses.form.teaching_hours') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="teaching_hours" name="teaching_hours" min="1" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('courses.form.description') }}</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">{{ __('courses.form.status') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="">{{ __('courses.form.select_status') }}</option>
                                            <option value="pending">{{ __('courses.form.pending') }}</option>
                                            <option value="active">{{ __('courses.form.active') }}</option>
                                            <option value="inactive">{{ __('courses.form.inactive') }}</option>
                                            <option value="completed">{{ __('courses.form.completed') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                            <label class="form-check-label" for="is_active">
                                                {{ __('courses.form.is_active') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Topics Tab -->
                    <div class="tab-pane fade" id="topics" role="tabpanel" aria-labelledby="topics-tab">
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>{{ __('courses.available_topics') }}</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="loadTopicsBtn">
                                    <i class="fas fa-sync"></i> {{ __('courses.load_topics') }}
                                </button>
                            </div>
                            
                            <div id="topics-loading" class="text-center py-3" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">{{ __('courses.loading') }}</span>
                                </div>
                                <p class="mt-2">{{ __('courses.loading_topics') }}</p>
                            </div>

                            <div id="topics-container" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <p class="text-muted text-center">{{ __('courses.select_language_level_first') }}</p>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <button type="button" class="btn btn-success" id="saveTopicsBtn" style="display: none;">
                                    <i class="fas fa-save"></i> {{ __('courses.save_topics') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Teachers Tab -->
                    <div class="tab-pane fade" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>{{ __('courses.available_teachers') }}</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="loadTeachersBtn">
                                    <i class="fas fa-sync"></i> {{ __('courses.load_teachers') }}
                                </button>
                            </div>
                            
                            <div id="teachers-loading" class="text-center py-3" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">{{ __('courses.loading') }}</span>
                                </div>
                                <p class="mt-2">{{ __('courses.loading_teachers') }}</p>
                            </div>

                            <div id="teachers-container" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <p class="text-muted text-center">{{ __('courses.select_language_level_first') }}</p>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <button type="button" class="btn btn-success" id="saveTeachersBtn" style="display: none;">
                                    <i class="fas fa-save"></i> {{ __('courses.save_teachers') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('courses.close') }}</button>
                <button type="button" class="btn btn-primary" id="submitBtn">{{ __('courses.submit') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewCourseModal" tabindex="-1" aria-labelledby="viewCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCourseModalLabel">{{ __('courses.view_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewCourseModalBody">
                <!-- Course details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('js-link')
    @include('partials.common.datatable_script')
    <script>
        $(document).ready(function() {
            let currentCourseId = null;
            let availableTopics = [];
            let availableTeachers = [];
            let selectedTopics = [];
            let selectedTeachers = [];

            // Initialize DataTable
            var table = $('#courses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('courses.index') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'language_name', name: 'language_name' },
                    { data: 'course_level_name', name: 'course_level_name' },
                    { data: 'total_hours', name: 'total_hours' },
                    { data: 'teaching_hours', name: 'teaching_hours' },
                    { data: 'mode', name: 'mode' },
                    { data: 'status', name: 'status' },
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
                openCourseModal();
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                openCourseModal(id);
            });

            // Open course modal
            function openCourseModal(courseId = null) {
                currentCourseId = courseId;
                
                if (courseId) {
                    $('#courseModalLabel').text('{{ __("courses.edit_title") }}');
                    $('#submitBtn').text('{{ __("courses.update") }}');
                    loadCourseData(courseId);
                } else {
                    $('#courseModalLabel').text('{{ __("courses.add_new") }}');
                    $('#submitBtn').text('{{ __("courses.submit") }}');
                    $('#courseForm')[0].reset();
                    $('#course_id').val('');
                    resetTabs();
                }
                
                // Reset to basic info tab
                $('#basic-info-tab').tab('show');
                
                $('#courseModal').modal('show');
            }

            // Load course data for editing
            function loadCourseData(courseId) {
                $.ajax({
                    url: "{{ route('courses.edit', '__id__') }}".replace('__id__', courseId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            var course = response.data;
                            $('#course_id').val(course.id);
                            $('#name').val(course.name);
                            $('#code').val(course.code);
                            $('#description').val(course.description);
                            $('#language_id').val(course.language_id);
                            $('#level_id').val(course.level_id);
                            $('#rate_scheme_id').val(course.rate_scheme_id);
                            $('#teaching_hours').val(course.teaching_hours);
                            $('#total_hours').val(course.total_hours);
                            $('#mode').val(course.mode);
                            $('#status').val(course.status);
                            $('#is_active').prop('checked', course.is_active);
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("courses.messages.error_fetching") }}');
                    }
                });
            }

            // Reset tabs
            function resetTabs() {
                $('#topics-container').html('<p class="text-muted text-center">{{ __("courses.select_language_level_first") }}</p>');
                $('#teachers-container').html('<p class="text-muted text-center">{{ __("courses.select_language_level_first") }}</p>');
                selectedTopics = [];
                selectedTeachers = [];
            }

            // Load topics when topics tab is clicked
            $('#topics-tab').on('click', function() {
                if (currentCourseId && $('#language_id').val() && $('#level_id').val()) {
                    loadCourseTopics();
                } else if (!$('#language_id').val() || !$('#level_id').val()) {
                    $('#topics-container').html('<p class="text-muted text-center">{{ __("courses.select_language_level_first") }}</p>');
                }
            });

            // Load teachers when teachers tab is clicked
            $('#teachers-tab').on('click', function() {
                if (currentCourseId) {
                    loadCourseTeachers();
                }
            });

            // Load topics button
            $('#loadTopicsBtn').on('click', function() {
                if (currentCourseId && $('#language_id').val() && $('#level_id').val()) {
                    loadCourseTopics();
                } else {
                    alert('{{ __("courses.select_language_level_first") }}');
                }
            });

            // Load teachers button
            $('#loadTeachersBtn').on('click', function() {
                if (currentCourseId) {
                    loadCourseTeachers();
                }
            });

            // Save teachers button
            $('#saveTeachersBtn').on('click', function() {
                if (currentCourseId) {
                    saveCourseTeachers();
                }
            });

            // Load course topics
            function loadCourseTopics() {
                $('#topics-loading').show();
                $('#topics-container').hide();

                $.ajax({
                    url: "{{ route('courses.topics', '__id__') }}".replace('__id__', currentCourseId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            availableTopics = response.data.available_topics;
                            selectedTopics = response.data.assigned_topic_ids;
                            renderTopicsList();
                        } else {
                            $('#topics-container').html('<p class="text-danger">{{ __("courses.error_loading_topics") }}</p>');
                        }
                    },
                    error: function(xhr) {
                        $('#topics-container').html('<p class="text-danger">{{ __("courses.error_loading_topics") }}</p>');
                    },
                    complete: function() {
                        $('#topics-loading').hide();
                        $('#topics-container').show();
                    }
                });
            }

            // Load course teachers
            function loadCourseTeachers() {
                $('#teachers-loading').show();
                $('#teachers-container').hide();

                $.ajax({
                    url: "{{ route('courses.teachers', '__id__') }}".replace('__id__', currentCourseId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            availableTeachers = response.data.available_teachers;
                            selectedTeachers = response.data.assigned_teacher_ids;
                            renderTeachersList();
                        } else {
                            $('#teachers-container').html('<p class="text-danger">{{ __("courses.error_loading_teachers") }}</p>');
                        }
                    },
                    error: function(xhr) {
                        $('#teachers-container').html('<p class="text-danger">{{ __("courses.error_loading_teachers") }}</p>');
                    },
                    complete: function() {
                        $('#teachers-loading').hide();
                        $('#teachers-container').show();
                    }
                });
            }

            // Render topics list
            function renderTopicsList() {
                let html = '';
                
                if (availableTopics.length === 0) {
                    html = '<p class="text-muted text-center">{{ __("courses.no_topics_available") }}</p>';
                    $('#saveTopicsBtn').hide();
                } else {
                    availableTopics.forEach(topic => {
                        const isSelected = selectedTopics.includes(topic.id);
                        html += `
                            <div class="form-check mb-2">
                                <input class="form-check-input topic-checkbox" type="checkbox" value="${topic.id}" id="topic_${topic.id}" ${isSelected ? 'checked' : ''}>
                                <label class="form-check-label" for="topic_${topic.id}">
                                    <strong>${topic.title}</strong>
                                    <br><small class="text-muted">${topic.description || 'No description'}</small>
                                </label>
                            </div>
                        `;
                    });
                    $('#saveTopicsBtn').show();
                }
                
                $('#topics-container').html(html);
            }

            // Render teachers list
            function renderTeachersList() {
                let html = '';
                
                if (availableTeachers.length === 0) {
                    html = '<p class="text-muted text-center">{{ __("courses.no_teachers_available") }}</p>';
                    $('#saveTeachersBtn').hide();
                } else {
                    availableTeachers.forEach(teacher => {
                        const isSelected = selectedTeachers.includes(teacher.id);
                        html += `
                            <div class="form-check mb-2">
                                <input class="form-check-input teacher-checkbox" type="checkbox" value="${teacher.id}" id="teacher_${teacher.id}" ${isSelected ? 'checked' : ''}>
                                <label class="form-check-label" for="teacher_${teacher.id}">
                                    <strong>${teacher.first_name} ${teacher.last_name}</strong>
                                    <br><small class="text-muted">${teacher.email}</small>
                                </label>
                            </div>
                        `;
                    });
                    $('#saveTeachersBtn').show();
                }
                
                $('#teachers-container').html(html);
            }

            // Form submit
            $('#submitBtn').on('click', function() {
                submitCourseForm();
            });

            // Submit course form
            function submitCourseForm() {
                var formData = new FormData($('#courseForm')[0]);
                var url = currentCourseId ? 
                    "{{ route('courses.update', '__id__') }}".replace('__id__', currentCourseId) : 
                    "{{ route('courses.store') }}";
                
                if (currentCourseId) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert(currentCourseId ? '{{ __("courses.messages.updated") }}' : '{{ __("courses.messages.created") }}');
                            $('#courseModal').modal('hide');
                            table.ajax.reload();
                            
                            // If it's a new course, save topics and teachers
                            if (!currentCourseId && response.data && response.data.id) {
                                currentCourseId = response.data.id;
                                saveCourseTopicsAndTeachers();
                            }
                        } else {
                            alert('{{ __("courses.messages.error_saving") }}');
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("courses.messages.error_saving") }}');
                    }
                });
            }

            // Save course teachers
            function saveCourseTeachers() {
                const selectedTeacherIds = $('.teacher-checkbox:checked').map(function() {
                    return parseInt($(this).val());
                }).get();

                $.ajax({
                    url: "{{ route('courses.teachers.save', '__id__') }}".replace('__id__', currentCourseId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        teacher_ids: selectedTeacherIds
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('{{ __("courses.teachers_saved") }}');
                            // Reload the teachers list to show updated assignments
                            loadCourseTeachers();
                        } else {
                            alert('{{ __("courses.teachers_save_failed") }}');
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("courses.teachers_save_failed") }}');
                    }
                });
            }

            // Save course topics and teachers
            function saveCourseTopicsAndTeachers() {
                // Save topics
                const selectedTopicIds = $('.topic-checkbox:checked').map(function() {
                    return parseInt($(this).val());
                }).get();

                if (selectedTopicIds.length > 0) {
                    $.ajax({
                        url: "{{ route('courses.topics.save', '__id__') }}".replace('__id__', currentCourseId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            topic_ids: selectedTopicIds
                        },
                        success: function(response) {
                            console.log('Topics saved successfully');
                        },
                        error: function(xhr) {
                            console.error('Failed to save topics');
                        }
                    });
                }

                // Save teachers
                const selectedTeacherIds = $('.teacher-checkbox:checked').map(function() {
                    return parseInt($(this).val());
                }).get();

                if (selectedTeacherIds.length > 0) {
                    $.ajax({
                        url: "{{ route('courses.teachers.save', '__id__') }}".replace('__id__', currentCourseId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            teacher_ids: selectedTeacherIds
                        },
                        success: function(response) {
                            console.log('Teachers saved successfully');
                        },
                        error: function(xhr) {
                            console.error('Failed to save teachers');
                        }
                    });
                }
            }

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('{{ __("courses.messages.delete_confirm") }}')) {
                    $.ajax({
                        url: "{{ route('courses.destroy', '__id__') }}".replace('__id__', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('{{ __("courses.messages.deleted") }}');
                                table.ajax.reload();
                            } else {
                                alert('{{ __("courses.messages.error_deleting") }}');
                            }
                        },
                        error: function(xhr) {
                            alert('{{ __("courses.messages.error_deleting") }}');
                        }
                    });
                }
            });

            // Restore button click
            $(document).on('click', '.restore-btn', function() {
                var id = $(this).data('id');
                if (confirm('{{ __("courses.messages.restore_confirm") }}')) {
                    $.ajax({
                        url: "{{ route('courses.restore', '__id__') }}".replace('__id__', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('{{ __("courses.messages.restored") }}');
                                table.ajax.reload();
                            } else {
                                alert('{{ __("courses.messages.error_restoring") }}');
                            }
                        },
                        error: function(xhr) {
                            alert('{{ __("courses.messages.error_restoring") }}');
                        }
                    });
                }
            });

            // Export functions
            window.exportToExcel = function() {
                window.open("{{ route('courses.export.excel') }}", '_blank');
            };

            window.exportToPDF = function() {
                window.open("{{ route('courses.export.pdf') }}", '_blank');
            };
        });
    </script>
@endpush