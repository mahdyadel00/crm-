<!-- action buttons -->
@include('pages.projects.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
<div id="projects-stats-wrapper" class="stats-wrapper card-embed-fix">
@if (@count($projects) > 0 && auth()->user()->is_team) @include('misc.list-pages-stats') @endif
</div>
<!--stats panel-->

<!--projects cards grid-->
<div class="card-embed-fix">
    @include('pages.projects.views.cards.layout.wrapper')
</div>
<!--projects cards grid-->
