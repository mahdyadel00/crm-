<div class="row">
    <div class="col-lg-12">

        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.invoice_date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control  form-control-sm pickadate" autocomplete="off"
                    name="bill_date_edit" value=""
                    autocomplete="off">
                <input class="mysql-date" type="hidden" name="bill_date" id="bill_date_edit"
                    value="">
            </div>
        </div>

        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.due_date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control  form-control-sm pickadate" autocomplete="off"
                    name="bill_due_date_edit" value=""
                    autocomplete="off">
                <input class="mysql-date" type="hidden" name="bill_date" id="bill_due_date_edit"
                    value="">
            </div>
        </div>

        
        <div class="form-group form-group-checkbox row">
            <div class="col-12 text-left p-t-5">
                <input type="checkbox" id="copy_estimate_terms" name="copy_estimate_terms"
                    class="filled-in chk-col-light-blue" checked>
                <label for="copy_estimate_terms">{{ cleanLang(__('lang.copy_estimate_terms')) }}</label>
            </div>
        </div>

        
        <div class="form-group form-group-checkbox row">
            <div class="col-12 text-left p-t-5">
                <input type="checkbox" id="copy_estimate_notes" name="copy_estimate_notes"
                    class="filled-in chk-col-light-blue" checked>
                <label for="copy_estimate_notes">{{ cleanLang(__('lang.copy_estimate_notes')) }}</label>
            </div>
        </div>

        <div class="form-group form-group-checkbox row">
            <div class="col-12 text-left p-t-5">
                <input type="checkbox" id="delete_original_estimate" name="delete_original_estimate"
                    class="filled-in chk-col-light-blue">
                <label for="delete_original_estimate">{{ cleanLang(__('lang.delete_original_estimate')) }}</label>
            </div>
        </div>

    </div>
</div>