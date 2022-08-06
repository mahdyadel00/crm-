@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form" id="settingsFormStripe">


    <!--live - publid key-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.publishable_key')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.stripe_general_info')) }}"
            data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_stripe_public_key"
                name="settings_stripe_public_key" value="{{ $settings->settings_stripe_public_key ?? '' }}">
        </div>
    </div>

    <!--live - secret key-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.secret_key')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.stripe_general_info')) }}"
            data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_stripe_secret_key"
                name="settings_stripe_secret_key" value="{{ $settings->settings_stripe_secret_key ?? '' }}">
        </div>
    </div>

    <!--webhooks secret key-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.webhooks_signing_key')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.stripe_general_info')) }}"
                data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_stripe_webhooks_key"
                name="settings_stripe_webhooks_key" value="{{ $settings->settings_stripe_webhooks_key ?? '' }}">
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
            <input type="text" class="form-control form-control-sm" id="settings_stripe_currency"
                name="settings_stripe_currency" value="{{ $settings->settings_stripe_currency ?? '' }}">
        </div>
    </div>


    <!--display name-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.display_name')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.display_name_info')) }}"
                data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_stripe_display_name"
                name="settings_stripe_display_name" value="{{ $settings->settings_stripe_display_name ?? '' }}">
        </div>
    </div>


    <!--webhooks url-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.webhooks_url')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" 
            title="{{ cleanLang(__('lang.add_this_inside_your_dashboard')) }} (Stripe)"
            data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_stripe_ipn_url"
                name="settings_stripe_ipn_url" value="{{ url('/api/stripe/webhooks') }}" disabled>
        </div>
    </div>

    <div class="line"></div>

    <!--Enabled-->
    <div class="form-group form-group-checkbox row">
        <label class="col-3 col-form-label" title="Foo">{{ cleanLang(__('lang.enable_payment_method')) }}</label>
        <div class="col-9 p-t-5">
            <input type="checkbox" id="settings_stripe_status" name="settings_stripe_status"
                class="filled-in chk-col-light-blue" {{ runtimePrechecked($settings->settings_stripe_status) }}>
            <label for="settings_stripe_status"></label>
        </div>
    </div>

    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left" data-url="/settings/stripe"
            data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection