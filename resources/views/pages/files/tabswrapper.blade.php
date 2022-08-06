<!-- action buttons -->
@include('pages.files.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="files-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($files) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

<!--files table-->
<div class="card-embed-fix">
@include('pages.files.components.table.wrapper')
</div>
<!--files table-->