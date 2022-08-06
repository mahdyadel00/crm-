<div class="card count-{{ @count($milestones) }}" id="milestones-table-wrapper">
    <div class="card-body">
        <div class="table-responsive">
            @if (@count($milestones) > 0)
            <table id="milestones-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10"
                data-type="form" data-form-id="milestones-table-wrapper" data-ajax-type="post"
                data-url="{{ url('milestones/update-positions') }}">

                <thead>
                    <tr>
                        <th class="milestones_col_name"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_milestone_title" href="javascript:void(0)"
                                data-url="{{ urlResource('/milestones?action=sort&orderby=milestone_title&sortorder=asc') }}">{{ cleanLang(__('lang.name')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="milestones_col_tasks w-20"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_total_tasks" href="javascript:void(0)"
                                data-url="{{ urlResource('/milestones?action=sort&orderby=total_tasks&sortorder=asc') }}">{{ cleanLang(__('lang.all_tasks')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="milestones_col_tasks_pending w-20"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_pending_tasks" href="javascript:void(0)"
                                data-url="{{ urlResource('/milestones?action=sort&orderby=pending_tasks&sortorder=asc') }}">{{ cleanLang(__('lang.pending_tasks')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="milestones_col_tasks_completed w-20"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_completed_tasks" href="javascript:void(0)"
                                data-url="{{ urlResource('/milestones?action=sort&orderby=completed_tasks&sortorder=asc') }}">{{ cleanLang(__('lang.completed_tasks')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        @if(config('visibility.milestone_actions'))
                        <th class="milestones_col_action w-5"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="milestones-td-container">
                    <!--ajax content here-->
                    @include('pages.milestones.components.table.ajax')
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
            @endif @if (@count($milestones) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>