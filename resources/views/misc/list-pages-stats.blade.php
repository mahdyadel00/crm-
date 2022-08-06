<!--WIDGET NOTES: stats displayed on top of result tables and list pages-->
<div class="card-group table-stats-cards  {{ runtimePreferenceStatsPanelPosition(auth()->user()->stats_panel_position) }}" id="list-pages-stats-widget">
    @include('misc.list-pages-stats-content')
</div>