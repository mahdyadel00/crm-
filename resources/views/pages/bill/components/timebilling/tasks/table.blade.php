<div class="card count-{{ @count($tasks) }}" id="tasks-table-wrapper">
    <div class="card-body p-t-0">
        <div class="table-responsive list-table-wrapper">
            @if (@count($tasks) > 0)
            <!--billing cycles information-->
            <div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span> </button>
                <div><i class="sl-icon-info text-info"></i> {{ cleanLang(__('lang.billable_hours_info')) }}</div>
            </div>
            <table id="tasks-list-table" class="table m-t-0 m-b-0 table-hover no-wrap tasks-list" data-page-size="10">
                <thead>
                    <tr>
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes" id="fx-timebilling-task-list">
                                <input type="checkbox" id="listcheckbox-tasks" name="listcheckbox-tasks"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="tasks-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-tasks">
                                <label for="listcheckbox-tasks"></label>
                            </span>
                        </th>
                        <th class="tasks_col_title"><a class="js-ajax-ux-request js-list-sorting" id="sort_task_title"
                                href="javascript:void(0)"
                                data-url="{{ url('/invoices/timebilling/'.$project_id.'?grouping=tasks&action=sort&orderby=task_title&sortorder=asc') }}">{{ cleanLang(__('lang.task')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="tasks_col_time">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_time" href="javascript:void(0)"
                                data-url="{{ url('/invoices/timebilling/'.$project_id.'?grouping=tasks&action=sort&orderby=time&sortorder=asc') }}">{{ cleanLang(__('lang.billable_time')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                    </tr>
                </thead>
                <tbody id="tasks-td-container">
                    <!--ajax content here-->
                    @include('pages.bill.components.timebilling.tasks.ajax')
                    <!--ajax content here-->
                </tbody>
            </table>
            @endif @if (@count($tasks) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>