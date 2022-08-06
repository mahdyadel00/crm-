<!-- action buttons -->
@include('pages.milestones.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="milestones-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($milestones) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--milestones table-->
<div class="card-embed-fix">
@include('pages.milestones.components.table.wrapper')
</div>
<!--milestones table-->