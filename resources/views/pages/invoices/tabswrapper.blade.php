<!-- action buttons -->
@include('pages.invoices.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="invoices-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($invoices) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--invoices table-->
<div class="card-embed-fix">
@include('pages.invoices.components.table.wrapper')
</div>
<!--invoices table-->