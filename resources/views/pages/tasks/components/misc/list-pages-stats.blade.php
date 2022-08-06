<!--WIDGET NOTES: stats displayed on top of result tables and list pages-->
<div class="card-group table-stats-cards  {{ runtimePreferenceStatsPanelPosition(auth()->user()->stats_panel_position) }}" id="list-pages-stats-widget">
    
    @foreach($stats as $stat)
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h3 id="stats-widget-value-1">{{ $stat['value'] ?? '' }}</h3>
                    <h6 class="card-subtitle" id="stats-widget-title-1">{{ $stat['title'] ?? '' }}</h6>
                </div>

            </div>
            <div class="progress dynamic-list">
                <div class="progress-bar {{ $stat['color'] ?? '' }} h-px-4 w-100" id="stats-widget-percentage-1"
                    role="progressbar"
                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
    @endforeach

</div>