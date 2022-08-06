<div class="payment-gateways" id="gateway-paypal">
    <!--PAYPAL-->
    <div class="x-button">
        @if(config('system.settings_paypal_mode') =='sandbox')
        <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" id="paypal-onetime"
            name="paypal-form" target="_top">
            @else
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal-onetime" name="paypal-form"
                target="_top">
                @endif
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="{{ config('system.settings_paypal_email') }}">
                <input type="hidden" name="item_name" value="{{ $paypal['item_name'] }}">
                <input type="hidden" name="item_number" value="{{ $paypal['session_id'] }}">
                <input type="hidden" name="image_url" value="{{ runtimeLogoLarge() }}">
                <input type="hidden" name="amount" value="{{ $paypal['amount'] }}">
                <input type="hidden" name="no_shipping" value="1">
                <input type="hidden" name="no_note" value="1">
                <input type="hidden" name="currency_code" value="{{ $paypal['currency'] }}">
                <input type="hidden" name="bn" value="FC-BuyNow">
                <input type="hidden" name="rm" value="2">
                <input type="hidden" name="custom" value="">
                <input type="hidden" name="notify_url" value="{{ $paypal['ipn_url'] }}">
                <input type="hidden" name="return" value="{{ $paypal['thank_you_url'] }}">
                <input type="hidden" name="cancel_return" value="{{ $paypal['cancel_url'] }}">
                <button class="btn btn-danger" id="gateway-button-paypal" type="submit"> {{ cleanLang(__('lang.pay_now')) }} -
                    {{ config('system.settings_paypal_display_name') }}</button>
            </form>

    </div>
</div>