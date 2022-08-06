<!-- action buttons -->
@include('pages.items.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="items-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($items) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--items table-->
<div class="card-embed-fix">
@include('pages.items.components.table.wrapper')
</div>
<!--items table-->