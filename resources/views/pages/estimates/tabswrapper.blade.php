<!-- action buttons -->
@include('pages.estimates.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
<div id="estimates-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($estimates) > 0) @include('misc.list-pages-stats') @endif
</div>
<!--stats panel-->

<!--estimates table-->
<div class="card-embed-fix">
@include('pages.estimates.components.table.wrapper')
</div>
<!--estimates table-->