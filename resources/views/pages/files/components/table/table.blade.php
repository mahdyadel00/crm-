<div class="card count-{{ @count($files) }}" id="files-table-wrapper">

    <div class="card-body">
        <div class="table-responsive">
            @if (@count($files) > 0)
            <table id="file-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.files_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-files" name="listcheckbox-files"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="files-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-files">
                                <label for="listcheckbox-files"></label>
                            </span>
                        </th>
                        @endif
                        <th class="files_col_file">{{ cleanLang(__('lang.preview')) }}</th>
                        <th class="files_col_file_name"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_file_filename" href="javascript:void(0)"
                                data-url="{{ urlResource('/files?action=sort&orderby=file_filename&sortorder=asc') }}">{{ cleanLang(__('lang.file_name')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="files_col_added_by"><a class="js-ajax-ux-request js-list-sorting" id="sort_added_by"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/files?action=sort&orderby=added_by&sortorder=asc') }}">{{ cleanLang(__('lang.uploaded_by')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="files_col_size"><a class="js-ajax-ux-request js-list-sorting" id="sort_file_size"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/files?action=sort&orderby=file_size&sortorder=asc') }}">{{ cleanLang(__('lang.size')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="files_col_date"><a class="js-ajax-ux-request js-list-sorting" id="sort_file_created"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/files?action=sort&orderby=file_created&sortorder=asc') }}">{{ cleanLang(__('lang.date')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.files_col_visibility'))
                        <th class="files_col_visible_to_client"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_visibility" href="javascript:void(0)"
                                data-url="{{ urlResource('/files?action=sort&orderby=visibility&sortorder=asc') }}">{{ cleanLang(__('lang.client_visibility')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @endif
                        <th class="files_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="files-td-container">
                    <!--ajax content here-->
                    @include('pages.files.components.table.ajax')
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
            @endif @if (@count($files) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>