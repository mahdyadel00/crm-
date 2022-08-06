@foreach($milestones as $milestone)
<!--each row-->
<tr id="milestone_{{ $milestone->milestone_id }}">
    <td class="milestones_col_name">
        @if(config('visibility.milestone_actions'))
        <span class="mdi mdi-drag-vertical cursor-pointer"></span>
        @endif
        <a class="js-dynamic-url js-ajax-ux-request" data-loading-target="embed-content-container"
            data-dynamic-url="{{ url('/projects') }}/{{ $milestone->milestone_projectid }}/tasks?source=ext&taskresource_type=project&taskresource_id={{ $milestone->milestone_projectid }}&filter_task_milestoneid={{ $milestone->milestone_id }}"
            data-url="{{ url('/tasks') }}?source=ext&taskresource_type=project&taskresource_id={{ $milestone->milestone_projectid }}&filter_task_milestoneid={{ $milestone->milestone_id }}"
            href="#projects_ajaxtab">{{ runtimeLang($milestone->milestone_title, 'task_milestone') }}</a>

        @if($milestone->milestone_type == 'uncategorised')
        <span class="sl-icon-star text-warning p-l-5" data-toggle="tooltip" title="{{ cleanLang(__('lang.default_category')) }}"></span>
        @endif
        <!--sorting data-->
        @if(config('visibility.milestone_actions'))
        <input type="hidden" name="sort-milestones[{{ $milestone->milestone_id }}]"
            value="{{ $milestone->milestone_id }}">
        @endif
    </td>
    <td class="milestones_col_tasks">
        {{ $milestone->milestone_count_tasks_all }}
    </td>
    <td class="milestones_col_tasks_pending">
        {{ $milestone->milestone_count_tasks_pending }}
    </td>
    <td class="milestones_col_tasks_completed">
        {{ $milestone->milestone_count_tasks_completed }}
    </td>
    @if(config('visibility.milestone_actions'))
    <td class="milestones_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            @if($milestone->milestone_type == 'categorised')
            <!---delete milestone with confirm checkbox-->
            <span id="milestone_form_{{ $milestone->milestone_id }}">
                <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                    class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                    id="foobar" data-confirm-title="{{ cleanLang(__('lang.delete_milestone')) }}"
                    data-confirm-text="
                            <input type='checkbox' id='confirm_action_{{ $milestone->milestone_id }}' 
                                   class='filled-in chk-col-light-blue confirm_action_checkbox' 
                                   data-field-id='delete_milestone_tasks_{{ $milestone->milestone_id }}'>
                            <label for='confirm_action_{{ $milestone->milestone_id }}'>{{ cleanLang(__('lang.delete_all_tasks')) }}</label>" data-ajax-type="DELETE" data-type="form"
                    data-form-id="milestone_form_{{ $milestone->milestone_id }}"
                    data-url="{{ url('/') }}/milestones/{{ $milestone->milestone_id }}?project_id={{ $milestone->milestone_projectid }}">
                    <i class="sl-icon-trash"></i>
                </button>
                <input type="hidden" class="confirm_hidden_fields" name="delete_milestone_tasks"
                    id="delete_milestone_tasks_{{ $milestone->milestone_id }}">
            </span>
            <!---/#delete milestone with confirm checkbox-->
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/milestones/'.$milestone->milestone_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_milestone')) }}"
                data-action-url="{{ urlResource('/milestones/'.$milestone->milestone_id.'?ref=list') }}"
                data-action-method="PUT" data-action-ajax-class=""
                data-action-ajax-loading-target="milestones-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i class="sl-icon-trash"></i></span>
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i class="sl-icon-note"></i></span>
            @endif
        </span>
        <!--action button-->
    </td>
    @endif
</tr>
@endforeach
<!--each row-->