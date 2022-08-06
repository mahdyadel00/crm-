<div class="card">
    <div class="card-body">
        <h4 class="card-title">@lang('lang.error_log')</h4>
        <h6 class="card-subtitle">@lang('lang.the_following_records_could_not_be_imported')</h6>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang('lang.row')</th>
                        <th>@lang('lang.column_name')</th>
                        <th>@lang('lang.error_message')</th>
                    </tr>
                </thead>
                <tbody>
                    {!! $log->log_payload !!}
                </tbody>
            </table>
        </div>
    </div>
</div>