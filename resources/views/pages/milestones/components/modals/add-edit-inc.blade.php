<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.milestone_name')) }}*</label>
    <div class="col-12">
        <input type="text" class="form-control  form-control-sm" autocomplete="off" id="milestone_title"
            name="milestone_title" value="{{ $milestone->milestone_title ?? '' }}">
        <input type="hidden" name="milestone_projectid" value="{{ request('project_id') }}">
    </div>
</div>