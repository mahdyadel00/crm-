@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form" id="settingsFormBank">


    <!--bank details-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.banking_details')) }}*</label>
        <div class="col-12">
            <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="settings_bank_details"
                id="settings_bank_details">
                {{ $settings->settings_bank_details }}
            </textarea>
        </div>
    </div>

    <!--show on invoices [UPCOMING]-->
    <div class="form-group form-group-checkbox row hidden">
        <label class="col-3 col-form-label">{{ cleanLang(__('lang.show_on_invoices')) }}</label>
        <div class="col-9 p-t-5">
            <input type="checkbox" id="settings_stripe_display_name" name="settings_bank_display_name"
                class="filled-in chk-col-light-blue" {{ runtimePrechecked($settings->settings_bank_display_name ?? '') }}>
            <label for="settings_bank_display_name"></label>
        </div>
    </div>

    <!--display name-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.display_name')) }}*
            <span class="align-middle text-themecontrast font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.display_name_info')) }}"
                data-placement="top"><i class="ti-info-alt"></i></span>
        </label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_bank_display_name"
                name="settings_bank_display_name" value="{{ $settings->settings_bank_display_name ?? '' }}">
        </div>
    </div>


    <!--Enabled-->
    <div class="form-group form-group-checkbox row">
        <label class="col-3 col-form-label">{{ cleanLang(__('lang.enable_payment_method')) }}</label>
        <div class="col-9 p-t-5">
            <input type="checkbox" id="settings_bank_status" name="settings_bank_status" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings->settings_bank_status) }}>
            <label for="settings_bank_status"></label>
        </div>
    </div>


    <div>
        <div><small>* {{ cleanLang(__('lang.required')) }} </small></div>
    </div>


    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left" data-url="/settings/bank"
            data-loading-target="" data-ajax-type="PUT" data-type="form" data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection