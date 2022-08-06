<div class="row">
    <div class="col-lg-12">
        <!--title-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.milestone_name')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="milestonecategory_title" name="milestonecategory_title"
                    value="{{ $milestone->milestonecategory_title ?? '' }}">
            </div>
        </div>
    </div>

</div>