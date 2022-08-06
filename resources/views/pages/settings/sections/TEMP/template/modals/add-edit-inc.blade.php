<div class="row">
    <div class="col-lg-12">
        <!--title-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.category_name')) }}*</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="category_name" name="category_name" value="{{ $category->category_name ?? '' }}">
                <input type="hidden" name="category_type" value="{{ request('category_type') }}">
            </div>
        </div>
    </div>
</div>