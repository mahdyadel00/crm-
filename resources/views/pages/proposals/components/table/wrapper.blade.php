<!--bulk actions-->
@include('pages.proposals.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.proposals.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.proposals.components.misc.filter-proposals')
@endif
<!--filter-->