<div class="payment-gateways" id="gateway-mollie">
    <!--MOLLIE BUTTONS-->
    <div class="x-button">
        <button class="btn btn-danger js-ajax-request disable-on-click-loading"
            data-url="{{ url('invoices/'.$bill->bill_invoiceid.'/mollie-payment') }}" id="gateway-button-paypal">
            {{ cleanLang(__('lang.pay_now')) }} -
            {{ config('system.settings_mollie_display_name') }}</button>
    </div>
</div>