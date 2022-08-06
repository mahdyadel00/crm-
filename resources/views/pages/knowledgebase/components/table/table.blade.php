<div class="card count-{{ @count($knowledgebase) }}">
    <div class="card-body">
        <div class="table-responsive min-h-250">
            @if (@count($knowledgebase) > 0)
            <table id="demo-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap knowledgebase" data-page-size="10">
                <tbody id="knowledgebase-td-container">
                    <!--ajax content here-->
                    @include('pages.knowledgebase.components.table.ajax')
                    <!--ajax content here-->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--load more button-->
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif @if (@count($knowledgebase) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>