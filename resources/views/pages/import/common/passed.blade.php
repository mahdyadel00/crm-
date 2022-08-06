<!--importing results-->
<div class="importing-step-3" id="importing-step-3">
    <div class="x-splash-image"><img src="{{ url('public/images/import-results-passed.svg') }}"
            alt="importing completed" /></div>
    <div class="x-splash-text">
        <h3>@lang('lang.importing_completed')</h3>
    </div>
    <div class="x-splash-subtext">
        <span class="label label-rounded label-success p-r-16 p-l-16"><strong>{{ $count_passed }}</strong> @lang('lang.records_imported')</span>
    </div>
</div>