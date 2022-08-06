<div class="row">
    <div class="col-lg-12">

        <!--meta data - creatd by-->
        @if(config('settings.visibiliy_payment_modal_meta'))
        <div class="modal-meta-data">
            <small><strong>@lang('lang.balance_due'):</strong> {{ runtimeMoneyFormat($invoice->invoice_balance) }}</small>
        </div>
        @endif

        <!--invoice id-->
        @if(config('settings.visibiliy_payment_modal_invoice_field'))
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.invoice_id')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon" id="basic-addon1">{{ config('system.settings_invoices_prefix') }}</span>
                    <input type="number" name="payment_invoiceid" id="payment_invoiceid"
                        class="form-control  form-control-sm" placeholder="" aria-describedby="basic-addon1">
                </div>
            </div>
        </div>
        @endif

        <!--amount-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.amount')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon" id="basic-addon2">{{ config('system.settings_system_currency_symbol') }}</span>
                    <input type="number" name="payment_amount" id="payment_amount" class="form-control form-control-sm"
                        value="{{ $invoice->invoice_balance ?? '' }}" aria-describedby="basic-addon2">
                </div>
            </div>
        </div>

        <!--date-->
        <div class="form-group row">
            <label for="due_date"
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" name="payment_date" class="form-control form-control-sm pickadate" autocomplete="off"
                    value="{{ runtimeTodaysDate() }}">
                <input class="mysql-date" type="hidden" name="payment_date" id="payment_date"
                    value="{{ runtimeTodaysDateMySQL() }}">
            </div>
        </div>

        <!--gateway-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.payment_method')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select id="payment_gateway" name="payment_gateway" class="select2-basic form-control-sm">
                    @foreach(config('system.gateways') as $gateway)
                    <option value="{{ $gateway }}">{{ $gateway }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!--transaction id-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.transaction_id')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" name="payment_transaction_id" class="form-control form-control-sm"
                    id="payment_transaction_id" autocomplete="off" placeholder="">
            </div>
        </div>

        <!--additional information toggle-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.additional_information')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="show_more_settings_payments" id="show_more_settings_payments"
                            class="js-switch-toggle-hidden-content" data-target="add_payment_additional_settings">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>

        <!--notes-->
        <div id="add_payment_additional_settings" class="hidden">
            <div class="form-group row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.notes')) }}**</label>
                <div class="col-sm-12 col-lg-9">
                    <textarea class="form-control" name="payment_notes" id="payment_notes" rows="5"></textarea>
                    <div><small>** {{ cleanLang(__('lang.private')) }} ({{ cleanLang(__('lang.not_visible_to_the_client')) }})</small></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="ref" value="{{ request('ref') }}">


        <!--send mayment notification-->
        <div class="form-group form-group-checkbox row">
            <div class="col-12 text-left p-t-5">
                <input type="checkbox" id="send_payment_email" name="send_payment_email"
                    class="filled-in chk-col-light-blue" checked="checked">
                <label for="send_payment_email">{{ cleanLang(__('lang.send_customer_payment_email')) }}</label>
            </div>
        </div>


        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>