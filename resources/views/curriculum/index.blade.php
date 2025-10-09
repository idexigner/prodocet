@extends('layouts.app')

@section('title', __('curriculum.title'))

@section('main-section')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(_has_permission('curriculum.view'))
        <div class="pagetitle row mt-4 mb-4">
            <div class="col-lg-6 mt-2">
                <h1>{{ __('curriculum.heading') }}</h1>
            </div>
            <div class="col-lg-6">
                @if(_has_permission('curriculum.create'))
                    <button type="button" class="btn btn-primary add_new" style="float: right">
                        {{ __('curriculum.add_new') }}
                    </button>
                @endif
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Loading indicator -->
                            <div id="curriculum-loading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">{{ __('curriculum.loading') }}</span>
                                </div>
                                <p class="mt-2">{{ __('curriculum.loading_topics') }}</p>
                            </div>

                            <!-- Accordion container -->
                            <div id="curriculum-accordion" class="accordion" style="display: none;">
                                <!-- Accordion items will be populated here -->
                            </div>

                            <!-- Empty state -->
                            <div id="curriculum-empty" class="text-center py-5" style="display: none;">
                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">{{ __('curriculum.no_topics_found') }}</h4>
                                <p class="text-muted">{{ __('curriculum.no_topics_description') }}</p>
                                @if(_has_permission('curriculum.create'))
                                    <button type="button" class="btn btn-primary add_new">
                                        {{ __('curriculum.add_first_topic') }}
                                    </button>
                                @endif
                            </div>
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
                            <h3>{{ __('curriculum.access_denied_title') }}</h3>
                            <p>{{ __('curriculum.access_denied_message') }}</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('curriculum.back_to_dashboard') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="curriculumModal" tabindex="-1" aria-labelledby="curriculumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="curriculumModalLabel">{{ __('curriculum.add_new') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="curriculumModalBody">
                <!-- Form will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewCurriculumModal" tabindex="-1" aria-labelledby="viewCurriculumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCurriculumModalLabel">{{ __('curriculum.view_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewCurriculumModalBody">
                <!-- Curriculum details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Hidden form template -->
<div id="curriculum-form-template" style="display: none;">
    <form id="curriculumForm">
        @csrf
        <input type="hidden" id="curriculum_id" name="id">
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('curriculum.form.title') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="order_index" class="form-label">{{ __('curriculum.form.order_index') }} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="order_index" name="order_index" min="1" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="language_id" class="form-label">{{ __('curriculum.form.language') }} <span class="text-danger">*</span></label>
                    <select class="form-control" id="language_id" name="language_id" required>
                        <option value="">{{ __('curriculum.form.select_language') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="level_id" class="form-label">{{ __('curriculum.form.level') }} <span class="text-danger">*</span></label>
                    <select class="form-control" id="level_id" name="level_id" required>
                        <option value="">{{ __('curriculum.form.select_level') }}</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">{{ __('curriculum.form.description') }}</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="mb-3">
            <label for="content" class="form-label">{{ __('curriculum.form.content') }}</label>
            <textarea class="form-control" id="content" name="content" rows="5"></textarea>
        </div>
        
        <div class="mb-3">
            <label for="documents" class="form-label">{{ __('curriculum.form.documents') }}</label>
            <input type="file" class="form-control" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            <div id="document_preview" class="mt-2"></div>
            <input type="hidden" id="document_urls" name="document_urls">
            <input type="hidden" id="documents_to_remove" name="documents_to_remove">
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                <label class="form-check-label" for="is_active">
                    {{ __('curriculum.form.is_active') }}
                </label>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('curriculum.close') }}</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('curriculum.submit') }}</button>
        </div>
    </form>
</div>

@endsection

@push('js-link')
    <script>
        $(document).ready(function() {
            let curriculumData = [];
            let languages = @json($languages);
            let levels = @json($levels);

            // Load curriculum data on page load
            loadCurriculumData();

            // Load curriculum data from server
            function loadCurriculumData() {
                $('#curriculum-loading').show();
                $('#curriculum-accordion').hide();
                $('#curriculum-empty').hide();

                $.ajax({
                    url: "{{ route('curriculum.grouped') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            curriculumData = response.data;
                            renderAccordion();
                        } else {
                            showEmptyState();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load curriculum data:', xhr);
                        showEmptyState();
                    },
                    complete: function() {
                        $('#curriculum-loading').hide();
                    }
                });
            }

            // Render accordion with curriculum data
            function renderAccordion() {
                const accordion = $('#curriculum-accordion');
                accordion.empty();

                if (curriculumData.length === 0) {
                    showEmptyState();
                    return;
                }

                curriculumData.forEach((group, index) => {
                    const accordionId = `accordion-${index}`;
                    const collapseId = `collapse-${index}`;
                    
                    const accordionItem = $(`
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-${index}">
                                <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#${collapseId}" 
                                        aria-expanded="${index === 0 ? 'true' : 'false'}" 
                                        aria-controls="${collapseId}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <strong>${group.language_name} - ${group.level_name}</strong>
                                            <small class="text-muted ms-2">(${group.topics.length} topics)</small>
                                        </div>
                                        @if(_has_permission('curriculum.create'))
                                            <button type="button" class="btn btn-sm btn-primary add-topic-btn" 
                                                    data-language-id="${group.language_id}" 
                                                    data-level-id="${group.level_id}"
                                                    data-language-name="${group.language_name}"
                                                    data-level-name="${group.level_name}">
                                                <i class="fas fa-plus"></i> {{ __('curriculum.add_topic') }}
                                            </button>
                                        @endif
                                    </div>
                                </button>
                            </h2>
                            <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                                 aria-labelledby="heading-${index}" data-bs-parent="#curriculum-accordion">
                                <div class="accordion-body">
                                    <div class="topics-list">
                                        ${renderTopicsList(group.topics)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                    accordion.append(accordionItem);
                });

                accordion.show();
            }

            // Render topics list for a group
            function renderTopicsList(topics) {
                if (topics.length === 0) {
                    return `
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-book-open fa-2x mb-2"></i>
                            <p>{{ __('curriculum.no_topics_in_group') }}</p>
                        </div>
                    `;
                }

                let topicsHtml = '<div class="list-group">';
                
                topics.forEach(topic => {
                    const statusBadge = topic.deleted_at 
                        ? '<span class="badge bg-danger">Deleted</span>'
                        : (topic.is_active 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-warning">Inactive</span>');

                    const actions = `
                        <div class="btn-group btn-group-sm" role="group">
                            @if(_has_permission('curriculum.edit'))
                                <button class="btn btn-outline-primary edit-btn" data-id="${topic.id}" title="{{ __('curriculum.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endif
                            @if(_has_permission('curriculum.delete'))
                                ${topic.deleted_at 
                                    ? `<button class="btn btn-outline-success restore-btn" data-id="${topic.id}" title="{{ __('curriculum.restore') }}">
                                        <i class="fas fa-undo"></i>
                                    </button>`
                                    : `<button class="btn btn-outline-danger delete-btn" data-id="${topic.id}" title="{{ __('curriculum.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>`
                                }
                            @endif
                        </div>
                    `;

                    topicsHtml += `
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">${topic.title}</div>
                                <small class="text-muted">${topic.description || 'No description'}</small>
                                <div class="mt-1">
                                    ${statusBadge}
                                    <small class="text-muted ms-2">Order: ${topic.order_index}</small>
                                </div>
                            </div>
                            ${actions}
                        </div>
                    `;
                });

                topicsHtml += '</div>';
                return topicsHtml;
            }

            // Show empty state
            function showEmptyState() {
                $('#curriculum-empty').show();
            }

            // Add new topic button (main button)
            $('.add_new').click(function() {
                openTopicModal();
            });

            // Add topic button (within accordion)
            $(document).on('click', '.add-topic-btn', function() {
                const languageId = $(this).data('language-id');
                const levelId = $(this).data('level-id');
                const languageName = $(this).data('language-name');
                const levelName = $(this).data('level-name');
                
                openTopicModal(languageId, levelId, languageName, levelName);
            });

            // Open topic modal
            function openTopicModal(languageId = null, levelId = null, languageName = null, levelName = null) {
                $('#curriculumModalLabel').text('{{ __("curriculum.add_new") }}');
                $('#submitBtn').text('{{ __("curriculum.submit") }}');
                $('#curriculumForm')[0].reset();
                $('#curriculum_id').val('');
                $('#curriculumModalBody').html($('#curriculum-form-template').html());
                
                // Clear document arrays
                curriculumDocuments = [];
                removedDocuments = [];
                $('#document_preview').empty();
                
                loadFormData();
                
                // Pre-select language and level if provided
                if (languageId && levelId) {
                    setTimeout(() => {
                        $('#language_id').val(languageId);
                        $('#level_id').val(levelId);
                        // Disable the selects since they're pre-selected
                        $('#language_id, #level_id').prop('disabled', true);
                        
                        // Add hidden fields to ensure values are submitted even when disabled
                        if (!$('#hidden_language_id').length) {
                            $('#language_id').after('<input type="hidden" id="hidden_language_id" name="language_id" value="' + languageId + '">');
                        } else {
                            $('#hidden_language_id').val(languageId);
                        }
                        
                        if (!$('#hidden_level_id').length) {
                            $('#level_id').after('<input type="hidden" id="hidden_level_id" name="level_id" value="' + levelId + '">');
                        } else {
                            $('#hidden_level_id').val(levelId);
                        }
                    }, 200);
                } else {
                    $('#language_id, #level_id').prop('disabled', false);
                    // Remove hidden fields if they exist
                    $('#hidden_language_id, #hidden_level_id').remove();
                }
                
                $('#curriculumModal').modal('show');
            }

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('curriculum.edit', '__id__') }}".replace('__id__', id),
                    type: 'GET',
                    success: function(response) {
                        $('#curriculumModal').modal('show');
                        $('#curriculumModalLabel').text('{{ __("curriculum.edit_title") }}');
                        $('#submitBtn').text('{{ __("curriculum.update") }}');
                        
                        // Use the same form template
                        $('#curriculumModalBody').html($('#curriculum-form-template').html());
                        
                        // Load form data (languages, levels) first
                        loadFormData();
                        
                        // Populate form with data
                        if (response.success && response.data) {
                            var topic = response.data;
                            $('#curriculum_id').val(topic.id);
                            $('#title').val(topic.title);
                            $('#description').val(topic.description);
                            $('#content').val(topic.content);
                            $('#order_index').val(topic.order_index);
                            $('#language_id').val(topic.language_id);
                            $('#level_id').val(topic.level_id);
                            $('#is_active').prop('checked', topic.is_active);
                            
                            // Load existing documents
                            if (topic.documents && topic.documents.length > 0) {
                                loadExistingDocuments(topic.documents);
                            }
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("curriculum.messages.error_fetching") }}');
                    }
                });
            });

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('{{ __("curriculum.messages.confirm_delete") }}')) {
                    $.ajax({
                        url: "{{ route('curriculum.destroy', '__id__') }}".replace('__id__', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('{{ __("curriculum.messages.deleted") }}');
                                loadCurriculumData(); // Reload accordion
                            } else {
                                alert('{{ __("curriculum.messages.delete_failed") }}');
                            }
                        },
                        error: function(xhr) {
                            alert('{{ __("curriculum.messages.delete_failed") }}');
                        }
                    });
                }
            });

            // Restore button click
            $(document).on('click', '.restore-btn', function() {
                var id = $(this).data('id');
                if (confirm('{{ __("curriculum.messages.confirm_restore") }}')) {
                    $.ajax({
                        url: "{{ route('curriculum.restore', '__id__') }}".replace('__id__', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('{{ __("curriculum.messages.restored") }}');
                                loadCurriculumData(); // Reload accordion
                            } else {
                                alert('{{ __("curriculum.messages.restore_failed") }}');
                            }
                        },
                        error: function(xhr) {
                            alert('{{ __("curriculum.messages.restore_failed") }}');
                        }
                    });
                }
            });

            // Form submit
            $(document).on('submit', '#curriculumForm', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                var url = $('#curriculum_id').val() ? 
                    "{{ route('curriculum.update', '__id__') }}".replace('__id__', $('#curriculum_id').val()) : 
                    "{{ route('curriculum.store') }}";
                
                var method = 'POST';
                if ($('#curriculum_id').val()) {
                    formData.append('_method', 'PUT');
                }

                // Note: Hidden fields for language_id and level_id are automatically included in FormData

                // Add documents to remove
                if (removedDocuments.length > 0) {
                    formData.append('documents_to_remove', removedDocuments.join(','));
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert($('#curriculum_id').val() ? '{{ __("curriculum.messages.updated") }}' : '{{ __("curriculum.messages.created") }}');
                            $('#curriculumModal').modal('hide');
                            loadCurriculumData(); // Reload accordion
                        } else {
                            alert('{{ __("curriculum.messages.create_failed") }}');
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("curriculum.messages.create_failed") }}');
                    }
                });
            });

            // Document handling
            var curriculumDocuments = [];
            var removedDocuments = [];

            $(document).on('change', '#documents', function() {
                var files = this.files;
                var preview = $('#document_preview');
                preview.empty();
                
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var fileUrl = URL.createObjectURL(file);
                    
                    var fileItem = $('<div class="document-item d-flex align-items-center mb-2 p-2 border rounded">' +
                        '<i class="fas fa-file-pdf text-danger me-2"></i>' +
                        '<span class="flex-grow-1">' + file.name + '</span>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger remove-document" data-filename="' + file.name + '">' +
                        '<i class="fas fa-times"></i>' +
                        '</button>' +
                        '</div>');
                    
                    preview.append(fileItem);
                    curriculumDocuments.push({
                        name: file.name,
                        file: file,
                        isNew: true
                    });
                }
            });

            $(document).on('click', '.remove-document', function() {
                var filename = $(this).data('filename');
                $(this).closest('.document-item').remove();
                
                // Remove from documents array
                curriculumDocuments = curriculumDocuments.filter(function(doc) {
                    return doc.name !== filename;
                });
                
                // If it's an existing document, add to removed list
                if (!curriculumDocuments.find(doc => doc.name === filename && doc.isNew)) {
                    removedDocuments.push(filename);
                }
            });

            // Load existing documents
            function loadExistingDocuments(documents) {
                var preview = $('#document_preview');
                preview.empty();
                curriculumDocuments = [];
                
                documents.forEach(function(doc) {
                    var fileItem = $('<div class="document-item d-flex align-items-center mb-2 p-2 border rounded">' +
                        '<i class="fas fa-file-pdf text-danger me-2"></i>' +
                        '<span class="flex-grow-1">' + doc + '</span>' +
                        '<a href="{{ asset("storage/curriculum_documents/") }}/' + doc + '" target="_blank" class="btn btn-sm btn-outline-primary me-2">' +
                        '<i class="fas fa-eye"></i>' +
                        '</a>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger remove-document" data-filename="' + doc + '">' +
                        '<i class="fas fa-times"></i>' +
                        '</button>' +
                        '</div>');
                    
                    preview.append(fileItem);
                    curriculumDocuments.push({
                        name: doc,
                        isExisting: true
                    });
                });
            }

            // Load form data (languages, levels)
            function loadFormData() {
                // Load languages
                var languageSelect = $('#language_id');
                languageSelect.empty().append('<option value="">{{ __("curriculum.form.select_language") }}</option>');
                languages.forEach(function(language) {
                    languageSelect.append('<option value="' + language.id + '">' + language.name + '</option>');
                });

                // Load course levels
                var levelSelect = $('#level_id');
                levelSelect.empty().append('<option value="">{{ __("curriculum.form.select_level") }}</option>');
                levels.forEach(function(level) {
                    levelSelect.append('<option value="' + level.id + '">' + level.name + '</option>');
                });
            }
        });
    </script>
@endpush