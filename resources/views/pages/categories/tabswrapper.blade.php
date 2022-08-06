<!-- action buttons -->
@include('pages.categories.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="categories-stats-wrapper" class="stats-wrapper card-embed-fix">
    @if (@count($categories) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--categories table-->
<div class="card-embed-fix">
    @include('pages.categories.components.table.wrapper')
</div>
<!--categories table-->