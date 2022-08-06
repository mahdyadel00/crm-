@foreach($timesheets as $timesheet)
<!--each row-->
<tr id="timesheet_{{ $timesheet->timer_id }}">
    @if(config('visibility.timesheets_col_checkboxes'))
    <td class="timesheets_col_checkbox checkitem" id="timesheets_col_checkbox_{{ $timesheet->timer_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-timesheets-{{ $timesheet->timer_id }}"
                name="ids[{{ $timesheet->timer_id }}]"
                class="listcheckbox listcheckbox-timesheets filled-in chk-col-light-blue"
                data-actions-container-class="timesheets-checkbox-actions-container"
                {{ runtimeDisabledTimesheetsCheckboxes(config('visibility.timesheets_disable_actions')) }}
                @if($timesheet->timer_billing_status == 'invoiced') disabled @endif>
            <label for="listcheckbox-timesheets-{{ $timesheet->timer_id }}"></label>
        </span>
    </td>
    @endif
    <td class="timesheets_col_user">

        @if(config('visibility.timesheets_grouped_by_users'))
        <span class="sl-icon-people"></span> {{ cleanLang(__('lang.multiple')) }}
        @else
        <img src="{{ getUsersAvatar($timesheet->avatar_directory, $timesheet->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall"> {{ str_limit($timesheet->first_name ?? runtimeUnkownUser(), 10) }}
        @endif

    </td>
    <td class="timesheets_col_task">
        <a class="show-modal-button reset-card-modal-form js-ajax-ux-request" href="javascript:void(0)"
            data-toggle="modal" data-target="#cardModal"
            data-url="{{ urlResource('/tasks/'.$timesheet->timer_taskid) }}"
            data-loading-target="main-top-nav-bar">{{ str_limit($timesheet->task_title ?? '---', 25) }}</a>
    </td>
    @if(config('visibility.timesheets_col_related'))
    <td class="timesheets_col_related">
        @if($timesheet->timer_projectid > 0)
        <a
            href="/projects/{{ $timesheet->timer_projectid }}">{{ str_limit($timesheet->project_title ?? '---', 25) }}</a>
        @else
        <a href="/leads/{{ $timesheet->timer_leadid }}">{{ str_limit($timesheet->lead_title ?? '---', 25) }}</a>
        @endif
    </td>
    @endif

    <!--date-->
    <td class="timesheets_col_start_time">{{ runtimeDate($timesheet->timer_created) }}</td>

    <!--billing status-->
    <td class="timesheets_col_billing_status">
        @if(request('filter_grouping') == 'none' || !request()->filled('filter_grouping'))
        @if($timesheet->timer_billing_status == 'invoiced')
        <span class="label label-outline-info">{{ cleanLang(__('lang.invoiced')) }}</span>
        @else
        <span class="label label-outline-default">{{ cleanLang(__('lang.not_invoiced')) }}</span>
        @endif
        @else
        ---
        @endif
    </td>

    <td class="timesheets_col_time">{!! clean(runtimeSecondsHumanReadable($timesheet->time, true)) !!}</td>
    @if(config('visibility.timesheets_col_action'))
    <td class="timesheets_col_action">
        <span class="list-table-action dropdown  font-size-inherit">
        @if(config('visibility.timesheets_disable_actions'))
            <span data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}">---</span>
        @else
        @if(config('visibility.action_buttons_delete'))
        @if($timesheet->timer_billing_status == 'invoiced')
        <span class="btn btn-outline-default btn-circle btn-sm disabled" data-toggle="tooltip"
            title="{{ cleanLang(__('lang.item_is_attached_to_invoice_cannot_be_edited')) }}"><i
                class="sl-icon-trash"></i></span>
        @else
        <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
            class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
            data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
            data-url="{{ url('/') }}/timesheets/{{ $timesheet->timer_id }}">
            <i class="sl-icon-trash"></i>
        </button>
        @if(config('visibility.action_buttons_edit'))
        <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
            class="hidden data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
            data-toggle="modal" data-target="#commonModal"
            data-url="{{ urlResource('/timesheets/'.$timesheet->timer_id.'/edit') }}" data-loading-target="commonModalBody"
            data-modal-title="{{ cleanLang(__('lang.edit_timesheet')) }}"
            data-action-url="{{ url('/timesheets/'.$timesheet->timer_id.'?source=list') }}" data-action-method="PUT"
            data-action-ajax-class="js-ajax-request" data-action-ajax-loading-target="timesheets-td-container">
            <i class="sl-icon-note"></i>
        </button>
        @endif
        @endif
        @if($timesheet->timer_billing_status == 'invoiced')
        <a href="{{ url('/invoices/'.$timesheet->timer_billing_invoiceid) }}" title="{{ cleanLang(__('lang.view')) }}"
            class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
            <i class="ti-new-window"></i>
        </a>
        @endif
        @endif
        @endif
        </span>
    </td>
    @endif
</tr>
<!--each row-->
@endforeach