@foreach($payments as $payment)
<!--each row-->
<tr id="payment_{{ $payment->payment_id }}">
    @if(config('visibility.payments_col_checkboxes'))
    <td class="payments_col_checkbox checkitem" id="payments_col_checkbox_{{ $payment->payment_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-payments-{{ $payment->payment_id }}"
                name="ids[{{ $payment->payment_id }}]"
                class="listcheckbox listcheckbox-payments filled-in chk-col-light-blue"
                data-actions-container-class="payments-checkbox-actions-container">
            <label for="listcheckbox-payments-{{ $payment->payment_id }}"></label>
        </span>
    </td>
    @endif
    @if(config('visibility.payments_col_id'))
    <td class="payments_col_id" id="payments_col_id_{{ $payment->payment_id }}"><a href="javascript:void(0)"
            class="show-modal-button js-ajax-ux-request" data-toggle="modal"
            data-url="{{ url( '/') }}/payments/{{  $payment->payment_id }} " data-target="#plainModal"
            data-loading-target="plainModalBody" data-modal-title="">#{{ $payment->payment_id }}</a></td>
    @endif
    <td class="payments_col_date" id="payments_col_date_{{ $payment->payment_id }}">
        {{ runtimeDate($payment->payment_date) }}
    </td>
    @if(config('visibility.payments_col_invoiceid'))
    <td class="payments_col_bill_invoiceid" id="payments_col_bill_invoiceid_{{ $payment->payment_id }}">
        <a href="/invoices/{{ $payment->payment_invoiceid }}">{{ runtimeInvoiceIdFormat($payment->bill_invoiceid) }}</a>
    </td>
    @endif
    <td class="payments_col_amount" id="payments_col_amount_{{ $payment->payment_id }}">
        {{ runtimeMoneyFormat($payment->payment_amount) }}</td>
    @if(config('visibility.payments_col_client'))
    <td class="payments_col_client" id="payments_col_client_{{ $payment->payment_id }}">
        <a
            href="/clients/{{ $payment->payment_clientid }}">{{ str_limit($payment->client_company_name ?? '---', 20) }}</a>
    </td>
    @endif
    @if(config('visibility.payments_col_project'))
    <td class="payments_col_project" id="payments_col_project_{{ $payment->payment_id }}">
        <a href="/projects/{{ $payment->payment_projectid }}">{{ str_limit($payment->project_title ?? '---', 25) }}</a>
    </td>
    @endif
    <td class="payments_col_transaction hidden" id="payments_col_transaction_{{ $payment->payment_id }}">
        {{ str_limit($payment->payment_transaction_id ?? '---', 20) }}</td>
    @if(config('visibility.payments_col_method'))
    <td class="payments_col_method text-ucf" id="payments_col_method_{{ $payment->payment_id }}">
        {{ $payment->payment_gateway }}
    </td>
    @endif
    @if(config('visibility.payments_col_action'))
    <td class="payments_col_action actions_column" id="payments_col_action_{{ $payment->payment_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/payments/'.$payment->payment_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_payment')) }}"
                data-action-url="{{ urlResource('/payments/'.$payment->payment_id.'?ref=list') }}"
                data-action-method="PUT" data-action-ajax-class=""
                data-action-ajax-loading-target="payments-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @endif
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_payment')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/payments/{{ $payment->payment_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <a href="javascript:void(0)" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm show-modal-button js-ajax-ux-request"
                data-toggle="modal" data-url="{{ url( '/') }}/payments/{{  $payment->payment_id }} "
                data-target="#plainModal" data-loading-target="plainModalBody" data-modal-title="">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->
    </td>
    @endif
</tr>
<!--each row-->
@endforeach