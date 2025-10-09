<form id="groupForm" onsubmit="return submitGroupForm(this)">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">{{ __('groups.form.name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required 
                       placeholder="{{ __('groups.placeholders.name') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="code">{{ __('groups.form.code') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="code" name="code" required 
                       placeholder="{{ __('groups.placeholders.code') }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="course_id">{{ __('groups.form.course') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="course_id" name="course_id" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->full_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="teacher_id">{{ __('groups.form.teacher') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="teacher_id" name="teacher_id" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="start_date">{{ __('groups.form.start_date') }} <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="end_date">{{ __('groups.form.end_date') }} <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="classroom">{{ __('groups.form.classroom') }}</label>
                <input type="text" class="form-control" id="classroom" name="classroom" 
                       placeholder="{{ __('groups.placeholders.classroom') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="virtual_link">{{ __('groups.form.virtual_link') }}</label>
                <input type="url" class="form-control" id="virtual_link" name="virtual_link" 
                       placeholder="{{ __('groups.placeholders.virtual_link') }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="max_students">{{ __('groups.form.max_students') }}</label>
                <input type="number" class="form-control" id="max_students" name="max_students" 
                       min="1" max="50" value="20" placeholder="{{ __('groups.placeholders.max_students') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="can_cancel_classes" name="can_cancel_classes">
                    <label class="form-check-label" for="can_cancel_classes">
                        {{ __('groups.form.can_cancel_classes') }}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('groups.buttons.create') }}</button>
    </div>
</form>
