@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">
    <!--form text tem-->
    <div class="form-group row">
        <label class="col-3 control-label col-form-label">{{ cleanLang(__('lang.project_title')) }}</label>
        <div class="col-9">
            <input type="text" class="form-control form-control-sm" id="settings_company_name"
                name="settings_company_name" value="{{ $settings->settings_company_name ?? '' }}">
        </div>
    </div>

    <!--form checkbox item-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.show_permission_project_creation')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_foo_bar" name="settings_foo_bar" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_foo_bar'] ?? '') }}>
            <label for="settings_foo_bar"></label>
        </div>
    </div>

    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton" class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="/settings/projects/general" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection