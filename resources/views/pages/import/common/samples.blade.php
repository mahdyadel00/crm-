<div class="x-splash-failed-text">
    @lang('lang.you_can_download_sample_files')
</div>

<!--leads samples-->
@if($type == 'leads')
<div class="x-splash-failed-text">
    <a href="{{ url('storage/system/samples/import-leads.csv') }}" download>@lang('lang.csv_sample')</a>
    | <a href="{{ url('storage/system/samples/import-leads.xlsx') }}" download>@lang('lang.xlsx_sample')</a>
</div>
@endif

<!--tasks samples-->
@if($type == 'tasks')
<div class="x-splash-failed-text">
    <a href="{{ url('storage/system/samples/import-tasks.csv') }}" download>@lang('lang.csv_sample')</a>
    | <a href="{{ url('storage/system/samples/import-tasks.xlsx') }}" download>@lang('lang.xlsx_sample')</a>
</div>
@endif

<!--projects samples-->
@if($type == 'projects')
<div class="x-splash-failed-text">
    <a href="{{ url('storage/system/samples/import-projects.csv') }}" download>@lang('lang.csv_sample')</a>
    | <a href="{{ url('storage/system/samples/import-projects.xlsx') }}" download>@lang('lang.xlsx_sample')</a>
</div>
@endif

<!--clients samples-->
@if($type == 'clients')
<div class="x-splash-failed-text">
    <a href="{{ url('storage/system/samples/import-clients.csv') }}" download>@lang('lang.csv_sample')</a>
    | <a href="{{ url('storage/system/samples/import-clients.xlsx') }}" download>@lang('lang.xlsx_sample')</a>
</div>
@endif