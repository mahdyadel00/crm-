<div class="card count-{{ @count($notes) }}" id="notes-table-wrapper">
    <div class="card-body">
        <div class="table-responsive">
            @if (@count($notes) > 0)
            <table id="note-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.notes_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-notes" name="listcheckbox-notes"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="notes-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-notes">
                                <label for="listcheckbox-notes"></label>
                            </span>
                        </th>
                        @endif
                        <th class="notes_col_added">{{ cleanLang(__('lang.added_by')) }}</th>
                        <th class="notes_col_title">{{ cleanLang(__('lang.title')) }}</th>
                        <th class="notes_col_tags">{{ cleanLang(__('lang.tags')) }}</th>
                        <th class="notes_col_date">{{ cleanLang(__('lang.date')) }}</th>
                        <th class="notes_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="notes-td-container">
                    <!--ajax content here-->
                    @include('pages.notes.components.table.ajax')
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
            @endif @if (@count($notes) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>