@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form" id="settingsFormEmailGeneral">

    <!--from email address-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.system_email_address')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_email_from_address"
                name="settings_email_from_address" value="{{ $settings->settings_email_from_address ?? '' }}">
        </div>
    </div>

    <!--from name-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.system_from_name')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_email_from_name"
                name="settings_email_from_name" value="{{ $settings->settings_email_from_name ?? '' }}">
        </div>
    </div>

    <!--server type-->
    <div class="form-group row">
        <label for="example-month-input"
            class="col-12 col-form-label text-left">{{ cleanLang(__('lang.email_delivery')) }}</label>
        <div class="col-12">
            <select class="select2-basic form-control form-control-sm" id="settings_email_server_type"
                name="settings_email_server_type">
                <option value="sendmail"
                    {{ runtimePreselected('sendmail', $settings->settings_email_server_type ?? '') }}>
                    Sendmail</option>
                <option value="smtp" {{ runtimePreselected('smtp', $settings->settings_email_server_type ?? '') }}>
                    SMTP</option>
            </select>
        </div>
    </div>

    <!--show error if cronjob has not run before--><!--[MULTITENANCY]-->
    @if(config('system.settings_type') == 'standalone')
    @if($settings->settings_cronjob_has_run != 'yes')
    <div class="splash-text">
        <div class="alert alert-danger">{{ cleanLang(__('lang.cronjob_and_emails')) }}. <a
                href="https://growcrm.io/documentation/cron-job-settings/"
                target="_blank">@lang('lang.more_information')</a></div>
    </div>
    @endif
    @endif

    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/email-settings/" target="_blank"
            class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>

    <!--buttons-->
    <div class="text-right">
        <!--send a test email-->
        <button type="button" class="btn btn-info edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
            data-toggle="modal" data-target="#commonModal" data-url="{{ url('settings/email/testemail') }}"
            data-loading-target="commonModalBody" data-modal-title="Send A Test Email"
            data-action-url="{{ url('settings/email/testemail') }}" data-action-method="POST" data-action-ajax-class=""
            data-action-type='form' data-action-form-id="test-email-form""
            data-modal-size=" modal-lg" data-header-close-icon="hidden" data-header-extra-close-icon="visible"
            data-action-ajax-loading-target="commonModalBody">{{ cleanLang(__('lang.send_test_email')) }}
        </button>
        <!--save changes-->
        <button type="submit" id="commonModalSubmitButton" class="btn btn-rounded-x btn-danger waves-effect text-left"
            data-url="/settings/email/general" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>

    </div>
</form>
@endsection