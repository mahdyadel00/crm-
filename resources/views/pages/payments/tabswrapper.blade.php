<!-- action buttons -->
@include('pages.payments.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="payments-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($payments) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--payments table-->
<div class="card-embed-fix">
@include('pages.payments.components.table.wrapper')
</div>
<!--payments table-->