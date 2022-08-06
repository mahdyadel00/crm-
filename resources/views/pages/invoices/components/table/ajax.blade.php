@foreach($invoices as $invoice)
<!--each row-->
<tr id="invoice_{{ $invoice->bill_invoiceid  }}">
    @if(config('visibility.invoices_col_checkboxes'))
    <td class="invoices_col_checkbox checkitem" id="invoices_col_checkbox_{{ $invoice->bill_invoiceid }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-invoices-{{ $invoice->bill_invoiceid }}"
                name="ids[{{ $invoice->bill_invoiceid }}]"
                class="listcheckbox listcheckbox-invoices filled-in chk-col-light-blue"
                data-actions-container-class="invoices-checkbox-actions-container">
            <label for="listcheckbox-invoices-{{ $invoice->bill_invoiceid }}"></label>
        </span>
    </td>
    @endif
    <td class="invoices_col_id" id="invoices_col_id_{{ $invoice->bill_invoiceid }}">
        <a href="/invoices/{{ $invoice->bill_invoiceid }}">
            {{ $invoice->formatted_bill_invoiceid }} </a>
        <!--recurring-->
        @if(auth()->user()->is_team && $invoice->bill_recurring == 'yes')
        <span class="sl-icon-refresh text-danger p-l-5" data-toggle="tooltip" title="@lang('lang.recurring_invoice')"></span>
        @endif
        <!--child invoice-->
        @if(auth()->user()->is_team && $invoice->bill_recurring_child == 'yes')
        <a href="{{ url('invoices/'.$invoice->bill_recurring_parent_id) }}">
            <i class="ti-back-right text-success p-l-5" data-toggle="tooltip" data-html="true"
                title="{{ cleanLang(__('lang.invoice_automatically_created_from_recurring')) }} <br>(#{{ runtimeInvoiceIdFormat($invoice->bill_recurring_parent_id) }})"></i>
        </a>
        @endif
    </td>
    <td class="invoices_col_date" id="invoices_col_date_{{ $invoice->bill_invoiceid }}">
        {{ runtimeDate($invoice->bill_date) }}
    </td>
    @if(config('visibility.invoices_col_client'))
    <td class="invoices_col_company" id="invoices_col_company_{{ $invoice->bill_invoiceid }}">
        <a href="/clients/{{ $invoice->bill_clientid }}">{{ str_limit($invoice->client_company_name ?? '---', 12) }}</a>
    </td>
    @endif
    @if(config('visibility.invoices_col_project'))
    <td class="invoices_col_project" id="invoices_col_project_{{ $invoice->bill_invoiceid }}">
        <a href="/projects/{{ $invoice->bill_projectid }}">{{ str_limit($invoice->project_title ?? '---', 12) }}</a>
    </td>
    @endif

    <td class="invoices_col_amount" id="invoices_col_amount_{{ $invoice->bill_invoiceid }}">
        {{ runtimeMoneyFormat($invoice->bill_final_amount) }}</td>
    @if(config('visibility.invoices_col_payments'))
    <td class="invoices_col_payments" id="invoices_col_payments_{{ $invoice->bill_invoiceid }}">
        <a
            href="{{ url('payments?filter_payment_invoiceid='.$invoice->bill_invoiceid) }}">{{ runtimeMoneyFormat($invoice->sum_payments) }}</a>
    </td>
    @endif
    <td class="invoices_col_balance hidden" id="invoices_col_balance_{{ $invoice->bill_invoiceid }}">
        {{ runtimeMoneyFormat($invoice->invoice_balance) }}
    </td>
    <td class="invoices_col_status" id="invoices_col_status_{{ $invoice->bill_invoiceid }}">

        <span class="label {{ runtimeInvoiceStatusColors($invoice->bill_status, 'label') }}">{{
            runtimeLang($invoice->bill_status) }}</span>

        @if(config('system.settings_estimates_show_view_status') == 'yes' && auth()->user()->is_team &&
        $invoice->bill_status != 'draft' && $invoice->bill_status != 'paid')
        <!--estimate not viewed-->
        @if($invoice->bill_viewed_by_client == 'no')
        <span class="label label-icons label-icons-default" data-toggle="tooltip" data-placement="top"
            title="@lang('lang.client_has_not_opened')"><i class="sl-icon-eye"></i></span>
        @endif
        <!--estimate viewed-->
        @if($invoice->bill_viewed_by_client == 'yes')
        <span class="label label-icons label-icons-info" data-toggle="tooltip" data-placement="top"
            title="@lang('lang.client_has_opened')"><i class="sl-icon-eye"></i></span>
        @endif
        @endif

    </td>
    <td class="invoices_col_action actions_column" id="invoices_col_action_{{ $invoice->bill_invoiceid }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">

            <!--client options-->
            @if(auth()->user()->is_client)
            <a title="{{ cleanLang(__('lang.download')) }}" title="{{ cleanLang(__('lang.download')) }}"
                class="data-toggle-tooltip data-toggle-tooltip btn btn-outline-warning btn-circle btn-sm"
                href="{{ url('/invoices/'.$invoice->bill_invoiceid.'/pdf') }}" download>
                <i class="ti-import"></i></a>
            @endif
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_invoice')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/invoices/{{ $invoice->bill_invoiceid }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <a href="/invoices/{{ $invoice->bill_invoiceid }}/edit-invoice" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="sl-icon-note"></i>
            </a>
            @endif
            <a href="/invoices/{{ $invoice->bill_invoiceid }}" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>

            <!--more button (team)-->
            @if(auth()->user()->is_team)
            <span class="list-table-action dropdown font-size-inherit">
                <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" title="{{ cleanLang(__('lang.more')) }}"
                    class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                    <i class="ti-more"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="listTableAction">
                    @if(config('visibility.action_buttons_edit'))
                    <!--quick edit-->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                        data-toggle="modal" data-target="#commonModal"
                        data-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'/edit') }}"
                        data-loading-target="commonModalBody"
                        data-modal-title="{{ cleanLang(__('lang.edit_invoice')) }}"
                        data-action-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'?ref=list') }}"
                        data-action-method="PUT" data-action-ajax-class=""
                        data-action-ajax-loading-target="invoices-td-container">
                        {{ cleanLang(__('lang.quick_edit')) }}
                    </a>
                    <!--email to client-->
                    <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                        data-confirm-title="{{ cleanLang(__('lang.email_to_client')) }}"
                        data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-url="{{ url('/invoices') }}/{{ $invoice->bill_invoiceid }}/resend?ref=list">
                        {{ cleanLang(__('lang.email_to_client')) }}</a>
                    <!--add payment-->
                    @if(auth()->user()->role->role_invoices > 1)
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="{{ cleanLang(__('lang.add_new_payment')) }}"
                        data-url="{{ url('/payments/create?bill_invoiceid='.$invoice->bill_invoiceid) }}"
                        data-action-url="{{ urlResource('/payments?ref=invoice&source=list&bill_invoiceid='.$invoice->bill_invoiceid) }}"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.add_new_payment')) }}</a>
                    @endif
                    <!--clone invoice-->
                    @if(auth()->user()->role->role_invoices > 1)
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="{{ cleanLang(__('lang.clone_invoice')) }}"
                        data-url="{{ url('/invoices/'.$invoice->bill_invoiceid.'/clone') }}"
                        data-action-url="{{ url('/invoices/'.$invoice->bill_invoiceid.'/clone') }}"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.clone_invoice')) }}</a>
                    @endif
                    <!--change category-->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
                        data-url="{{ url('/invoices/change-category') }}"
                        data-action-url="{{ urlResource('/invoices/change-category?id='.$invoice->bill_invoiceid) }}"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.change_category')) }}</a>
                    <!--attach project -->
                    @if(!is_numeric($invoice->bill_projectid))
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title=" {{ cleanLang(__('lang.attach_to_project')) }}"
                        data-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'/attach-project?client_id='.$invoice->bill_clientid) }}"
                        data-action-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'/attach-project') }}"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.attach_to_project')) }}</a>
                    @endif
                    <!--dettach project -->
                    @if(is_numeric($invoice->bill_projectid))
                    <a class="dropdown-item confirm-action-danger" href="javascript:void(0)"
                        data-confirm-title="{{ cleanLang(__('lang.detach_from_project')) }}"
                        data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'/detach-project') }}">
                        {{ cleanLang(__('lang.detach_from_project')) }}</a>
                    @endif
                    <!--recurring settings-->
                    <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'/recurring-settings?source=page') }}"
                        data-loading-target="commonModalBody"
                        data-modal-title="{{ cleanLang(__('lang.recurring_settings')) }}"
                        data-action-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'/recurring-settings?source=page') }}"
                        data-action-method="POST"
                        data-action-ajax-loading-target="invoices-td-container">{{ cleanLang(__('lang.recurring_settings')) }}</a>
                    <!--stop recurring -->
                    @if($invoice->bill_recurring == 'yes')
                    <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                        data-confirm-title="{{ cleanLang(__('lang.stop_recurring')) }}"
                        data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-url="{{ urlResource('/invoices/'.$invoice->bill_invoiceid.'/stop-recurring') }}">
                        {{ cleanLang(__('lang.stop_recurring')) }}</a>
                    @endif
                    @endif
                    <a class="dropdown-item"
                        href="{{ url('payments?filter_payment_invoiceid='.$invoice->bill_invoiceid) }}">
                        {{ cleanLang(__('lang.view_payments')) }}</a>
                    <a class="dropdown-item" href="{{ url('/invoices/'.$invoice->bill_invoiceid.'/pdf') }}" download>
                        {{ cleanLang(__('lang.download')) }}</a>
                </div>
            </span>
            @endif
            <!--more button-->
        </span>
        <!--action button-->

    </td>
</tr>
@endforeach
<!--each row-->