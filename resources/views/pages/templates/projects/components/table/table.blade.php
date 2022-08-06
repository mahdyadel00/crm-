<div class="card count-{{ @count($projects) }}" id="projects-view-wrapper">
    <div class="card-body">
        <div class="table-responsive">
            @if (@count($projects) > 0)
            <table class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        <th class="projects_col_title"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_project_title" href="javascript:void(0)"
                                data-url="{{ urlResource('/templates/projects?action=sort&orderby=project_title&sortorder=asc') }}">{{ cleanLang(__('lang.title')) }}</a>
                        </th>
                        <th class="projects_col_date"><a href="javascript:void(0)">{{ cleanLang(__('lang.date_created')) }}</a>
                        </th>
                        <th class="projects_col_category"><a class="js-ajax-ux-request js-list-sorting"
                            id="sort_category" href="javascript:void(0)"
                            data-url="{{ urlResource('/templates/projects?action=sort&orderby=category&sortorder=asc') }}">{{ cleanLang(__('lang.category')) }}</a>
                    </th>
                        <th class="projects_col_createby"><a
                                href="javascript:void(0)">{{ cleanLang(__('lang.created_by')) }}</a></th>
                        <th class="projects_col_action w-px-99"><a
                                href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="projects-td-container">
                    <!--ajax content here-->
                    @include('pages.templates.projects.components.table.ajax')
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
            @endif @if (@count($projects) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>