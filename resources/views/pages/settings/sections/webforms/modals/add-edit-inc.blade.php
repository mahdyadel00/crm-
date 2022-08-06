<div class="row">
    <div class="col-lg-12">
        <!--title-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label required">@lang('lang.form_name')*</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="webform_title" name="webform_title" value="{{ $webform->webform_title ?? '' }}">
            </div>
        </div>
    </div>
</div>
<!--section js resource-->