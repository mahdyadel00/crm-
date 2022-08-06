@foreach($board['tasks'] as $task)
<!--each card-->
<div class="kanban-card show-modal-button reset-card-modal-form js-ajax-ux-request" data-toggle="modal"
    data-target="#cardModal" data-url="{{ urlResource('/tasks/'.$task->task_id) }}" data-task-id="{{ $task->task_id }}"
    data-loading-target="main-top-nav-bar" id="card_task_{{ $task->task_id }}">
    <div class="x-title wordwrap" id="kanban_task_title_{{ $task->task_id }}">{{ $task->task_title }}
        <span class="x-action-button" id="card-action-button-{{ $task->task_id }}" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></span>
        <div class="dropdown-menu dropdown-menu-small dropdown-menu-right js-stop-propagation"
            aria-labelledby="card-action-button-{{ $task->task_id }}">
            @php $count_actions = 0 ; @endphp
            <!--delete-->
            @if($task->permission_delete_task)
            <a class="dropdown-item confirm-action-danger  js-stop-propagation"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/tasks/{{ $task->task_id }}">{{ cleanLang(__('lang.delete')) }}</a>
            @php $count_actions ++ ; @endphp
            @endif

            <!--clone task (team only)-->
            @if(auth()->user()->is_team && $task->permission_edit_task)
            <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal" data-modal-title="@lang('lang.clone_task')"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/clone') }}"
                data-action-url="{{ urlResource('/tasks/'.$task->task_id.'/clone') }}" data-modal-size="modal-lg"
                data-loading-target="commonModalBody" data-action-method="POST" aria-expanded="false">
                @lang('lang.clone_task')
            </a>
            @php $count_actions ++ ; @endphp
            @endif

            <!--record time-->
            @if($task->assigned_to_me)
            <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-confirm-title="{{ cleanLang(__('lang.archive_task')) }}" data-toggle="modal"
                data-target="#commonModal" data-modal-title="@lang('lang.record_your_work_time')"
                data-url="{{ url('/timesheets/create?task_id='.$task->task_id) }}"
                data-action-url="{{ urlResource('/timesheets') }}" data-modal-size="modal-sm"
                data-loading-target="commonModalBody" data-action-method="POST" aria-expanded="false">
                {{ cleanLang(__('lang.record_time')) }}
            </a>
            @php $count_actions ++ ; @endphp
            @endif

            <!--stop my timer-->
            @if($task->timer_current_status)
            <a class="dropdown-item confirm-action-danger js-stop-propagation"
                data-confirm-title="{{ cleanLang(__('lang.stop_my_timer')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="GET"
                data-url="{{ url('/') }}/tasks/timer/{{ $task->task_id }}/stop">{{ cleanLang(__('lang.stop_my_timer')) }}</a>
            @php $count_actions ++ ; @endphp
            @endif
            <!--stop all timers-->
            @if(auth()->user()->is_team && $task->permission_super_user)
            <a class="dropdown-item confirm-action-danger js-stop-propagation"
                data-confirm-title="{{ cleanLang(__('lang.stop_all_timers')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="GET"
                data-url="{{ url('/') }}/tasks/timer/{{ $task->task_id }}/stopall?source=list">{{ cleanLang(__('lang.stop_all_timers')) }}</a>
            @php $count_actions ++ ; @endphp
            @endif


            @if(auth()->user()->is_team && $task->permission_edit_task)
            <!--recurring settings-->
            <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=list') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.recurring_settings')) }}"
                data-action-url="{{ urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=list') }}"
                data-action-method="POST"
                data-action-ajax-loading-target="tasks-td-container">{{ cleanLang(__('lang.recurring_settings')) }}</a>
            <!--stop recurring -->
            @if($task->task_recurring == 'yes')
            <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                data-confirm-title="{{ cleanLang(__('lang.stop_recurring')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/stop-recurring?source=list') }}">
                {{ cleanLang(__('lang.stop_recurring')) }}</a>
            @endif
            @endif

            <!--archive-->
            @if($task->permission_super_user && runtimeArchivingOptions())
            <a class="dropdown-item confirm-action-info {{ runtimeActivateOrAchive('archive-button', $task->task_active_state) }} card_archive_button_{{ $task->task_id }}"
                id="card_archive_button_{{ $task->task_id }}"
                data-confirm-title="{{ cleanLang(__('lang.archive_task')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/archive') }}">
                {{ cleanLang(__('lang.archive')) }}
            </a>
            @php $count_actions ++ ; @endphp
            @endif

            <!--activate-->
            @if($task->permission_super_user && runtimeArchivingOptions())
            <a class="dropdown-item confirm-action-info {{ runtimeActivateOrAchive('activate-button', $task->task_active_state) }} card_restore_button_{{ $task->task_id }}"
                id="card_restore_button_{{ $task->task_id }}"
                data-confirm-title="{{ cleanLang(__('lang.restore_task')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/activate') }}">
                {{ cleanLang(__('lang.restore')) }}
            </a>
            @php $count_actions ++ ; @endphp
            @endif

            <!--no actions-->
            @if($count_actions == 0)
            <a class="dropdown-item js-stop-propagation"
                href="javascript:void(0);">{{ cleanLang(__('lang.no_actions_available')) }}</a>
            @endif
        </div>
    </div>
    <div class="x-meta">
        <!--priority-->
        @if(config('system.settings_tasks_kanban_priority') == 'show')
        <label class="label {{ runtimeTaskPriorityColors($task->task_priority, 'label') }} p-t-3 p-b-3 p-l-8 p-r-8"
            data-toggle="tooltip"
            title="{{ cleanLang(__('lang.priority')) }}">{{ runtimeLang($task->task_priority) }}</label>
        @endif
        <!--project-->
        @if(config('system.settings_tasks_kanban_project_title') == 'show')
        <span title="{{ $task->project_title ?? '---' }}"><strong>{{ cleanLang(__('lang.project')) }}:</strong>
            {{ str_limit($task->project_title ??'---', 68) }}</span>
        @endif
        <!--client-->
        @if(config('system.settings_tasks_kanban_client_name') == 'show')
        <span title="{{ $task->client_company_name ?? '---' }}"><strong>{{ cleanLang(__('lang.client')) }}:</strong>
            {{ str_limit($task->client_company_name ??'---', 68) }}</span>
        @endif
        <!--date created-->
        @if(config('system.settings_tasks_kanban_date_created') == 'show')
        <span><strong>{{ cleanLang(__('lang.created')) }}:</strong> {{ runtimeDate($task->task_created) }}</span>
        @endif
        <!--start date-->
        @if(config('system.settings_tasks_kanban_date_start') == 'show')
        <span><strong>{{ cleanLang(__('lang.start_date')) }}:</strong>: {{ runtimeDate($task->task_date_start) }}</span>
        @endif
        <!--due date-->
        @if(config('system.settings_tasks_kanban_date_due') == 'show')
        <span><strong>{{ cleanLang(__('lang.due')) }}:</strong>: {{ runtimeDate($task->task_date_due) }}</span>
        @endif

        <!--show enabled custom fields-->
        @foreach($task->fields as $field)
        @if($field->customfields_show_task_summary == 'yes')
        <span><strong>{{ $field->customfields_title }}:</strong>:
            {{ strip_tags(customFieldValue($field->customfields_name, $task, $field->customfields_datatype)) }}</span>
        @endif
        @endforeach

    </div>
    <div class="x-footer row">
        <div class="col-6 x-icons">

            <!--recurring-->
            @if(auth()->user()->is_team && $task->task_recurring == 'yes')
            <span class="x-icon text-danger p-l-5  display-inline-block font-14 vm " data-toggle="tooltip" title="@lang('lang.recurring_task')"><i
                    class="sl-icon-refresh"></i></span>
            @endif

            <!--created by you-->
            @if($task->task_creatorid == auth()->user()->id)
            <span class="x-icon text-info" data-toggle="tooltip" title="@lang('lang.you_created_this_task')"
                data-placement="top"><i class="mdi mdi-account-circle"></i></span>
            @endif
            <!--archived-->
            @if(runtimeArchivingOptions())
            <span class="x-icon {{ runtimeActivateOrAchive('archived-icon', $task->task_active_state) }}"
                id="archived_icon_{{ $task->task_id }}" data-toggle="tooltip" title="@lang('lang.archived')"><i
                    class="ti-archive font-15"></i></span>
            @endif


            <!--client visibility-->
            @if(config('system.settings_tasks_kanban_client_visibility') == 'show' && auth()->user()->is_team)
            @if($task->task_client_visibility == 'no')
            <span class="x-icon" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.client')) }} - {{ cleanLang(__('lang.hidden')) }}" data-placement="top"><i
                    class="mdi mdi-eye-outline-off"></i></span>
            @endif
            @endif

            <!--attachments-->
            @if($task->has_attachments)
            <span class="x-icon"><i class="mdi mdi-attachment"></i>
                @if($task->count_unread_attachments > 0)
                <span class="x-notification" id="card_notification_attachment_{{ $task->task_id }}"></span>
                @endif
            </span>
            @endif
            <!--comments-->
            @if($task->has_comments)
            <span class="x-icon"><i class="mdi mdi-comment-text-outline"></i>
                @if($task->count_unread_comments > 0)
                <span class="x-notification" id="card_notification_comment_{{ $task->task_id }}"></span>
                @endif
            </span>
            @endif

            <!--checklists-->
            @if($task->has_checklist)
            <span class="x-icon"><i class="mdi mdi-checkbox-marked-outline"></i></span>
            @endif

            <!--timer running-->
            <span class="x-icon text-danger {{ runtimeCardMyRunningTimer($task->timer_current_status) }}"
                id="card-task-timer-{{ $task->task_id }}"><i class="mdi mdi-timer"></i></span>

        </div>
        <div class="col-6 x-assigned">
            @foreach($task->assigned as $user)
            <img src="{{ getUsersAvatar($user->avatar_directory, $user->avatar_filename) }}" data-toggle="tooltip"
                title="" data-placement="top" alt="{{ $user->first_name }}" class="img-circle avatar-xsmall"
                data-original-title="{{ $user->first_name }}">
            @endforeach
        </div>
    </div>
</div>
@endforeach