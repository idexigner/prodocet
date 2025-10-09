<form id="courseForm" onsubmit="return submitCourseForm(this)" action="{{ route('courses.update', $course->id) }}">
    @csrf
    <input type="hidden" name="_method" value="POST">
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">{{ __('courses.form.name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required 
                       value="{{ $course->name }}" placeholder="{{ __('courses.placeholders.name') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="code">{{ __('courses.form.code') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="code" name="code" required 
                       value="{{ $course->code }}" placeholder="{{ __('courses.placeholders.code') }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="language_id">{{ __('courses.form.language') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="language_id" name="language_id" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    @foreach($languages as $language)
                        <option value="{{ $language->id }}" {{ $course->language_id == $language->id ? 'selected' : '' }}>
                            {{ $language->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="level_id">{{ __('courses.form.level') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="level_id" name="level_id" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ $course->level_id == $level->id ? 'selected' : '' }}>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="rate_scheme_id">{{ __('courses.form.rate_scheme') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="rate_scheme_id" name="rate_scheme_id" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    @foreach($rateSchemes as $scheme)
                        <option value="{{ $scheme->id }}" {{ $course->rate_scheme_id == $scheme->id ? 'selected' : '' }}>
                            {{ $scheme->letter_code }} - {{ $scheme->formatted_rate }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="teaching_hours">{{ __('courses.form.teaching_hours') }} <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="teaching_hours" name="teaching_hours" 
                       min="1" required value="{{ $course->teaching_hours }}" 
                       placeholder="{{ __('courses.placeholders.teaching_hours') }}">
                <small class="form-text text-muted">{{ __('courses.info.hc_explanation') }}</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="mode">{{ __('courses.form.mode') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="mode" name="mode" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    <option value="in_person" {{ $course->mode == 'in_person' ? 'selected' : '' }}>{{ __('courses.modes.in_person') }}</option>
                    <option value="virtual" {{ $course->mode == 'virtual' ? 'selected' : '' }}>{{ __('courses.modes.virtual') }}</option>
                    <option value="hybrid" {{ $course->mode == 'hybrid' ? 'selected' : '' }}>{{ __('courses.modes.hybrid') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="max_students_per_group">{{ __('courses.form.max_students_per_group') }}</label>
                <input type="number" class="form-control" id="max_students_per_group" name="max_students_per_group" 
                       min="1" max="50" value="{{ $course->max_students_per_group }}" 
                       placeholder="{{ __('courses.placeholders.max_students_per_group') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="is_curriculum_required" name="is_curriculum_required" 
                           {{ $course->is_curriculum_required ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_curriculum_required">
                        {{ __('courses.form.is_curriculum_required') }}
                    </label>
                </div>
                <small class="form-text text-muted">{{ __('courses.info.curriculum_optional') }}</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="description">{{ __('courses.form.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="{{ __('courses.placeholders.description') }}">{{ $course->description }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('courses.buttons.update') }}</button>
    </div>
</form>
