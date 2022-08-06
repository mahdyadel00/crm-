@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">
    <!--form text tem-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.cron_job_command')) }}</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_company_name"
                name="settings_company_name" value="{{ config('system.cronjob_path') }}">
        </div>
    </div>
    @if(config('system.settings_cronjob_has_run') == 'yes')
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span>
        </button>
        <h4 class="text-info">{{ cleanLang(__('lang.cronjob_status')) }}</h4>
        {{ cleanLang(__('lang.cronjob_last_executed')) }}: ({{ runtimeDateAgo(config('system.settings_cronjob_last_run')) }})
    </div>
    @else

    
    <div id="fx-settings-cronjob-instructions">
        {{ cleanLang(__('lang.cronjob_instructions')) }}
    </div>

    
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span>
        </button>
        <h4 class="text-danger">{{ cleanLang(__('lang.cronjob_status')) }}</h4>
        {{ cleanLang(__('lang.cronjob_inactive')) }}
    </div>
    @endif
    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/cron-job-settings/"  target="_blank" class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
</form>
@endsection