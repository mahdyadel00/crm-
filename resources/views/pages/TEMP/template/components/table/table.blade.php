<div class="card count-{{ @count($foos) }}" id="foo-table-wrapper">
    <div class="card-body">
        <div class="table-responsive">
            @if (@count($foos) > 0)
            <table id="foo-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.foos_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-foos" name="listcheckbox-foos"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="foos-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-foos">
                                <label for="listcheckbox-foos"></label>
                            </span>
                        </th>
                        @endif

                        <!--actions-->
                        <th class="col_foos_actions"><a href="javascript:void(0)">@lang('lang.actions')</a></th>
                    </tr>
                </thead>
                <tbody id="foo-td-container">
                    <!--ajax content here-->
                    @include('pages.foos.components.table.ajax')
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
            @endif @if (@count($foos) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>