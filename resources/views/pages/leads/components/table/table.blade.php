<div class="card count-{{ @count($leads) }}" id="leads-view-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($leads) > 0)
            <table id="leads-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.leads_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-leads" name="listcheckbox-leads"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="leads-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-leads">
                                <label for="listcheckbox-leads"></label>
                            </span>
                        </th>
                        @endif
                        <th class="leads_col_title">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_lead_title" href="javascript:void(0)"
                                data-url="{{ urlResource('/leads?action=sort&orderby=lead_title&sortorder=asc') }}">{{ cleanLang(__('lang.title')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                            </a>
                        </th>
                        <th class="leads_col_contact">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_lead_firstname"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/leads?action=sort&orderby=lead_firstname&sortorder=asc') }}">{{ cleanLang(__('lang.contact')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                            </a>
                        </th>
                        <th class="leads_col_date">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_lead_created"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/leads?action=sort&orderby=lead_created&sortorder=asc') }}">{{ cleanLang(__('lang.date')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                            </a>
                        </th>
                        <th class="leads_col_company">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_lead_company_name"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/leads?action=sort&orderby=category_name&sortorder=asc') }}">{{ cleanLang(__('lang.category')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                            </a>
                        </th>

                        <th class="leads_col_assigned">{{ cleanLang(__('lang.assigned')) }}</th>
                        <th class="leads_col_stage">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/leads?action=sort&orderby=status&sortorder=asc') }}">{{ cleanLang(__('lang.status')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                            </a>
                        </th>
                        <th class="leads_col_value">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_lead_value" href="javascript:void(0)"
                                data-url="{{ urlResource('/leads?action=sort&orderby=lead_value&sortorder=asc') }}">{{ cleanLang(__('lang.value')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                            </a>
                        </th>
                        <th class="leads_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="leads-td-container">
                    <!--ajax content here-->
                    @include('pages.leads.components.table.ajax')
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
            @endif @if (@count($leads) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>