<div class="table-responsive">
    @if (@count($foos) > 0)
    <table id="demo-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
        <thead>
            <tr>
                <th class="foos_col_name">@lang('lang.foo')</th>
                <th class="foos_col_date">@lang('lang.foo')</th>
                <th class="foos_col_created_by">@lang('lang.foo')</th>
                <th class="foos_col_action"><a href="javascript:void(0)">@lang('lang.foo')</a></th>
            </tr>
        </thead>
        <tbody id="foos-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.foos.table.ajax')
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
    <!--settings documentation help-->
    <div>
        <a href="https://growcrm.io/documentation/foo/" target="_blank"
            class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
    @endif
    @if (@count($foos) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
</div>