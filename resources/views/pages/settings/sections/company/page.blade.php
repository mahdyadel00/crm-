@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form" id="settingsFormCompany">

    <!--company-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.company_name')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_name"
                name="settings_company_name" value="{{ $settings->settings_company_name ?? '' }}">
        </div>
    </div>

    <!--address 1-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.address')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_address_line_1"
                name="settings_company_address_line_1" value="{{ $settings->settings_company_address_line_1 ?? '' }}">
        </div>
    </div>

    <!--city-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.city')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_city"
                name="settings_company_city" value="{{ $settings->settings_company_city ?? '' }}">
        </div>
    </div>

    <!--state-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.state')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_state"
                name="settings_company_state" value="{{ $settings->settings_company_state ?? '' }}">
        </div>
    </div>

    <!--form text tem-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.zipcode')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_zipcode"
                name="settings_company_zipcode" value="{{ $settings->settings_company_zipcode ?? '' }}">
        </div>
    </div>


    <!--form text tem-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.country')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_country"
                name="settings_company_country" value="{{ $settings->settings_company_country ?? '' }}">
        </div>
    </div>


    <!--form text tem-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.telephone')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_telephone"
                name="settings_company_telephone" value="{{ $settings->settings_company_telephone ?? '' }}">
        </div>
    </div>

    <div class="line m-t-8"></div>

    <h5 class="p-b-15">@lang('lang.additional_company_info')</h5>

    <!--customer field 1-->
    <div class="form-group row">
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_customfield_1"
                name="settings_company_customfield_1" value="{{ $settings->settings_company_customfield_1 ?? '' }}">
        </div>
    </div>

    <!--customer field 2-->
    <div class="form-group row">
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_customfield_2"
                name="settings_company_customfield_2" value="{{ $settings->settings_company_customfield_2 ?? '' }}">
        </div>
    </div>

    <!--customer field 3-->
    <div class="form-group row">
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_customfield_3"
                name="settings_company_customfield_3" value="{{ $settings->settings_company_customfield_3 ?? '' }}">
        </div>
    </div>

    <!--customer field 4-->
    <div class="form-group row">
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_customfield_4"
                name="settings_company_customfield_4" value="{{ $settings->settings_company_customfield_4 ?? '' }}">
        </div>
    </div>
    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/company-settings/" target="_blank"
            class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>

    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton" class="btn btn-rounded-x btn-danger waves-effect text-left"
            data-url="/settings/company" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection