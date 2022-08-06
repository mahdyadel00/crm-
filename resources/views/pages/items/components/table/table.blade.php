<div class="card count-{{ @count($items) }}" id="items-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($items) > 0)
            <table id="items-list-table" class="table m-t-0 m-b-0 table-hover no-wrap item-list" data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.items_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-items" name="listcheckbox-items"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="items-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-items">
                                <label for="listcheckbox-items"></label>
                            </span>
                        </th>
                        @endif
                        <th class="items_col_description"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_item_description" href="javascript:void(0)"
                                data-url="{{ urlResource('/items?action=sort&orderby=item_description&sortorder=asc') }}">{{ cleanLang(__('lang.description')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="items_col_rate"><a class="js-ajax-ux-request js-list-sorting" id="sort_item_rate"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/items?action=sort&orderby=item_rate&sortorder=asc') }}">{{ cleanLang(__('lang.rate')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="items_col_unit"><a class="js-ajax-ux-request js-list-sorting" id="sort_item_unit"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/items?action=sort&orderby=item_unit&sortorder=asc') }}">{{ cleanLang(__('lang.unit')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.items_col_category'))
                        <th class="items_col_category"><a class="js-ajax-ux-request js-list-sorting" id="sort_category"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/items?action=sort&orderby=category&sortorder=asc') }}">{{ cleanLang(__('lang.category')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        @if(config('visibility.items_col_action'))
                        <th class="items_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="items-td-container">
                    <!--ajax content here-->
                    @include('pages.items.components.table.ajax')
                    <!--ajax content here-->

                    <!--bulk actions - change category-->
                    <input type="hidden" name="checkbox_actions_items_category" id="checkbox_actions_items_category">
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
            @endif @if (@count($items) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>