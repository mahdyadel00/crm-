@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">
    <!--form checkbox item-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.mark_estimates_as_billable_by_default')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_expenses_billable_by_default" name="settings_expenses_billable_by_default" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_expenses_billable_by_default'] ?? '') }}>
            <label for="settings_expenses_billable_by_default"></label>
        </div>
    </div>

    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/expensesettings/"  target="_blank" class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
    
    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton" class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="/settings/expenses" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection