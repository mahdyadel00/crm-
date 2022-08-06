<div id="gateway-stripe">
    <!--BUTTONS-->
    <div class="x-button">
        <button class="btn btn-danger disable-on-click-loading" id="invoice-stripe-payment-button">
            {{ cleanLang(__('lang.pay_now')) }} </button>
    </div>
    <!--STRIPE REDIRECT-->
    <!--section js resource-->
    <span class="hidden" id="js-pay-stripe" data-key="{{ config('system.settings_stripe_public_key') }}"
        data-session="{{ $session_id }}">placeholder</span>
</div>