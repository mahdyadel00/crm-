@if(isset($expense->expense_billing_status) && $expense->expense_billing_status == 'invoiced')
<div class="alert alert-warning">{{ cleanLang(__('lang.expense_has_already_been_invoiced_cannot_be_attached')) }}</div>
@else
<!--client-->
<div class="form-group row">
    <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.client')) }}</label>
    <div class="col-sm-12 col-lg-9">
        <!--select2 basic search-->
        <select name="expense_clientid" id="expense_clientid"
            class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
            data-projects-dropdown="expense_projectid" data-feed-request-type="clients_projects"
            data-ajax--url="{{ url('/') }}/feed/company_names">
        </select>
        <!--select2 basic search-->
        </select>
    </div>
</div>
<!--clients projects-->
<div class="form-group row">
    <label for="example-month-input" class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.project')) }}</label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic form-control form-control-sm dynamic_expense_projectid" id="expense_projectid" name="expense_projectid"
            disabled>
        </select>
    </div>
</div>
@endif