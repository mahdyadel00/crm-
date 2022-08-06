    <div class="subscription-pay-now">

        <div class="x-splash">
            <img src="{{ url('/') }}/public/images/credit-card.svg" alt="pay" />
        </div>

        <div class="x-plan">
            {{ $subscription->subscription_gateway_product_name }}
        </div>

        <div class="x-price">
            {{ runtimeMoneyFormat($subscription->subscription_final_amount) }}/{{ $interval}}
        </div>


        <!--BUTTONS-->
        <div class="x-button">
            <button class="btn btn-danger disable-on-click-loading" id="invoice-stripe-payment-button">
                {{ cleanLang(__('lang.pay_now')) }} </button>
        </div>

        <div class="x-cards">
            <img src="{{ url('/') }}/public/images/credit-cards.png" alt="pay" />
        </div>

        <!--STRIPE REDIRECT-->
        <!--section js resource-->
        <span class="hidden" id="js-pay-stripe" data-key="{{ config('system.settings_stripe_public_key') }}"
            data-session="{{ $session_id }}">placeholder</span>
    </div>