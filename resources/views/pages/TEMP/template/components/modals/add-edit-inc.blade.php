<div class="form-group row">
    <label class="col-sm-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.invoice_date')) }}*</label>
    <div class="col-sm-9">
        <input type="text" class="form-control  form-control-sm pickadate" autocomplete="off" 
            name="add_invoices_date" placeholder="">
        <input class="mysql-date" type="hidden" name="add_invoices_date" id="add_invoices_date" value="">
    </div>
</div>