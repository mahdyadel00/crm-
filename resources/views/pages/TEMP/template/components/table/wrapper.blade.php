<!--main table view-->
@include('pages.foos.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.foos.components.misc.filter')
@endif
<!--filter-->