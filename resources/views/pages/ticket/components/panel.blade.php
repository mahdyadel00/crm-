<!--details-->
<div class="col-sm-12 col-lg-3" id="ticket-left-panel">
    <div class="card">
        <div class="row">
            <div class="col-lg-12">
                <div class="ticket-panel">
                    <div class="x-top-header">
                        {{ cleanLang(__('lang.ticket_details')) }}
                    </div>

                    <div class="x-body">

                        <!--department-->
                        <div class="x-list">
                            <div class="x-name">{{ cleanLang(__('lang.department')) }}</div>
                            <div class="x-details">{{ $ticket->category_name }}</div>
                        </div>

                        <!--date-->
                        <div class="x-list">
                            <div class="x-name">{{ cleanLang(__('lang.created')) }}</div>
                            <div class="x-details">{{ runtimeDate($ticket->ticket_created) }} {{ cleanLang(__('lang.at')) }}
                                {{ runtimeTime($ticket->ticket_created) }}</div>
                        </div>

                        <!--client-->
                        @if(auth()->user()->is_team)
                        <div class="x-list">
                            <div class="x-name">{{ cleanLang(__('lang.client')) }}</div>
                            <div class="x-details">
                                <a href="/clients/{{ $ticket->ticket_clientid }}"
                                    title="{{ $ticket->client_company_name }}">{{ str_limit($ticket->client_company_name ?? '---', 35) }}</a>
                            </div>
                        </div>
                        @endif
                        
                        <!--project-->
                        <div class="x-list">
                            <div class="x-name">{{ cleanLang(__('lang.project')) }}</div>
                            <div class="x-details">
                                <a href="/projects/{{ $ticket->ticket_projectid }}"
                                    title="{{ $ticket->project_title }}">{{
                                    str_limit($ticket->project_title ?? '---', 30) }}</a></div>
                        </div>


                        <!--last activity-->
                        <div class="x-list">
                            <div class="x-name">{{ cleanLang(__('lang.activity')) }}</div>
                            <div class="x-details">{{ runtimeDateAgo($ticket->ticket_last_updated) }}</div>
                        </div>

                        <!--priority-->
                        <div class="x-list">
                            <div class="x-name">{{ cleanLang(__('lang.priority')) }}</div>
                            <div class="x-details"><span
                                    class="label {{ runtimeTicketPriorityColors($ticket->ticket_priority, 'label') }}">{{
                                    runtimeLang($ticket->ticket_priority) }}</span></div>
                        </div>

                        <!--status-->
                        <div class="x-list">
                            <div class="x-name">{{ cleanLang(__('lang.status')) }}</div>
                            <div class="x-details"><span
                                    class="label {{ runtimeTicketStatusColors($ticket->ticket_status, 'label') }}">{{
                                    runtimeLang($ticket->ticket_status) }}</span></div>
                        </div>

                        <!--edit button-->
                        @if(config('visibility.action_buttons_edit'))
                        <div class="x-list b-none">
                            <button type="button" class="btn btn-rounded-x btn-danger edit-add-modal-button js-ajax-ux-request"
                                data-toggle="modal"
                                data-url="/tickets/{{ $ticket->ticket_id }}/edit?edit_type=all&edit_source=leftpanel"
                                data-action-url="/tickets/{{ $ticket->ticket_id }}" data-target="#commonModal"
                                data-loading-target="commonModalBody" data-action-method="PUT"
                                data-modal-title="{{ cleanLang(__('lang.edit_ticket')) }}">
                                {{ cleanLang(__('lang.edit_ticket')) }}</button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!--options-->