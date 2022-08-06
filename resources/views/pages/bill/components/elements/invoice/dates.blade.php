    <!--dates-->
    <div class="pull-left invoice-dates">
        <table>
            <tr>
                <td class="x-date-lang" id="fx-invoice-date-lang">{{ cleanLang(__('lang.invoice_date')) }} </td>
                @if(config('visibility.bill_mode') == 'editing')
                <td><input type="text" class="form-control form-control-xs pickadate" name="bill_date"
                        autocomplete="off" value="{{ runtimeDate($bill->bill_date) }}">
                    <input class="mysql-date" type="hidden" name="bill_date" id="bill_date"
                        value="{{ $bill->bill_date }}">
                </td>
                @else
                <td class="x-date" > <span>{{ runtimeDate($bill->bill_date) }}</span></td>
                @endif
            </tr>
            <tr>
                <td class="x-date-due-lang" >{{ cleanLang(__('lang.due_date')) }} </td>
                @if(config('visibility.bill_mode') == 'editing')
                <td><input type="text" class="form-control form-control-xs pickadate" name="bill_due_date"
                        autocomplete="off" value="{{ runtimeDate($bill->bill_due_date) }}">
                    <input class="mysql-date" type="hidden" name="bill_due_date" id="bill_due_date"
                        value="{{ $bill->bill_due_date }}">
                </td>
                @else
                <td class="x-date-due"> <span>{{ runtimeDate($bill->bill_due_date) }}</span></td>
                @endif
            </tr>
        </table>
    </div>