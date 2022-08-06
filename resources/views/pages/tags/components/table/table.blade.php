<div class="card count-{{ @count($tags) }}" id="tags-table-wrapper">
    <div class="card-body">
        <div class="table-responsive">
            @if (@count($tags) > 0)
            <table id="tag-foo-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        <th class="tags_col_date">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_tag_created"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/tags?action=sort&orderby=tag_created&sortorder=asc') }}">{{ cleanLang(__('lang.date_created')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <th class="tags_col_title">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_tag_title" href="javascript:void(0)"
                                data-url="{{ urlResource('/tags?action=sort&orderby=tag_title&sortorder=asc') }}">{{ cleanLang(__('lang.title')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>

                        <th class="tags_col_creator">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_created_by" href="javascript:void(0)"
                                data-url="{{ urlResource('/tags?action=sort&orderby=created_by&sortorder=asc') }}">{{ cleanLang(__('lang.created_by')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <th class="tags_col_resourcetype">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_tagresource_type"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/tags?action=sort&orderby=tagresource_type&sortorder=asc') }}">{{ cleanLang(__('lang.resource_type')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <th class="tags_col_resourceid">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_tagresource_id"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/tags?action=sort&orderby=tagresource_id&sortorder=asc') }}">{{ cleanLang(__('lang.resource_id')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="tags_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="tags-td-container">
                    <!--ajax content here-->
                    @include('pages.tags.components.table.ajax')
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
            @endif @if (@count($tags) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
            <div>
                <!--settings documentation help-->
                <a href="https://growcrm.io/documentation/tag-settings/"  target="_blank" class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
            </div>
        </div>
    </div>
</div>