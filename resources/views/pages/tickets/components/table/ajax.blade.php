@foreach($tickets as $ticket)
<!--each row-->
<tr id="ticket_{{ $ticket->ticket_id }}">
    @if(config('visibility.tickets_col_checkboxes'))
    <td class="tickets_col_checkbox checkitem hidden" id="tickets_col_checkbox_{{ $ticket->ticket_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-tickets-{{ $ticket->ticket_id }}"
                name="ids[{{ $ticket->ticket_id }}]"
                class="listcheckbox listcheckbox-tickets filled-in chk-col-light-blue"
                data-actions-container-class="tickets-checkbox-actions-container">
            <label for="listcheckbox-tickets-{{ $ticket->ticket_id }}"></label>
        </span>
    </td>
    @endif
    @if(config('visibility.tickets_col_id'))
    <td class="tickets_col_id"><a href="/tickets/{{ $ticket->ticket_id }}">{{ $ticket->ticket_id }}</a></td>
    @endif
    <td class="tickets_col_subject">
        <a href="/tickets/{{ $ticket->ticket_id }}">{{ str_limit($ticket->ticket_subject ?? '---', 35) }}</a>
    </td>
    @if(config('visibility.tickets_col_client'))
    <td class="tickets_col_client">
        {{ str_limit($ticket->client_company_name ?? '---', 15) }}
    </td>
    @endif
    @if(config('visibility.tickets_col_department'))
    <td class="tickets_col_department">
        {{ str_limit($ticket->category_name ?? '---', 30) }}
    </td>
    @endif
    <td class="tickets_col_date">
        {{ runtimeDate($ticket->ticket_created) }}
    </td>
    <td class="tickets_col_priority">
        <span class="label {{ runtimeTicketPriorityColors($ticket->ticket_priority, 'label') }}">{{
                        runtimeLang($ticket->ticket_priority) }}</span>
    </td>
    @if(config('visibility.tickets_col_activity'))
    <td class="tickets_col_activity">
        {{ runtimeDateAgo($ticket->ticket_last_updated) }}
    </td>
    @endif
    <td class="tickets_col_status">
        <span class="label {{ runtimeTicketStatusColors($ticket->ticket_status, 'label') }}">{{
                        runtimeLang($ticket->ticket_status) }}</span>
    </td>
    <td class="tickets_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE" data-url="{{ url('/') }}/tickets/{{ $ticket->ticket_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/tickets/'.$ticket->ticket_id.'/edit?edit_type=all&edit_source=list') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_ticket')) }}"
                data-action-url="{{ urlResource('/tickets/'.$ticket->ticket_id) }}" data-action-method="PUT"
                data-action-ajax-class="js-ajax-ux-request" data-action-ajax-loading-target="tickets-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @endif
            <a href="/tickets/{{ $ticket->ticket_id }}" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->