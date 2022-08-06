<div class="table-responsive p-b-30">
    @if (@count($statuses) > 0)
    <table id="task-stages" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10"
        data-type="form" data-form-id="task-stages" data-ajax-type="post"
        data-url="{{ url('settings/tasks/update-stage-positions') }}">
        <thead>
            <tr>
                <th class="status_col_name">{{ cleanLang(__('lang.name')) }}</th>
                <th class="status_col_count">{{ cleanLang(__('lang.tasks')) }}</th>
                <th class="status_col_color">{{ cleanLang(__('lang.color')) }}</th>
                <th class="status_col_created_by">{{ cleanLang(__('lang.created_by')) }}</th>
                <th class="status_col_action w-px-110"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
            </tr>
        </thead>
        <tbody id="status-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.tasks.table.ajax')
            <!--ajax content here-->
        </tbody>
    </table>
    @endif
    @if (@count($statuses) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
</div>