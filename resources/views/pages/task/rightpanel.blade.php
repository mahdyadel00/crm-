    <!----------Assigned----------->
    @if(config('visibility.tasks_card_assigned'))
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.assigned_users')) }}</h6>
        </div>
        <span id="task-assigned-container" class="">
            @include('pages.task.components.assigned')
        </span>
        <!--user-->
        @if($task->permission_assign_users)
        <span class="x-assigned-user x-assign-new js-card-settings-button-static card-task-assigned text-info"
            tabindex="0" data-popover-content="card-task-team" data-title="{{ cleanLang(__('lang.assign_users')) }}"><i
                class="mdi mdi-plus"></i></span>
        @endif
    </div>
    @else
    <!--spacer-->
    <div class="p-b-40"></div>
    @endif


    <!--show timer-->
    <div id="task-timer-container">
        @include('pages.task.components.timer')
    </div>


    <!----------settings----------->
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.settings')) }}</h6>
        </div>
        <!--start date-->
        @if(config('visibility.tasks_standard_features'))
        <div class="x-element" id="task-start-date"><i class="mdi mdi-calendar-plus"></i>
            <span>{{ cleanLang(__('lang.start_date')) }}:</span>
            @if($task->permission_edit_task)
            <span class="x-highlight x-editable card-pickadate"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-start-date/') }}" data-type="form"
                data-progress-bar='hidden' data-form-id="task-start-date" data-hidden-field="task_date_start"
                data-container="task-start-date-container" data-ajax-type="post"
                id="task-start-date-container">{{ runtimeDate($task->task_date_start) }}</span></span>
            <input type="hidden" name="task_date_start" id="task_date_start">
            @else
            <span class="x-highlight">{{ runtimeDate($task->task_date_start) }}</span>
            @endif
        </div>
        @endif
        <!--due date-->
        @if(config('visibility.tasks_standard_features'))
        <div class="x-element" id="task-due-date"><i class="mdi mdi-calendar-clock"></i>
            <span>{{ cleanLang(__('lang.due_date')) }}:</span>
            @if($task->permission_edit_task)
            <span class="x-highlight x-editable card-pickadate"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-due-date/') }}" data-type="form"
                data-progress-bar='hidden' data-form-id="task-due-date" data-hidden-field="task_date_due"
                data-container="task-due-date-container" data-ajax-type="post"
                id="task-due-date-container">{{ runtimeDate($task->task_date_due) }}</span></span>
            <input type="hidden" name="task_date_due" id="task_date_due">
            @else
            <span class="x-highlight">{{ runtimeDate($task->task_date_due) }}</span>
            @endif
        </div>
        @endif
        <!--status-->
        <div class="x-element" id="card-task-status"><i class="mdi mdi-flag"></i>
            <span>{{ cleanLang(__('lang.status')) }}: </span>
            @if($task->permission_participate)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-task-status-text" tabindex="0"
                data-popover-content="card-task-statuses" data-offset="0 25%"
                data-status-id="{{ $task->taskstatus_id }}"
                data-title="{{ cleanLang(__('lang.status')) }}">{{ runtimeLang($task->taskstatus_title) }}</strong></span>
            @else
            <span class="x-highlight">{{ runtimeLang($task->taskstatus_title) }}</span>
            @endif
        </div>

        <!--priority-->
        <div class="x-element" id="card-task-priority"><i class="mdi mdi-verified"></i>
            <span>{{ cleanLang(__('lang.priority')) }}:
            </span>
            @if($task->permission_participate)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-task-priority-text"
                tabindex="0" data-popover-content="card-task-priorities"
                data-title="{{ cleanLang(__('lang.priority')) }}">{{ runtimeLang($task->task_priority) }}</strong></span>
            <input type="hidden" name="task_priority" id="task_priority">
            @else
            <span class="x-highlight">{{ runtimeLang($task->task_priority) }}</span>
            @endif
        </div>

        <!--client visibility-->
        @if(auth()->user()->type =='team')
        <div class="x-element" id="card-task-client-visibility"><i class="mdi mdi-eye"></i>
            <span>{{ cleanLang(__('lang.client')) }}:</span>
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-task-client-visibility-text"
                tabindex="0" data-popover-content="card-task-visibility"
                data-title="{{ cleanLang(__('lang.client_visibility')) }}">{{ runtimeDBlang($task->task_client_visibility, 'task_client_visibility') }}</strong></span>
            <input type="hidden" name="task_client_visibility" id="task_client_visibility">
        </div>
        @endif

        <!--reminder-->
        @if(config('visibility.modules.reminders') && $task->project_type == 'project')
        <div class="card-reminders-container" id="card-reminders-container">
            @include('pages.reminders.cards.wrapper')
        </div>
        @endif


    </div>

    <!----------tags----------->
    <div class="card-tags-container" id="card-tags-container">
        @include('pages.task.components.tags')
    </div>

    <!----------actions----------->
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.actions')) }}</h6>
        </div>

        <!--track if we have any actions-->
        @php $count_action = 0 ; @endphp

        <!--change milestone-->
        @if($task->permission_edit_task && auth()->user()->type =='team')
        <div class="x-element x-action js-card-settings-button-static" id="card-task-milestone" tabindex="0"
            data-popover-content="card-task-milestones" data-title="{{ cleanLang(__('lang.milestone')) }}"><i
                class="mdi mdi-redo-variant"></i>
            <span class="x-highlight">{{ cleanLang(__('lang.change_milestone')) }}</strong></span>
        </div>
        @php $count_action ++ ; @endphp
        @endif

        <!--stop all timer-->
        @if($task->permission_super_user && config('visibility.tasks_standard_features'))
        <div class="x-element x-action confirm-action-danger"
            data-confirm-title="{{ cleanLang(__('lang.stop_all_timers')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
            data-url="{{ url('/') }}/tasks/timer/{{ $task->task_id }}/stopall?source=card"><i
                class="mdi mdi-timer-off"></i>
            <span class="x-highlight" id="task-start-date">{{ cleanLang(__('lang.stop_all_timers')) }}</span></span>
        </div>
        @php $count_action ++ ; @endphp
        @endif


        <!--archive-->
        @if($task->permission_edit_task && config('visibility.tasks_standard_features'))
        <div class="x-element x-action confirm-action-info  {{ runtimeActivateOrAchive('archive-button', $task->task_active_state) }} card_archive_button_{{ $task->task_id }}"
            id="card_archive_button_{{ $task->task_id }}" data-confirm-title="{{ cleanLang(__('lang.archive_task')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
            data-url="{{ url('/') }}/tasks/{{ $task->task_id }}/archive"><i class="mdi mdi-archive"></i> <span
                class="x-highlight" id="task-start-date">{{ cleanLang(__('lang.archive')) }}</span></span></div>
        @php $count_action ++ ; @endphp
        @endif

        <!--restore-->
        @if($task->permission_edit_task && runtimeArchivingOptions())
        <div class="x-element x-action confirm-action-info  {{ runtimeActivateOrAchive('activate-button', $task->task_active_state) }} card_restore_button_{{ $task->task_id }}"
            id="card_restore_button_{{ $task->task_id }}" data-confirm-title="{{ cleanLang(__('lang.restore_task')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
            data-url="{{ url('/') }}/tasks/{{ $task->task_id }}/activate"><i class="mdi mdi-archive"></i> <span
                class="x-highlight" id="task-start-date">{{ cleanLang(__('lang.restore')) }}</span></span></div>
        @php $count_action ++ ; @endphp
        @endif

        <!--delete-->
        @if($task->permission_delete_task && runtimeArchivingOptions())
        <div class="x-element x-action confirm-action-danger"
            data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
            data-url="{{ urlResource('/') }}/tasks/{{ $task->task_id }}"><i class="mdi mdi-delete"></i> <span
                class="x-highlight" id="task-start-date">{{ cleanLang(__('lang.delete')) }}</span></span></div>
        @php $count_action ++ ; @endphp
        @endif


        <!--no action available-->
        @if($count_action == 0)
        <div class="x-element">
            {{ cleanLang(__('lang.no_actions_available')) }}
        </div>
        @endif

    </div>

    <!----------meta infor----------->
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.information')) }}</h6>
        </div>
        <div class="x-element x-action">
            <table class="table table-bordered table-sm">
                <tbody>
                    <tr>
                        <td>{{ cleanLang(__('lang.task_id')) }}</td>
                        <td><strong>#{{ $task->task_id }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.created_by')) }}</td>
                        <td><strong>{{ $task->first_name }} {{ $task->last_name }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.date_created')) }}</td>
                        <td><strong>{{ runtimeDate($task->task_created) }}</strong></td>
                    </tr>
                    @if(auth()->user()->is_team)
                    <tr>
                        <td>{{ cleanLang(__('lang.total_time')) }}</td>
                        <td><strong><span id="task_timer_all_card_{{ $task->task_id }}">{!!
                                    clean(runtimeSecondsHumanReadable($task->sum_all_time, false))
                                    !!}</span></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.time_invoiced')) }}</td>
                        <td><strong><span id="task_timer_all_card_{{ $task->task_id }}">{!!
                                    clean(runtimeSecondsHumanReadable($task->sum_invoiced_time, false))
                                    !!}</span></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.project')) }}</td>
                        <td><strong><a href="{{ urlResource('/projects/'.$task->task_projectid) }}"
                                    target="_blank">#{{ $task->project_id }}</a></strong>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


    <!-----------------------------popover dropdown elements------------------------------------>

    <!--task statuses - popover -->
    @if($task->permission_participate)
    <div class="hidden" id="card-task-statuses">
        <ul class="list">
            @foreach(config('task_statuses') as $task_status)
            <li class="card-tasks-update-status-link" data-button-text="card-task-status-text"
                data-progress-bar='hidden' data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-status') }}"
                data-type="form" data-value="{{ $task_status->taskstatus_id }}" data-form-id="--set-dynamically--"
                data-ajax-type="post">
                {{ runtimeLang($task_status->taskstatus_title) }}</li>
            @endforeach
        </ul>
        <input type="hidden" name="task_status" id="task_status">
        <input type="hidden" name="current_task_status_text" id="current_task_status_text">
    </div>
    @endif


    <!--task priority - popover-->
    @if($task->permission_participate)
    <div class="hidden" id="card-task-priorities">
        <ul class="list">
            @foreach(config('settings.task_priority') as $key => $value)
            <li class="card-tasks-update-priority-link" data-button-text="card-task-priority-text"
                data-progress-bar='hidden' data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-priority') }}"
                data-type="form" data-value="{{ $key }}" data-form-id="--set-dynamically--" data-ajax-type="post">
                {{ runtimeLang($key) }}</li>
            @endforeach
        </ul>
        <input type="hidden" name="task_priority" id="task_priority">
        <input type="hidden" name="current_task_priority_text" id="current_task_priority_text">
    </div>
    @endif

    <!--client visibility - popover-->
    @if($task->permission_edit_task)
    <div class="hidden" id="card-task-visibility">
        <ul class="list">
            <li class="card-tasks-update-visibility-link" data-button-text="card-task-client-visibility-text"
                data-progress-bar='hidden' data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-visibility') }}"
                data-type="form" data-value="no" data-text="{{ cleanLang(__('lang.hidden')) }}"
                data-form-id="card-task-client-visibility" data-ajax-type="post">
                {{ cleanLang(__('lang.hidden')) }}
            </li>
            <li class="card-tasks-update-visibility-link" data-button-text="card-task-client-visibility-text"
                data-progress-bar='hidden' data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-visibility') }}"
                data-type="form" data-value="yes" data-text="{{ cleanLang(__('lang.visible')) }}"
                data-form-id="card-task-client-visibility" data-ajax-type="post">
                {{ cleanLang(__('lang.visible')) }}
            </li>
        </ul>
        <input type="hidden" name="task_client_visibility" id="task_client_visibility">
        <input type="hidden" name="current_task_client_visibility_text" id="current_task_client_visibility_text">
    </div>
    @endif

    <!--milestone - popover -->
    @if($task->permission_edit_task)
    <div class="hidden" id="card-task-milestones">
        <div class="form-group m-t-10">
            <select class="custom-select col-12 form-control form-control-sm" id="task_milestoneid"
                name="task_milestoneid">
                @if(isset($milestones))
                @foreach($milestones as $milestone)
                <option value="{{ $milestone->milestone_id }}">
                    {{ runtimeLang($milestone->milestone_title, 'task_milestone') }}</option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-milestone-button"
                data-progress-bar='hidden' data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-milestone') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>
    @endif


    <!--assign user-->
    <div class="hidden" id="card-task-team">
        <div class="card-assigned-popover-content">
            <div class="alert alert-info">Only users assigned to the project are shown in this list</div>
            <div class="line"></div>

            <!--staff users-->
            <h5>@lang('lang.team_members')</h5>
            @foreach($project_assigned as $staff)
            <div class="form-check m-b-15">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="assigned[{{ $staff->id }}]"
                        class="custom-control-input assigned_user_{{ $staff->id }}">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description"><img
                            src="{{ getUsersAvatar($staff->avatar_directory, $staff->avatar_filename) }}"
                            class="img-circle avatar-xsmall"> {{ $staff->first_name }} {{ $staff->last_name }}</span>
                </label>
            </div>
            @endforeach

            <div class="line"></div>

            <!--client users-->
            <h5>@lang('lang.client_users')</h5>
            @foreach($client_users as $staff)
            <div class="form-check m-b-15">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="assigned[{{ $staff->id }}]"
                        class="custom-control-input assigned_user_{{ $staff->id }}">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description"><img
                            src="{{ getUsersAvatar($staff->avatar_directory, $staff->avatar_filename) }}"
                            class="img-circle avatar-xsmall"> {{ $staff->first_name }} {{ $staff->last_name }}</span>
                </label>
            </div>
            @endforeach

            <div class="form-group text-right">
                <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-assigned"
                    data-progress-bar='hidden' data-url="{{ urlResource('/tasks/'.$task->task_id.'/update-assigned') }}"
                    data-type="form" data-ajax-type="post" data-form-id="popover-body">
                    {{ cleanLang(__('lang.update')) }}
                </button>
            </div>
        </div>
    </div>