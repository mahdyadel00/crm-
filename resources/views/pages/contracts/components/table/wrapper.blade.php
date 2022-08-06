<!--bulk actions-->
@include('pages.contracts.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.contracts.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.contracts.components.misc.filter-contracts')
@endif
<!--filter-->