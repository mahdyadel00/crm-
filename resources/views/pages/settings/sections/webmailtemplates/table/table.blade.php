<div class="table-responsive">
    @if (@count($templates) > 0)
    <table id="demo-webform-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
        <thead>
            <tr>
                <th class="templates_col_name">@lang('lang.name')</th>
                <th class="templates_col_date">@lang('lang.date_created')</th>
                <th class="templates_col_created_by">@lang('lang.created_by')</th>
                <th class="templates_col_action">@lang('lang.actions')</th>
            </tr>
        </thead>
        <tbody id="templates-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.webmailtemplates.table.ajax')
            <!--ajax content here-->
        </tbody>
    </table>
    <!--settings documentation help-->
    <div class="m-t-40">
        <a href="https://growcrm.io/documentation/webmail-templates/" target="_blank"
            class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
    @endif
    @if (@count($templates) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
</div>