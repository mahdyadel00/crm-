<!--main table view-->
@include('pages.clients.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.clients.components.misc.filter-clients')
@endif
<!--filter-->