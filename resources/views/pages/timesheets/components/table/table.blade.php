<div class="card count-{{ @count($timesheets) }}" id="timesheets-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($timesheets) > 0)
            <table id="timesheets-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list"
                data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.timesheets_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-timesheets" name="listcheckbox-timesheets"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="timesheets-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-timesheets"
                                    {{ runtimeDisabledTimesheetsCheckboxes(config('visibility.timesheets_disable_actions')) }}>
                                <label for="listcheckbox-timesheets"></label>
                            </span>
                        </th>
                        @endif
                        <th class="timesheets_col_user"><a class="js-ajax-ux-request js-list-sorting" id="sort_user"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/timesheets?action=sort&orderby=user&sortorder=asc') }}">{{ cleanLang(__('lang.user')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="timesheets_col_task"><a class="js-ajax-ux-request js-list-sorting" id="sort_task"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/timesheets?action=sort&orderby=task&sortorder=asc') }}">{{ cleanLang(__('lang.task')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.timesheets_col_related'))
                        <th class="timesheets_col_related"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_related" href="javascript:void(0)"
                                data-url="{{ urlResource('/timesheets?action=sort&orderby=related&sortorder=asc') }}">{{ cleanLang(__('lang.project')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @endif

                        <!--date-->
                        <th class="timesheets_col_start_time"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_start_time" href="javascript:void(0)"
                                data-url="{{ urlResource('/timesheets?action=sort&orderby=start_time&sortorder=asc') }}">{{ cleanLang(__('lang.date')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--billing status-->
                        <th class="timesheets_col_billing_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_billing_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/timesheets?action=sort&orderby=billing_status&sortorder=asc') }}">{{ cleanLang(__('lang.invoiced')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <th class="timesheets_col_time"><a class="js-ajax-ux-request js-list-sorting" id="sort_time"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/timesheets?action=sort&orderby=time&sortorder=asc') }}">{{ cleanLang(__('lang.time')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.timesheets_col_action'))
                        <th class="timesheets_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="timesheets-td-container">
                    <!--ajax content here-->
                    @include('pages.timesheets.components.table.ajax')
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
            @endif @if (@count($timesheets) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>