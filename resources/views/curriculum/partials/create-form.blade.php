<form id="curriculumForm" onsubmit="return submitCurriculumForm(this)">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="language_id">{{ __('curriculum.form.language') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="language_id" name="language_id" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    @foreach($languages as $language)
                        <option value="{{ $language->id }}">{{ $language->display_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="level_id">{{ __('curriculum.form.level') }} <span class="text-danger">*</span></label>
                <select class="form-control" id="level_id" name="level_id" required>
                    <option value="">{{ __('common.select_option') }}</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="title">{{ __('curriculum.form.title') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required 
                       placeholder="{{ __('curriculum.placeholders.title') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="order_index">{{ __('curriculum.form.order_index') }}</label>
                <input type="number" class="form-control" id="order_index" name="order_index" 
                       min="0" value="0" placeholder="{{ __('curriculum.placeholders.order_index') }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="description">{{ __('curriculum.form.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="{{ __('curriculum.placeholders.description') }}"></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('curriculum.buttons.create') }}</button>
    </div>
</form>
