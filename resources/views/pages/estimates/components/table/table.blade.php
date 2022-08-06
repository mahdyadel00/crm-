<div class="card count-{{ @count($estimates) }}" id="estimates-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($estimates) > 0)
            <table id="estimates-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list"
                data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.estimates_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-estimates" name="listcheckbox-estimates"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="estimates-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-estimates">
                                <label for="listcheckbox-estimates"></label>
                            </span>
                        </th>
                        @endif
                        <th class="estimates_col_id"><a class="js-ajax-ux-request js-list-sorting" id="sort_bill_estimateid"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/estimates?action=sort&orderby=bill_estimateid&sortorder=asc') }}">{{ cleanLang(__('lang.id')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="estimates_col_date"><a class="js-ajax-ux-request js-list-sorting"
                            id="sort_bill_date" href="javascript:void(0)"
                            data-url="{{ urlResource('/estimates?action=sort&orderby=bill_date&sortorder=asc') }}">{{ cleanLang(__('lang.date')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                    </th>
                        @if(config('visibility.estimates_col_client'))
                        <th class="estimates_col_company"><a class="js-ajax-ux-request js-list-sorting" id="sort_client"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/estimates?action=sort&orderby=client&sortorder=asc') }}">{{ cleanLang(__('lang.company_name')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @endif
                        @if(config('visibility.estimates_col_created_by'))
                        <th class="estimates_col_created_by"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_created_by" href="javascript:void(0)"
                                data-url="{{ urlResource('/estimates?action=sort&orderby=created_by&sortorder=asc') }}">{{ cleanLang(__('lang.created_by')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @endif
                        @if(config('visibility.estimates_col_expires'))
                        <th class="estimates_col_expires"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_bill_expiry_date" href="javascript:void(0)"
                                data-url="{{ urlResource('/estimates?action=sort&orderby=bill_expiry_date&sortorder=asc') }}">{{ cleanLang(__('lang.expires')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        @if(config('visibility.estimates_col_tags'))
                        <th class="estimates_col_tags"><a href="javascript:void(0)">{{ cleanLang(__('lang.tags')) }}</a></th>
                        @endif
                        <th class="estimates_col_amount"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_bill_final_amount" href="javascript:void(0)"
                                data-url="{{ urlResource('/estimates?action=sort&orderby=bill_final_amount&sortorder=asc') }}">{{ cleanLang(__('lang.amount')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="estimates_col_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_bill_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/estimates?action=sort&orderby=bill_status&sortorder=asc') }}">{{ cleanLang(__('lang.status')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="estimates_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="estimates-td-container">
                    <!--ajax content here-->
                    @include('pages.estimates.components.table.ajax')
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
            @endif @if (@count($estimates) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>