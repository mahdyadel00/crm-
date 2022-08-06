<!--bulk actions-->
@include('pages.timesheets.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.timesheets.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.timesheets.components.misc.filter-timesheets')
@endif
<!--timesheets-->