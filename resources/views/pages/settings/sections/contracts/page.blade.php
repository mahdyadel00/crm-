@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">
    <!--form text tem-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.contract_prefix')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_contracts_prefix"
                name="settings_contracts_prefix" value="{{ $settings->settings_contracts_prefix ?? '' }}">
        </div>
    </div>

    <!--next id-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">@lang('lang.next_id_number_contract')
            (@lang('lang.optional'))
            <!--info tooltip-->
            <span class="align-middle text-themecontrast" data-toggle="tooltip"
                title="@lang('lang.next_id_number_info')" data-placement="top"><i
                    class="ti-info-alt"></i></span></label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="next_id" name="next_id" value="{{ $next_id }}">
            <input type="hidden" name="next_id_current" value="{{ $next_id }}">
        </div>
    </div>

    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/contract-settings/"  target="_blank" class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
    
    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton" class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="/settings/contracts" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection