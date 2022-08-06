@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form" id="settingsFormPaypal">


    <!--email address-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.email_address')) }}*</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_paypal_email"
                name="settings_paypal_email" value="{{ $settings->settings_paypal_email ?? '' }}">
        </div>
    </div>

    <!--currency-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.currency')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.payment_gateway_currency_code_example')) }}" data-placement="top"
                ><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_paypal_currency"
                name="settings_paypal_currency" value="{{ $settings->settings_paypal_currency ?? '' }}">
        </div>
    </div>

    <!--display name-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.display_name')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.display_name_info')) }}"
                data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_paypal_display_name"
                name="settings_paypal_display_name" value="{{ $settings->settings_paypal_display_name ?? '' }}">
        </div>
    </div>


    <!--ipn url-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.paypal_ipn_url')) }}* 
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.paypal_api_instructions')) }}"
            data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_stripe_ipn_url"
                name="settings_stripe_ipn_url" value="{{ url('/api/paypal/ipn') }}" disabled>
        </div>
    </div>

    <div class="line"></div>

    <!--sandbox mode-->
    <div class="form-group form-group-checkbox row">
        <label class="col-3 col-form-label" title="Foo">{{ cleanLang(__('lang.sandbox_mode')) }} 
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.sandbox_mode_info')) }}"
            data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-9 p-t-5">
            <input type="checkbox" id="settings_paypal_mode" name="settings_paypal_mode"
                class="filled-in chk-col-light-blue" {{ runtimePrechecked($settings->settings_paypal_mode) }}>
            <label for="settings_paypal_mode"></label>
        </div>
    </div>

    <!--Enabled-->
    <div class="form-group form-group-checkbox row">
        <label class="col-3 col-form-label">{{ cleanLang(__('lang.enable_payment_method')) }}</label>
        <div class="col-9 p-t-5">
            <input type="checkbox" id="settings_paypal_status" name="settings_paypal_status" class="filled-in chk-col-light-blue"
            {{ runtimePrechecked($settings->settings_paypal_status) }}>
            <label for="settings_paypal_status"></label>
        </div>
    </div>


    <div>
        <div class="p-b-10">
            <stong><small>* {{ cleanLang(__('lang.required')) }} </small></stong>
        </div>
    </div>

    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/payment-gateway-paypal/"  target="_blank" class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>

    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left" data-url="/settings/paypal"
            data-loading-target="" data-ajax-type="PUT" data-type="form" data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection