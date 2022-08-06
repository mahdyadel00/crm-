<ul class="nav nav-tabs" role="tablist">
        <!--home-->
        <li class="nav-item"> <a class="nav-link active ajax-request" data-toggle="tab" href="javascript:void(0);"
                        role="tab" data-url="{{ url('tasks/content/'.$task->task_id.'/show-main?show=tab') }}"
                        data-loading-class="loading-before-centre" data-loading-target="card-tasks-left-panel"><span
                                class="hidden-sm-up"><i class="ti-home"></i></span> <span
                                class="hidden-xs-down">@lang('lang.task')</span></a> </li>

        <!--customfields-->
        <li class="nav-item"> <a class="nav-link ajax-request" data-toggle="tab" href="javascript:void(0);" role="tab"
                        data-url="{{ url('tasks/content/'.$task->task_id.'/show-customfields') }}"
                        data-loading-class="loading-before-centre" data-loading-target="card-tasks-left-panel">
                        <span class="hidden-sm-up"><i class="ti-menu"></i></span>
                        <span class="hidden-xs-down">@lang('lang.information')</span></a>
        </li>


        <!--my notes (do not show for templates)-->
        @if($task->project_type == 'project')
        <li class="nav-item"> <a class="nav-link ajax-request" data-toggle="tab" href="javascript:void(0);" role="tab"
                        data-url="{{ url('tasks/content/'.$task->task_id.'/show-mynotes') }}"
                        data-loading-class="loading-before-centre" data-loading-target="card-tasks-left-panel">
                        <span class="hidden-sm-up"><i class="ti-notepad"></i></span>
                        <span class="hidden-xs-down">@lang('lang.my_notes')</span></a>
        </li>
        @endif



        <!--recurring settings-->
        @if($task->project_type == 'project' && auth()->user()->is_team)
        <li class="nav-item"> <a class="nav-link ajax-request" data-toggle="tab" href="javascript:void(0);" role="tab"
                        data-url="{{ url('tasks/'.$task->task_id.'/recurring-settings?source=modal') }}"
                        data-loading-class="loading-before-centre" data-loading-target="card-tasks-left-panel">
                        <span class="hidden-sm-up"><i class="sl-icon-refresh"></i></span>
                        <span class="hidden-xs-down">@lang('lang.recurring')</span>
                        <!--recurring-->
                        @if(auth()->user()->is_team)
                        <span class="sl-icon-refresh font-13 vm text-danger p-l-5 {{ runtimeTaskRecurringIcon($task->task_recurring) }}" id="task-modal-menu-recurring-icon"
                                data-toggle="tooltip"
                                title="@lang('lang.recurring_task')"></span>
                        @endif </a>
        </li>
        @endif
</ul>