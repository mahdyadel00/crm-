<div class="subscription-summary">

    <div class="subscription-header">
        <div class="x-plan">{{ $subscription->subscription_gateway_product_name ?? '---' }}</div>
        <div class="x-cycle">{{ runtimeMoneyFormat($subscription->subscription_final_amount) }}/{{ $interval}}</div>
    </div>
    <div class="subscription-body card">

        @if(config('visibility.client_name'))
        <div class="x-each-item">
            <small class="text-muted">@lang('lang.client')</small>
            <div class="x-content">
                <h6>{{ $subscription->client_company_name ?? '---' }}</h6>
            </div>
        </div>
        @endif

        @if(config('visibility.stripe_id'))
        <div class="x-each-item">
            <small class="text-muted">@lang('lang.stripe_id')</small>
            <div class="x-content">
                <h6>{{ $subscription->subscription_gateway_id ?? '---' }}</h6>
            </div>
        </div>
        @endif

        <div class="x-each-item">
            <small class="text-muted">@lang('lang.start_date')</small>
            <div class="x-content">
                <h6>{{ runtimeDate($subscription->subscription_date_started) }}</h6>
            </div>
        </div>


        <div class="x-each-item">
            <small class="text-muted">@lang('lang.last_payment')</small>
            <div class="x-content">
                <h6>{{ runtimeDate($subscription->subscription_date_renewed) }}</h6>
            </div>
        </div>


        <div class="x-each-item">
            <small class="text-muted">@lang('lang.next_payment')</small>
            <div class="x-content">
                <h6>{{ runtimeDate($subscription->subscription_date_next_renewal) }}</h6>
            </div>
        </div>

        <div class="x-each-item">
            <small class="text-muted">@lang('lang.status')</small>
            <div class="x-content">
                <span class="label label-lg {{ runtimeSubscriptionsColors($subscription->subscription_status, 'label') }}">{{
                    runtimeLang($subscription->subscription_status) }}</span>
            </div>
        </div>

    </div>

</div>