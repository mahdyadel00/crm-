<!--bulk actions-->
@include('pages.items.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.items.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.items.components.misc.filter-items')
@endif
<!--filter-->