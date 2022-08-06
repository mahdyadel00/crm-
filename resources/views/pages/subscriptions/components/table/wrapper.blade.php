<!--main table view-->
@include('pages.subscriptions.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.subscriptions.components.misc.filter')
@endif
<!--filter-->