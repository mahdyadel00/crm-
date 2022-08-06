<div class="card count-{{ @count($subscriptions) }}" id="subscriptions-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($subscriptions) > 0)
            <table id="subscription-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <th class="subscriptions_col_id">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_subscription_id"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_id&sortorder=asc') }}">{{ cleanLang(__('lang.id')) }}
                                #<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @if(config('visibility.subscriptions_col_client'))
                        <th class="subscriptions_col_company">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_client" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=client&sortorder=asc') }}">{{ cleanLang(__('lang.client_name')) }}
                                <span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @endif
                        <th class="subscriptions_col_plan">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_plan" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=plan&sortorder=asc') }}">{{ cleanLang(__('lang.plan')) }}
                                <span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="subscriptions_col_amount">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_amount" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=amount&sortorder=asc') }}">{{ cleanLang(__('lang.amount')) }}
                                <span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="subscriptions_col_renewed"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_date_renewed" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=date_renewed&sortorder=asc') }}">
                                {{ cleanLang(__('lang.renewed')) }}<span class="sorting-icons"><i
                                        class="ti-arrows-vertical"></i></span></a></th>
                        <th class="subscriptions_col_payments"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_payments" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=payments&sortorder=asc') }}">
                                {{ cleanLang(__('lang.payments')) }}
                                <span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="subscriptions_col_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=status&sortorder=asc') }}">
                                {{ cleanLang(__('lang.status')) }}<span class="sorting-icons"><i
                                        class="ti-arrows-vertical"></i></span></a></th>
                        <th class="subscriptions_col_action"><a
                                href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="subscriptions-td-container">
                    <!--ajax content here-->
                    @include('pages.subscriptions.components.table.ajax')
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
            @endif @if (@count($subscriptions) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>