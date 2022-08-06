@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form>
    <!--permission - view tasks-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label text-left">{{ cleanLang(__('lang.view_tasks')) }}</label>
        <div class="col-8 text-left p-t-5">
            <input type="checkbox" id="settings_projects_clientperm_tasks_view"
                name="settings_projects_clientperm_tasks_view" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_projects_clientperm_tasks_view'] ?? '') }}>
            <label for="settings_projects_clientperm_tasks_view"></label>
        </div>
    </div>
    <!--permission - task participation-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label text-left required">{{ cleanLang(__('lang.task_participation')) }}**</label>
        <div class="col-8 text-left p-t-5">
            <input type="checkbox" id="settings_projects_clientperm_tasks_collaborate"
                name="settings_projects_clientperm_tasks_collaborate" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_projects_clientperm_tasks_collaborate'] ?? '') }}>
            <label for="settings_projects_clientperm_tasks_collaborate"></label>
        </div>
    </div>
    <!--permission - create tasks-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label text-left required">{{ cleanLang(__('lang.create_tasks')) }}**</label>
        <div class="col-8 text-left p-t-5">
            <input type="checkbox" id="settings_projects_clientperm_tasks_create"
                name="settings_projects_clientperm_tasks_create" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_projects_clientperm_tasks_create'] ?? '') }}>
            <label for="settings_projects_clientperm_tasks_create"></label>
        </div>
    </div>
    <div class="line"></div>
    <!--permission - view timesheets-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label text-left">{{ cleanLang(__('lang.view_time_sheets')) }}</label>
        <div class="col-8 text-left p-t-5">
            <input type="checkbox" id="settings_projects_clientperm_timesheets_view"
                name="settings_projects_clientperm_timesheets_view" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_projects_clientperm_timesheets_view'] ?? '') }}>
            <label for="settings_projects_clientperm_timesheets_view"></label>
        </div>
    </div>
    <div class="line"></div>
    <!--permission - view expenses-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label text-left">{{ cleanLang(__('lang.view_expenses')) }}</label>
        <div class="col-8 text-left p-t-5">
            <input type="checkbox" id="settings_projects_clientperm_expenses_view"
                name="settings_projects_clientperm_expenses_view" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_projects_clientperm_expenses_view'] ?? '') }}>
            <label for="settings_projects_clientperm_expenses_view"></label>
        </div>
    </div>
    <div><small>** {{ cleanLang(__('lang.viewing_permissions_info')) }}</small></div>

            <div>
                <!--settings documentation help-->
                <a href="https://growcrm.io/documentation/project-settings/"  target="_blank" class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
            </div>

    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton" class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="/settings/projects/client" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>

</form>

<!--/#dynamic ajax section-->
<!--section js resource-->
@endsection