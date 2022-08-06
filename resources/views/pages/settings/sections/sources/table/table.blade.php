<div class="table-responsive">
    @if (@count($sources) > 0)
    <table id="demo-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
        <thead>
            <tr>
                <th class="sources_col_name">{{ cleanLang(__('lang.name')) }}</th>
                <th class="sources_col_date">{{ cleanLang(__('lang.date_created')) }}</th>
                <th class="sources_col_created_by">{{ cleanLang(__('lang.created_by')) }}</th>
                <th class="sources_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
            </tr>
        </thead>
        <tbody id="sources-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.sources.table.ajax')
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
    @if (@count($sources) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
</div>