<div class="form-group row">
    <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.status')) }}</label>
    <div class="col-sm-12">
        <select class="select2-basic form-control form-control-sm" id="project_status"
            name="project_status">
            <option value="not_started" {{ runtimePreselected('not_started', $project->project_status) }}>{{ cleanLang(__('lang.not_started')) }}</option>
            <option value="in_progress" {{ runtimePreselected('in_progress', $project->project_status) }}>{{ cleanLang(__('lang.in_progress')) }}</option>
            <option value="on_hold" {{ runtimePreselected('on_hold', $project->project_status) }}>{{ cleanLang(__('lang.on_hold')) }}</option>
            <option value="cancelled" {{ runtimePreselected('cancelled', $project->project_status) }}>{{ cleanLang(__('lang.cancelled')) }}</option>
            <option value="completed" {{ runtimePreselected('completed', $project->project_status) }}>{{ cleanLang(__('lang.completed')) }}</option>
        </select>
    </div>
</div>