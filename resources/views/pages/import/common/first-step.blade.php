<!--FIRST STEP-->
<div id="importing-step-1">

    <!--splash images-->
    <div class="x-splash-images">
        <span class="x-icons">
            <img src="{{ url('public/images/file-icons/icon-csv.svg') }}" alt="CSV FILE" />
        </span>
        <span class="x-icons">
            <img src="{{ url('public/images/file-icons/icon-xlsx.svg') }}" alt="XLSX FILE" />
        </span>
    </div>


    <!--fileupload-->
    <div class="form-group row">
        <div class="col-12">
            <div class="dropzone dz-clickable text-center file-upload-box import-files-upload">
                <div class="dz-default dz-message">
                    <div>
                        <h4>{{ cleanLang(__('lang.drag_drop_single_file')) }}</h4>
                    </div>
                    <div>
                        <h6>(CVS or XLSX)</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--samples-->
    <div class="p-t-10 p-b-30 text-align-center">
        @include('pages.import.common.samples')
    </div>
</div>