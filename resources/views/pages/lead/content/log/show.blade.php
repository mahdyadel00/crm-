<!--heading-->
<div class="x-heading p-t-10"><i class="mdi mdi-file-document-box"></i>{{ cleanLang(__('lang.lead_log')) }}</div>



<!--Form Data-->
<div class="card-show-form-data " id="card-lead-organisation">

@if(count($logs) > 0)




@else

<div class="x-no-result">
    <img src="{{ url('/') }}/public/images/no-download-avialble.png" alt="404 - Not found" /> 
    <div class="p-t-20"><h4>{{ cleanLang(__('lang.you_do_not_have_logs')) }}</h4></div>
    <div class="p-t-10">
        <a href="{{ url('app/settings/customfields/leads') }}" class="btn btn-info btn-sm">@lang('lang.record_new_log')</a>
    </div>
</div>

@endif

</div>