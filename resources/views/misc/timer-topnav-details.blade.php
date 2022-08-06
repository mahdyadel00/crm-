<div class="x-heading">@lang('lang.active_timer')</div>
<div class="x-task">
    <a
        href="{{ urlResource('/tasks/v/'.request('users_running_timer_task_id').'/'.str_slug(request('users_running_timer_task_title'))) }}">
        {{ str_limit(request('users_running_timer_title'), 100) }}</a>
    <!--polling trigger-->
    @if(Auth::user() && auth()->user()->is_team && env('APP_DEBUG_TOOLBAR') === false)
    <span class="hidden" id="js-trigger-topnav-timer" data-progress-bar='hidden'
        data-notifications="disabled" data-skip-checkboxes-reset="TRUE"
        data-url="{{ url('/polling/timer') }}"></span>
    @endif
</div>
<div class="x-button">
    <!--stoptimer-->
    <button type="button" id="my-timer-time-topnav-stop-button"
        class="btn waves-effect waves-light btn-sm btn-danger js-timer-button js-ajax-request"
        data-url="{{ url('tasks/timer/'.request('users_running_timer_task_id').'/stop?source=topnav') }}"
        data-form-id="tasks-list-table"
        data-progress-bar='hidden'>@lang('lang.stop_timer')</button>
</div>