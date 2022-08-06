<div class="form-group row">
    <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.status')) }}</label>
    <div class="col-sm-12">
        <select class="select2-basic form-control form-control-sm" id="bill_status" name="bill_status">
            <option value="draft" {{ runtimePreselected('draft', $estimate->bill_status) }}>{{ cleanLang(__('lang.draft')) }}</option>
            <option value="new" {{ runtimePreselected('new', $estimate->bill_status) }}>{{ cleanLang(__('lang.new')) }}</option>
            <option value="accepted" {{ runtimePreselected('accepted', $estimate->bill_status) }}>{{ cleanLang(__('lang.accepted')) }}</option>
            <option value="declined" {{ runtimePreselected('declined', $estimate->bill_status) }}>{{ cleanLang(__('lang.declined')) }}</option>
            <option value="revised" {{ runtimePreselected('revised', $estimate->bill_status) }}>{{ cleanLang(__('lang.revised')) }}</option>
            <option value="expired" {{ runtimePreselected('expired', $estimate->bill_status) }}>{{ cleanLang(__('lang.expired')) }}</option>
        </select>
    </div>
</div>