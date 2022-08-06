<div class="table-responsive" id="milestone-categories-table">
    @if (@count($milestones) > 0)
    <table id="milestone-stages" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10"
        data-type="form" data-form-id="milestone-stages" data-ajax-type="post"
        data-url="{{ url('settings/milestones/update-positions') }}">
        <thead>
            <tr>
                <th class="milestones_col_name">{{ cleanLang(__('lang.name')) }}</th>
                <th class="milestones_col_date">{{ cleanLang(__('lang.date_created')) }}</th>
                <th class="milestones_col_created_by">{{ cleanLang(__('lang.created_by')) }}</th>
                <th class="milestones_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
            </tr>
        </thead>
        <tbody id="milestones-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.milestones.table.ajax')
            <!--ajax content here-->
        </tbody>
    </table>
    @endif
    @if (@count($milestones) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif

    <div class="m-t-40">
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/milestone-settings/" target="_blank"
            class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
</div>