<!--main table view-->
@include('pages.tickets.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.tickets.components.misc.filter-tickets')
@endif
<!--filter-->