<!-- action buttons -->
@include('pages.proposals.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="proposals-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($proposals) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--proposals table-->
<div class="card-embed-fix">
@include('pages.proposals.components.table.wrapper')
</div>
<!--proposals table-->