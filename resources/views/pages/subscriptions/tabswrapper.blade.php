<!-- action buttons -->
@include('pages.subscriptions.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="subscriptions-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($subscriptions) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--subscriptions table-->
<div class="card-embed-fix">
@include('pages.subscriptions.components.table.wrapper')
</div>
<!--subscriptions table-->