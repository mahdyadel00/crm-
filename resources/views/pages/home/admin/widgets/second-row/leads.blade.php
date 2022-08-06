    <!-- Column -->
    <div class="col-lg-4 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex m-b-30 no-block">
                    <h5 class="card-title m-b-0 align-self-center">{{ cleanLang(__('lang.leads')) }}</h5>
                    <div class="ml-auto">
                        {{ cleanLang(__('lang.this_year')) }}
                    </div>
                </div>
                <div id="leadsWidget"></div>
                <ul class="list-inline m-t-30 text-center font-12">
                    @foreach(config('home.lead_statuses') as $lead_status)
                    <li class="p-b-10"><span class="label {{ $lead_status['label'] }} label-rounded"><i class="fa fa-circle {{ $lead_status['color'] }}"></i> {{ $lead_status['title'] }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!--[DYNAMIC INLINE SCRIPT]  Backend Variables to Javascript Variables-->
    <script>
        NX.admin_home_c3_leads_data = JSON.parse('{!! clean($payload["leads_stats"]) !!}', true);
        NX.admin_home_c3_leads_colors = JSON.parse('{!! clean($payload["leads_key_colors"]) !!}', true);
        NX.admin_home_c3_leads_title = "{{ $payload['leads_chart_center_title'] }}";
    </script>