<div class="card count-{{ @count($proposals) }}" id="proposals-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($proposals) > 0)
            <table id="proposals-list-table" class="table m-t-0 m-b-0 table-hover no-wrap proposal-list"
                data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.proposals_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-proposals" name="listcheckbox-proposals"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="proposals-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-proposals">
                                <label for="listcheckbox-proposals"></label>
                            </span>
                        </th>
                        @endif

                        <!--doc_id-->
                        <th class="col_doc_id"><a class="js-ajax-ux-request js-list-sorting" id="sort_doc_id"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/proposals?action=sort&orderby=doc_id&sortorder=asc') }}">@lang('lang.id')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>


                        <!--doc_date_start-->
                        <th class="col_doc_date_start"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_doc_date_start" href="javascript:void(0)"
                                data-url="{{ urlResource('/proposals?action=sort&orderby=doc_date_start&sortorder=asc') }}">@lang('lang.date')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>


                        <!--client-->
                        @if(config('visibility.col_client'))
                        <th class="col_client"><a class="js-ajax-ux-request js-list-sorting" id="sort_client"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/proposals?action=sort&orderby=client&sortorder=asc') }}">@lang('lang.proposed_to')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @endif


                        <!--doc_title-->
                        <th class="col_doc_title"><a class="js-ajax-ux-request js-list-sorting" id="sort_doc_title"
                            href="javascript:void(0)"
                            data-url="{{ urlResource('/proposals?action=sort&orderby=doc_title&sortorder=asc') }}">@lang('lang.title')<span
                                class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                                
                        <!--value-->
                        <th class="col_value"><a class="js-ajax-ux-request js-list-sorting" id="sort_value"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/proposals?action=sort&orderby=value&sortorder=asc') }}">@lang('lang.value')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--created by-->
                        @if(config('visibility.col_created_by'))
                        <th class="col_created_by"><a class="js-ajax-ux-request js-list-sorting" id="sort_created_by"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/proposals?action=sort&orderby=created_by&sortorder=asc') }}">@lang('lang.created_by')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @endif

                        <!--doc_date_end-->
                        <th class="col_doc_date_end"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_doc_date_end" href="javascript:void(0)"
                                data-url="{{ urlResource('/proposals?action=sort&orderby=doc_date_end&sortorder=asc') }}">@lang('lang.valid_until')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>


                        <!--status-->
                        <th class="col_doc_status"><a class="js-ajax-ux-request js-list-sorting" id="sort_doc_status"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/foos?action=sort&orderby=doc_status&sortorder=asc') }}">@lang('lang.status')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--actions-->
                        @if(config('visibility.proposals_col_action'))
                        <th class="proposals_col_action"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="proposals-td-container">
                    <!--ajax content here-->
                    @include('pages.proposals.components.table.ajax')
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
            @endif @if (@count($proposals) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>