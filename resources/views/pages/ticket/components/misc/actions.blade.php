<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-3 align-self-center text-right parent-page-actions p-b-9"
        id="list-page-actions-container">
        <div id="list-page-actions">
                <!--edit (nb: the second condition is needed for timeline [right actions nav] replacement-->
                @if(config('visibility.action_buttons_edit'))
                <!--reminder-->
                @if(config('visibility.modules.reminders'))
                <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.reminder')) }}"
                        id="reminders-panel-toggle-button"
                        class="reminder-toggle-panel-button list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-reminder-panel ajax-request {{ $ticket->reminder_status }}"
                        data-url="{{ url('reminders/start?resource_type=ticket&resource_id='.$ticket->ticket_id) }}"
                        data-loading-target="reminders-side-panel-body" data-progress-bar='hidden'
                        data-target="reminders-side-panel" data-title="@lang('lang.my_reminder')">
                        <i class="ti-alarm-clock"></i>
                </button>
                @endif
                <span class="dropdown">
                        <button type="button"
                                class="data-toggle-tooltip list-actions-button btn btn-page-actions waves-effect waves-dark edit-add-modal-button js-ajax-ux-request"
                                data-toggle="modal"
                                data-url="/tickets/{{ $ticket->ticket_id }}/edit?edit_type=all&edit_source=leftpanel"
                                data-action-url="/tickets/{{ $ticket->ticket_id }}" data-target="#commonModal"
                                data-loading-target="commonModalBody" data-action-method="PUT"
                                data-modal-title="{{ cleanLang(__('lang.edit_ticket')) }}">
                                <i class="sl-icon-note"></i>
                        </button>
                </span>
                @endif

                @if(auth()->user()->role->role_tickets >= 3)
                <!--delete-->
                <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.delete_ticket')) }}"
                        class="list-actions-button btn btn-page-actions waves-effect waves-dark confirm-action-danger"
                        data-confirm-title="{{ cleanLang(__('lang.delete_ticket')) }}"
                        data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                        data-url="{{ url('/tickets/'.$ticket->ticket_id.'?source=page') }}"><i
                                class="sl-icon-trash"></i></button>
                @endif

                @if(auth()->user()->is_client)
                <!--reminder-->
                @if(config('visibility.modules.reminders'))
                <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.reminder')) }}"
                        id="reminders-panel-toggle-button"
                        class="reminder-toggle-panel-button list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-reminder-panel ajax-request {{ $ticket->reminder_status }}"
                        data-url="{{ url('reminders/start?resource_type=ticket&resource_id='.$ticket->ticket_id) }}"
                        data-loading-target="reminders-side-panel-body" data-progress-bar='hidden'
                        data-target="reminders-side-panel" data-title="@lang('lang.my_reminder')">
                        <i class="ti-alarm-clock"></i>
                </button>
                @endif
                @endif
        </div>
</div>