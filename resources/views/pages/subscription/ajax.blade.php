       @foreach($invoices as $invoice)
       <!--each payment-->
       <div class="subscription-history" id="invoice_{{ $invoice->bill_invoiceid  }}">
           <div class="table-responsive">
               <table class="table m-0">
                   <tbody>
                       <tr class="x-item col-sm-12 col-md-2 p-t-2">
                           <td><i class="sl-icon-credit-card"></i>
                               <a
                                   href="{{ url('/invoices/'.$invoice->bill_invoiceid) }}"><span>{{ $invoice->formatted_bill_invoiceid }}</span></a>
                           </td>
                           <td>{{ runtimeDate($invoice->bill_date) }}</td>
                           <td>@lang('lang.stripe_payment')</td>
                           <td>{{ runtimeMoneyFormat($invoice->bill_final_amount) }}</td>
                           <td><span class="label label-outline-info">@lang('lang.paid')</span>
                           </td>
                           <td>
                               <div class="list-table-action">
                                   <!--delete-->
                                   @if(config('visibility.delete_button'))
                                   <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                                       class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                                       data-confirm-title="{{ cleanLang(__('lang.delete_invoice')) }}"
                                       data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                                       data-ajax-type="DELETE"
                                       data-url="{{ url('/') }}/invoices/{{ $invoice->bill_invoiceid }}">
                                       <i class="sl-icon-trash"></i>
                                   </button>
                                   @endif
                                   <!--view-->
                                   <a href="/invoices/{{ $invoice->bill_invoiceid }}"
                                       title="{{ cleanLang(__('lang.view')) }}"
                                       class="subscription-invoice-button btn btn-outline-info btn-circle btn-sm">
                                       <i class="ti-new-window"></i>
                                   </a>
                                   <!--download-->
                                   <a href="{{ url('/invoices/'.$invoice->bill_invoiceid.'/pdf') }}" download
                                       title="Dowload"
                                       class="subscription-invoice-button data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm"
                                       data-original-title="@lang('lang.download')">
                                       <i class="ti-download"></i>
                                   </a>
                               </div>
                           </td>
                       </tr>
                   <tbody>
               </table>
           </div>
       </div>
       @endforeach