<div class="card count-{{ @count($categories) }}"  id="categories-table-wrapper" data-payload="{{ request('filter_category_type') }}" >
    <div class="card-body">
        <div class="table-responsive">
            @if (@count($categories) > 0)
            <table id="demo-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        <th class="categories_col_name">{{ cleanLang(__('lang.name')) }}</th>
                        <th class="categories_col_created_by">{{ cleanLang(__('lang.created_by')) }}</th>
                        <th class="categories_col_date">{{ cleanLang(__('lang.date_created')) }}</th>
                        <th class="categories_col_items">{{ cleanLang(__('lang.items')) }}</th>
                        <th class="categories_col_status">{{ cleanLang(__('lang.status')) }}</th>
                        @if(request('filter_category_type')=='project')
                        <th class="categories_col_users">{{ cleanLang(__('lang.team')) }}</th>
                        @endif
                        <th class="categories_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="categories-td-container">
                    <!--ajax content here-->
                    @include('pages.categories.components.table.ajax')
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
            @endif
            @if (@count($categories) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif

            <div>
                <!--settings documentation help-->
                <a href="https://growcrm.io/documentation/category-settings/"  target="_blank" class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
            </div>
        </div>
    </div>
</div>