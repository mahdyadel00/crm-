<div class="col-lg-6  col-md-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ cleanLang(__('lang.latest_activity')) }}</h5>
            <div class="dashboard-events profiletimeline" id="dashboard-client-events">
                @php $events = $payload['all_events'] ; @endphp
                @include('pages.timeline.components.misc.ajax')
            </div>
            <!--load more-->
        </div>
    </div>
</div>