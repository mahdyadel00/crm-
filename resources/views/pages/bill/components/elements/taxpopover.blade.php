<!--taxes - popover -->
<div id="invoice-tax-popover-content">
    <div class="p-t-10">
        <!--select type-->
        <!--[notes]: will be used with inline feature-->
        <div class="form-group m-t-10 hidden">
            <select class="custom-select col-12 form-control form-control-sm" id="billing-tax-type">
                <option value="none" {{ runtimePreselected('none', $bill['bill_tax_type'] ?? '') }}>{{ cleanLang(__('lang.no_tax')) }}</option>
                <option value="inline" {{ runtimePreselected('inline', $bill['bill_tax_type'] ?? '') }}>{{ cleanLang(__('lang.inline_tax')) }}</option>
                <option value="summary" {{ runtimePreselected('summary', $bill['bill_tax_type'] ?? '') }}>{{ cleanLang(__('lang.summary_tax')) }}</option>
            </select>
        </div>
        <div class="form-group m-t-10 hidden" id="billing-tax-popover-inline-info">
            {{ cleanLang(__('lang.you_can_set_tax_on_each_line')) }}
        </div>
        <!--tax rates for 'summary' typs-->
        <div class="hidden" id="billing-tax-popover-summary-info">
            @if(count($taxrates) > 0)
            @foreach($taxrates as $taxrate)
            <div class="form-group m-b-0 m-t-0">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input js_summary_tax_rate"
                           data-tax-unique-id="{{ $taxrate->taxrate_uniqueid }}"
                           id="tax-{{ $taxrate->taxrate_uniqueid }}" 
                           value="{{ $taxrate->taxrate_value }}">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">{{ $taxrate->taxrate_name }}
                        ({{ $taxrate->taxrate_value }}%)</span>
                    </label>
            </div>
            @endforeach
            @else
            <div class="text-center">
                <h5>{{ cleanLang(__('lang.no_tax_rates_available')) }}</h5>
            </div>
            @endif
        </div>
        <!--update-->
        <div class="form-group text-right">
            <button type="button" class="btn btn-info btn-sm" id="billing-tax-popover-update">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>
</div>