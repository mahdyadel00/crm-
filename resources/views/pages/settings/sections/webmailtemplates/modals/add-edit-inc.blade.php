<div class="row">
    <div class="col-lg-12">
        <!--name-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label required">@lang('lang.template_name')*</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="webmail_template_name"
                    name="webmail_template_name" value="{{ $template->webmail_template_name ?? '' }}">
            </div>
        </div>


        <!--name-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label required">@lang('lang.message')*</label>
            <div class="col-12">
                <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="webmail_template_body" id="webmail_template_body">
                    {!! $template->webmail_template_body ?? '' !!}
                </textarea>
            </div>
        </div>

    </div>
</div>
<!--section js resource-->