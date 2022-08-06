<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label">@lang('lang.embed_code')</label>
    <div class="col-12">
        <textarea class="form-control form-control-sm" rows="10" name="add_items_description"
            id="add_items_description">{{  $payload['code'] }}</textarea>
    </div>
</div>

<!--instructions-->
<div class="alert alert-info">
    <div><strong>@lang('lang.instructions')</strong></div>
    @lang('lang.instructions_webform_code')
</div>
<!--section js resource-->