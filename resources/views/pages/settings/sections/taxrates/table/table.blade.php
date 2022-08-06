<div class="table-responsive" id="taxrates-table-wrapper">
    @if (@count($taxrates) > 0)
    <table id="demo-taxrate-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
        <thead>
            <tr>
                <th class="taxrates_col_name">{{ cleanLang(__('lang.name')) }}</th>
                <th class="taxrates_col_value">{{ cleanLang(__('lang.rate')) }}</th>
                <th class="taxrates_col_created_by">{{ cleanLang(__('lang.created_by')) }}</th>
                <th class="taxrates_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
            </tr>
        </thead>
        <tbody id="taxrates-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.taxrates.table.ajax')
            <!--ajax content here-->
        </tbody>
        <ttaxratet>
            <tr>
                <td colspan="20">
                    <!--load more button-->
                    @include('misc.load-more-button')
                    <!--load more button-->
                </td>
            </tr>
        </ttaxratet>
    </table>
    @endif
    @if (@count($taxrates) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif

    
    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/tax-settings/"  target="_blank" class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
    

</div>