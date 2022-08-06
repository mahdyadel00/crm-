<!-- action buttons -->
@include('pages.leads.components.misc.list-page-actions')
<!-- action buttons -->

<!--stats panel-->
@if(auth()->user()->is_team)
<div id="leads-stats-wrapper" class="stats-wrapper card-embed-fix">
    @if (@count($leads) > 0) @include('misc.list-pages-stats') @endif
</div>
@endif
<!--stats panel-->

@if(auth()->user()->pref_view_leads_layout =='list')
<div class="card-embed-fix  kanban-wrapper">
    @include('pages.leads.components.table.wrapper')
</div>
@else
<div class="card-embed-fix  kanban-wrapper">
    @include('pages.leads.components.kanban.wrapper')
</div>
@endif


<!--filter-->
@if(auth()->user()->is_team)
@include('pages.leads.components.misc.filter-leads')
@endif
<!--filter-->