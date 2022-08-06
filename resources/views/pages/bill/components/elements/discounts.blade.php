<div id="invoice-discounts-popover-content">
    <div class="p-t-10">
        <!--select type-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label">@lang('lang.discount_type')</label>
            <div class="col-12">
                <select class="custom-select form-control form-control-sm" id="js-billing-discount-type">
                    <option value="none">{{ cleanLang(__('lang.no_discount')) }}</option>
                    <option value="fixed" {{ runtimePreselected('fixed', $bill['bill_discount_type'] ?? '') }}>{{ cleanLang(__('lang.fixed')) }}
                    </option>
                    <option value="percentage"
                        {{ runtimePreselected('percentage', $bill['bill_discount_type'] ?? '') }}>
                        {{ cleanLang(__('lang.percentage')) }}</option>
                </select>
            </div>
        </div>
        <!--percentage discounts-->
        <div class="form-group row hidden" id="billing-discounts-popover-percentage">
            <label class="col-12 text-left control-label col-form-label">Percentage</label>
            <div class="col-12">
                <input type="number" class="form-control form-control-sm" id="js_bill_discount_percentage" name="js_bill_discount_percentage"
                    value="{{ $bill->bill_discount_percentage }}">
            </div>
        </div>
        <!--percentage fixed-->
        <div class="form-group row hidden" id="billing-discounts-popover-fixed">
            <label class="col-12 text-left control-label col-form-label">{{ cleanLang(__('lang.fixed_amount')) }}</label>
            <div class="col-12">
                <input type="number" class="form-control form-control-sm" id="js_bill_discount_amount"
                    name="" value="{{ $bill->bill_discount_amount }}">
            </div>
        </div>
        <!--update-->
        <div class="form-group text-right">
            <button type="button" class="btn btn-info btn-sm" id="billing-discount-popover-update">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>
</div>