<!-- action buttons -->
@include('pages.expenses.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="expenses-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($expenses) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--expenses table-->
<div class="card-embed-fix">
@include('pages.expenses.components.table.wrapper')
</div>
<!--expenses table-->