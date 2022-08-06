<div class="row">
    <div class="col-lg-12 p-t-30">
        <!--add comment-->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.comment')) }}</label>
            <div class="col-sm-12">
                <textarea class="form-control form-control-sm" rows="5" name="add_items_description" id="add_items_description"></textarea>
            </div>
        </div>
        <!--fileupload-->
        <div class="form-group row">
            <div class="col-12">
                <div class="dropzone dz-clickable" id="fileupload_receipt">
                    <div class="dz-default dz-message">
                        <i class="icon-Upload-toCloud"></i>
                        <span>{{ cleanLang(__('lang.drag_drop_file')) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!--fileupload-->
        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>