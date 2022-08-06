@foreach($tasks as $task)
<!--each row-->
<tr id="task_{{ $task->task_id }}">
    <td class="tasks_col_checkbox checkitem" id="tasks_col_checkbox_{{ $task->task_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes" id="fx-timebilling-list">
            <input type="checkbox" id="listcheckbox-tasks-{{ $task->task_id }}" name="ids[{{ $task->task_id }}]"
                class="listcheckbox listcheckbox-tasks filled-in chk-col-light-blue tasks-checkbox"
                data-actions-container-class="tasks-checkbox-actions-container" 
                data-task-id="{{ $task->task_id }}"
                data-timers-list="{{ $task->timer_ids }}"
                data-project-id="{{ $task->task_projectid }}"
                data-description="{{ $task->task_title }}"
                data-hours="{{ $task->hours }}"
                data-minutes="{{ $task->minutes }}"
                data-total="{{ $task->total }}"
                data-rate="{{ $billing_rate }}"
                data-unit="{{ cleanLang(__('lang.time')) }}"
                data-linked-type="timer"
                data-linked-id="{{ $task->task_id }}">
            <label for="listcheckbox-tasks-{{ $task->task_id }}"></label>
        </span>
    </td>
    <td class="tasks_col_title">
        {{ $task->task_title }}
    </td>
    <td class="tasks_col_time">
        @if(runtimeSecondsWholeHours($task->time) > 0)
        {{ runtimeSecondsWholeHours($task->time) }} Hrs 
        @endif        
        {{ runtimeSecondsWholeMinutes($task->time) }} Mins 
    </td>
</tr>
@endforeach