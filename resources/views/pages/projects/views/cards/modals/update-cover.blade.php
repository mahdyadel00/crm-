<!--fileupload-->
<div class="form-group row">
    <div class="col-12">
        <div class="dropzone dz-clickable text-center file-upload-box" id="fileupload_cover_image">
            <div class="dz-default dz-message">
                <div>
                    <h4>{{ cleanLang(__('lang.drag_drop_file')) }}</h4>
                </div>
                <div class="p-t-10"><small>{{ cleanLang(__('lang.allowed_file_types')) }}: (jpg, png)</small></div>
                <div class=""><small>@lang('lang.recommended_image_size'): (400 x 170 px)</small></div>
            </div>
        </div>
    </div>
</div>