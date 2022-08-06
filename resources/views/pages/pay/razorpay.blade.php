<div id="gateway-stripe">
    <!--BUTTONS-->
    <div class="x-button">
        <button class="btn btn-danger disable-on-click-loading" id="invoice-razorpay-payment-button"
            data-amount="{{ $payload['amount'] }}" data-key-id="{{ $payload['key'] }}"
            data-currency="{{ $payload['currency'] }}" data-company-name="{{ $payload['company_name'] }}"
            data-description="{{ $payload['description'] }}" data-image="{{ $payload['image'] }}"
            data-thankyou-url="{{ $payload['thankyou_url'] }}" data-client-name="{{ $payload['client_name'] }}"
            data-client-email="{{ $payload['client_email'] }}" data-order-id="{{ $payload['order_id'] }}">
            {{ cleanLang(__('lang.pay_now')) }} </button>
    </div>
    <!--STRIPE REDIRECT-->
</div>