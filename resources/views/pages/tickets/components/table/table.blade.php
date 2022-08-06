<div class="card count-{{ @count($tickets) }}" id="tickets-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($tickets) > 0)
            <table id="tickets-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list"
                data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.tickets_col_checkboxes'))
                        <th class="list-checkbox-wrapper hidden">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-tickets" name="listcheckbox-tickets"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="tickets-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-tickets">
                                <label for="listcheckbox-tickets"></label>
                            </span>
                        </th>
                        @endif
                        @if(config('visibility.tickets_col_id'))
                        <th class="tickets_col_id"><a class="js-ajax-ux-request js-list-sorting" id="sort_ticket_id"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=ticket_id&sortorder=asc') }}">{{ cleanLang(__('lang.id')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <th class="tickets_col_subject"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_ticket_subject" href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=ticket_subject&sortorder=asc') }}">{{ cleanLang(__('lang.subject')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.tickets_col_client'))
                        <th class="tickets_col_client"><a class="js-ajax-ux-request js-list-sorting" id="sort_client"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=client&sortorder=asc') }}">{{ cleanLang(__('lang.client')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        @if(config('visibility.tickets_col_department'))
                        <th class="tickets_col_department"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_category" href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=category&sortorder=asc') }}">{{ cleanLang(__('lang.department')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <th class="tickets_col_date"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_ticket_created" href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=ticket_created&sortorder=asc') }}">{{ cleanLang(__('lang.date')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>

                        <th class="tickets_col_priority"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_ticket_priority" href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=ticket_priority&sortorder=asc') }}">{{ cleanLang(__('lang.priority')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.tickets_col_activity'))
                        <th class="tickets_col_activity"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_ticket_last_updated" href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=ticket_last_updated&sortorder=asc') }}">{{ cleanLang(__('lang.activity')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <th class="tickets_col_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_ticket_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/tickets?action=sort&orderby=ticket_status&sortorder=asc') }}">{{ cleanLang(__('lang.status')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>

                        <th class="tickets_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="tickets-td-container">
                    <!--ajax content here-->
                    @include('pages.tickets.components.table.ajax')
                    <!--ajax content here-->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--load more button-->
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif @if (@count($tickets) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>