@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">

    <!--keyid-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.live_api_key')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.mollie_general_info')) }}" data-placement="top"><i
                    class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_mollie_live_api_key"
                name="settings_mollie_live_api_key" value="{{ $settings->settings_mollie_live_api_key ?? '' }}">
        </div>
    </div>

    <!--keyid-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.test_api_key')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.mollie_general_info')) }}" data-placement="top"><i
                    class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_mollie_test_api_key"
                name="settings_mollie_test_api_key" value="{{ $settings->settings_mollie_test_api_key ?? '' }}">
        </div>
    </div>

    <!--currency-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.currency')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.payment_gateway_currency_code_example')) }}" data-placement="top"><i
                    class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_mollie_currency"
                name="settings_mollie_currency" value="{{ $settings->settings_mollie_currency ?? '' }}">
        </div>
    </div>

    <!--display name-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.display_name')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.display_name_info')) }}" data-placement="top"><i
                    class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_mollie_display_name"
                name="settings_mollie_display_name" value="{{ $settings->settings_mollie_display_name ?? '' }}">
        </div>
    </div>


    <!--sandbox mode-->
    <div class="form-group form-group-checkbox row">
        <label class="col-3 col-form-label" title="Foo">{{ cleanLang(__('lang.sandbox_mode')) }}
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.sandbox_mode_info')) }}" data-placement="top"><i
                    class="ti-info-alt"></i></span>
        </label>
        <div class="col-9 p-t-5">
            <input type="checkbox" id="settings_mollie_mode" name="settings_mollie_mode"
                class="filled-in chk-col-light-blue" {{ runtimePrechecked($settings->settings_mollie_mode) }}>
            <label for="settings_mollie_mode"></label>
        </div>
    </div>


    <!--Enabled-->
    <div class="form-group form-group-checkbox row">
        <label class="col-3 col-form-label" title="Foo">{{ cleanLang(__('lang.enable_payment_method')) }}</label>
        <div class="col-9 p-t-5">
            <input type="checkbox" id="settings_mollie_status" name="settings_mollie_status"
                class="filled-in chk-col-light-blue" {{ runtimePrechecked($settings->settings_mollie_status) }}>
            <label for="settings_mollie_status"></label>
        </div>
    </div>


    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request" data-url="/settings/mollie"
            data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection