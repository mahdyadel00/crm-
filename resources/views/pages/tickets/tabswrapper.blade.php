<!-- action buttons -->
@include('pages.tickets.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="tickets-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($tickets) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--tickets table-->
<div class="card-embed-fix">
@include('pages.tickets.components.table.wrapper')
</div>
<!--tickets table-->