@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form>


    <!--show project categories in main menu-->
    <div class="form-group row">
        <label class="col-4 control-label col-form-label">@lang('lang.projects_user_permission')</label>
        <div class="col-3">
            <select class="select2-basic form-control form-control-sm select2-preselected"
                id="settings_projects_permissions_basis" name="settings_projects_permissions_basis"
                data-preselected="{{ $settings->settings_projects_permissions_basis ?? ''}}">
                <option value="user_roles">@lang('lang.role_based')</option>
                <option value="category_based">@lang('lang.category_based')</option>
            </select>
        </div>
    </div>

    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label text-left">{{ cleanLang(__('lang.tasks_collaboration')) }}</label>
        <div class="col-8 text-left p-t-5">
            <input type="checkbox" id="settings_projects_assignedperm_tasks_collaborate"
                name="settings_projects_assignedperm_tasks_collaborate" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_projects_assignedperm_tasks_collaborate'] ?? '') }}>
            <label for="settings_projects_assignedperm_tasks_collaborate"></label>
        </div>
    </div>
<div class="alert alert-warning"><h5 class="text-warning"><i class="sl-icon-info"></i> @lang('lang.warning')</h5>@lang('lang.changing_project_permissions_warning')</div>


    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="/settings/projects/staff" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection