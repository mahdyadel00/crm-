<div class="x-heading p-t-10"><i class="mdi mdi-cached"></i>@lang('lang.recurring_settings')</div>




<!--not recurring-->
@if($task->task_recurring =='no' && !request()->filled('recurring_action') )
<div class="x-no-result">
    <div class="alert alert-info m-t-40 m-b-40">@lang('lang.task_is_not_recurring')</div>
    <div class="text-center p-t-10">
        <button class="btn btn-info btn-sm ajax-request" data-loading-class="loading-before-centre"
            data-loading-target="card-tasks-left-panel"
            data-url="{{ urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=modal&recurring_action=edit') }}">@lang('lang.make_recurring')</a>
    </div>
</div>
@endif

<!--recurring [show]-->
@if($task->task_recurring =='yes' && !request()->filled('recurring_action'))
<div class="card-show-form-data" id="card-task-organisation">

    <!--repeat every-->
    <div class="form-data-row">
        <span class="x-data-title">@lang('lang.repeat_every') - </span>
        <span class="x-data-content text">
            {{ runtimeIntervalPlural($task->task_recurring_duration, $task->task_recurring_period) }}
        </span>
    </div>

    <!--cycles-->
    <div class="form-data-row">
        <span class="x-data-title">@lang('lang.cycles') - </span>
        <span class="x-data-content text">
            @if($task->task_recurring_cycles == 0)
            <span>@lang('lang.infinite')</span>
            @else
            <span>{{ $task->task_recurring_cycles }}</span>
            @endif
        </span>
    </div>

    <!--first task data-->
    <div class="form-data-row">
        <span class="x-data-title">@lang('lang.first_task_date') - </span>
        <span class="x-data-content text">{{ runtimeDate($task->task_recurring_next) }}</span>
    </div>

    <!--copy checklists-->
    <div class="form-data-row">
        <span class="x-data-title">@lang('lang.copy_checklists') - </span>
        <span class="x-data-content text">{{ runtimeLang($task->task_recurring_copy_checklists) }}</span>
    </div>

    <!--copy files-->
    <div class="form-data-row">
        <span class="x-data-title">@lang('lang.copy_files') - </span>
        <span class="x-data-content text">{{ runtimeLang($task->task_recurring_copy_files) }}</span>
    </div>

    <!--automatically assign-->
    <div class="form-data-row">
        <span class="x-data-title">@lang('lang.automatically_assign') - </span>
        <span class="x-data-content text">{{ runtimeLang($task->task_recurring_automatically_assign) }}</span>
    </div>

    <!--edit button-->
    <div class="form-data-row-buttons">
        <button type="button" class="btn waves-effect waves-light btn-xs btn-info ajax-request"
            data-url="{{ urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=modal&recurring_action=edit') }}"
            data-loading-class="loading-before-centre"
            data-loading-target="card-tasks-left-panel">@lang('lang.edit')</button>
    </div>
</div>
@endif

<!--recurring [edit]-->
@if(request('recurring_action') == 'edit')
<div class="p-t-40" id="task-modal-recurring-form">
    @include('pages.tasks.components.modals.recurring-settings')
    <div class="text-right p-t-10">
        <!--stop-->
        <button class="btn btn-default btn-xs ajax-request" data-loading-class="loading-before-centre"
            data-loading-target="card-tasks-left-panel"
            data-url="{{ urlResource('/tasks/'.$task->task_id.'/stop-recurring?source=modal') }}">@lang('lang.stop_recurring')</button>
        <!--save changes-->
        <button class="btn btn-danger btn-xs ajax-request" data-type="form" data-form-id="task-modal-recurring-form"
            data-ajax-type="post" data-loading-class="loading-before-centre" data-loading-target="card-tasks-left-panel"
            data-url="{{ urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=modal') }}">@lang('lang.save_changes')</button>
    </div>
</div>
@endif