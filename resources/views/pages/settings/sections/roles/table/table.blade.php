<div class="table-responsive">
    @if (@count($roles) > 0)
    <table id="demo-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
        <thead>
            <tr>
                <th class="roles_col_name">{{ cleanLang(__('lang.name')) }}</th>
                <th class="roles_col_users">{{ cleanLang(__('lang.active_users')) }}</th>
                <th class="roles_col_type">{{ cleanLang(__('lang.type')) }}</th>
                <th class="roles_col_status">{{ cleanLang(__('lang.status')) }}</th>
                <th class="roles_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
            </tr>
        </thead>
        <tbody id="roles-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.roles.table.ajax')
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
    @if (@count($roles) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/user-roles/"  target="_blank" class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
</div>