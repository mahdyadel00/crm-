<!--importing results-->
<div class="importing-step-3" id="importing-step-3">
    <div class="x-splash-image"><img src="{{ url('public/images/import-results-partial.svg') }}"
            alt="importing completed" /></div>
    <div class="x-splash-text">
        <h3>@lang('lang.importing_completed')</h3>
    </div>
    <div class="x-splash-subtext p-b-15">
        <span class="label label-rounded label-success p-r-16 p-l-16"><strong>{{ $count_passed }}</strong>
            @lang('lang.records_imported')</span>
        <span class="label label-rounded label-danger p-r-16 p-l-16"><strong>{{ $error_count }}</strong>
            @lang('lang.records_failed')</span>
    </div>
    <!--see error log-->
    <div class="x-splash-failed-text">
        <a href="#" class="js-ajax-request" data-loading-target="commonModalBody"
            data-url="{{ url('import/errorlog?ref='.$error_ref) }}">@lang('lang.click_to_view_error_log')</a>
    </div>
</div>