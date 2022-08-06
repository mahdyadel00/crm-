@extends('pages.settings.ajaxwrapper')
@section('settings-page')

<!--error logs-->
@if (@count($logs) > 0)
<div class="row">
    @foreach($logs as $key => $log)
    <div class="col-sm-12 col-md-4 col-lg-3" id="logfile_{{ $key }}">
        <div class="error-log">
            <h6>{{ $log->getFilename() }}</h6>
            <a type="button" href="{{ url('settings/errorlogs/download?filename='.$log->getFilename()) }}"
                class="btn btn-primary btn-xs" download><i class="fa fa-check"></i>
                @lang('lang.download') - ({{ humanFileSize($log->getSize()) }})
            </a>
            <input type="hidden" name="filename" value="{{ $log->getFilename() }}">
            <button type="button" class="btn btn-danger btn-sm btn-xs confirm-action-danger"
                data-confirm-title="@lang('lang.delete_item')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('settings/errorlogs/delete?key='.$key.'&filename='.$log->getFilename()) }}">
                @lang('lang.delete')
            </button>
        </div>
</div>
@endforeach
</div>

<div class="alert alert-info m-t-30">
    <h5 class="text-info"><i class="sl-icon-info"></i> @lang('lang.info')</h5>@lang('lang.you_can_delete_these_files')
</div>
@endif
@if (@count($logs) == 0)
<!--nothing found-->
@include('notifications.no-results-found')
<!--nothing found-->
@endif

<!--section js resource-->
@endsection