<div class="card count-{{ @count($clients) }}" id="clients-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($clients) > 0)
            <table id="clients-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <th class="clients_col_id">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_client_id" href="javascript:void(0)"
                                data-url="{{ urlResource('/clients?action=sort&orderby=client_id&sortorder=asc') }}">{{ cleanLang(__('lang.id')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="clients_col_company">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_client_company_name"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/clients?action=sort&orderby=client_company_name&sortorder=asc') }}">{{ cleanLang(__('lang.company_name')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="clients_col_account_owner">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_contact" href="javascript:void(0)"
                                data-url="{{ urlResource('/clients?action=sort&orderby=contact&sortorder=asc') }}">{{ cleanLang(__('lang.account_owner')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @if(config('visibility.modules.projects'))
                        <th class="clients_col_projects">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_count_projects"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/clients?action=sort&orderby=count_projects&sortorder=asc') }}">{{ cleanLang(__('lang.projects')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <th class="clients_col_invoices">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_sum_invoices"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/clients?action=sort&orderby=sum_invoices&sortorder=asc') }}">{{ cleanLang(__('lang.invoices')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="clients_col_tags"><a href="javascript:void(0)">{{ cleanLang(__('lang.tags')) }}</a>
                        </th>
                        <th class="clients_col_category">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_category" href="javascript:void(0)"
                                data-url="{{ urlResource('/clients?action=sort&orderby=category&sortorder=asc') }}">{{ cleanLang(__('lang.category')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="clients_col_status">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_client_status"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/clients?action=sort&orderby=client_status&sortorder=asc') }}">{{ cleanLang(__('lang.status')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.action_column'))
                        <th class="clients_col_action"><a
                                href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="clients-td-container">
                    <!--ajax content here-->
                    @include('pages.clients.components.table.ajax')
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
            @endif @if (@count($clients) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>