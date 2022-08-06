<div class="card count-{{ @count($expenses) }}" id="expenses-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($expenses) > 0)
            <table id="expenses-list-table" class="table m-t-0 m-b-0 table-hover no-wrap expense-list"
                data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.expenses_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-expenses" name="listcheckbox-expenses"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="expenses-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-expenses">
                                <label for="listcheckbox-expenses"></label>
                            </span>
                        </th>
                        @endif
                        @if(config('visibility.expenses_col_date'))
                        <th class="expenses_col_date"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_expense_date" href="javascript:void(0)"
                                data-url="{{ urlResource('/expenses?action=sort&orderby=expense_date&sortorder=asc') }}">{{ cleanLang(__('lang.date')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        @if(config('visibility.expenses_col_description'))
                        <th class="expenses_col_description"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_expense_description" href="javascript:void(0)"
                                data-url="{{ urlResource('/expenses?action=sort&orderby=expense_description&sortorder=asc') }}">{{ cleanLang(__('lang.description')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <!--column visibility-->
                        @if(config('visibility.expenses_col_user'))
                        <th class="expenses_col_user"><a class="js-ajax-ux-request js-list-sorting" id="sort_user"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expenses?action=sort&orderby=user&sortorder=asc') }}">{{ cleanLang(__('lang.user')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <!--column visibility-->
                        @if(config('visibility.expenses_col_client'))
                        <th class="expenses_col_client"><a class="js-ajax-ux-request js-list-sorting" id="sort_client"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expenses?action=sort&orderby=client&sortorder=asc') }}">{{ cleanLang(__('lang.client')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <!--column visibility-->
                        @if(config('visibility.expenses_col_project'))
                        <th class="expenses_col_project"><a class="js-ajax-ux-request js-list-sorting" id="sort_project"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expenses?action=sort&orderby=project&sortorder=asc') }}">{{ cleanLang(__('lang.project')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        @if(config('visibility.expenses_col_amount'))
                        <th class="expenses_col_amount"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_expense_amount" href="javascript:void(0)"
                                data-url="{{ urlResource('/expenses?action=sort&orderby=expense_amount&sortorder=asc') }}">{{ cleanLang(__('lang.amount')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        @if(config('visibility.expenses_col_status'))
                        <th class="expenses_col_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_expense_billing_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/expenses?action=sort&orderby=expense_billing_status&sortorder=asc') }}">{{ cleanLang(__('lang.status')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        @if(config('visibility.expenses_col_action'))
                        <th class="expenses_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="expenses-td-container">
                    <!--ajax content here-->
                    @include('pages.expenses.components.table.ajax')
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
            @endif @if (@count($expenses) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>