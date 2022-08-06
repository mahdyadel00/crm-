<div class="row">
    <div class="col-lg-12">
        <div class="p-b-30">

            <table class="table table-bordered payment-details">
                <tbody>
                    <tr>
                        <td>{{ cleanLang(__('lang.payment_id')) }}</td>
                        <td>#{{ $payment->payment_id }}</td>
                    </tr>
                    <tr class="font-16 font-weight-600">
                            <td>{{ cleanLang(__('lang.amount')) }}</td>
                            <td>
                                {{ runtimeMoneyFormat($payment->payment_amount) }}</td>
                            </td>
                        </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.invoice_id')) }}</td>
                        <td> {{ runtimeInvoiceIdFormat($payment->payment_invoiceid) }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.date')) }}</td>
                        <td>{{ runtimeDate($payment->payment_date) }}</td>
                    </tr>

                    <tr>
                        <td>{{ cleanLang(__('lang.payment_method')) }}</td>
                        <td>{{ $payment->payment_gateway }}</td>
                    </tr>
                    @if(auth()->user()->is_team)
                    <tr>
                        <td>{{ cleanLang(__('lang.client')) }}</td>
                        <td>{{ $payment->client_company_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>{{ cleanLang(__('lang.project')) }}</td>
                        <td>{{ $payment->project_title }}</td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.notes')) }}</td>
                        <td>{{ $payment->payment_notes }}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>