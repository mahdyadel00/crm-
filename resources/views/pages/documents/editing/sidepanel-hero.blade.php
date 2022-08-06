<!-- right-sidebar -->
<div class="right-sidebar documents-side-panel-hero sidebar-md" id="documents-side-panel-hero">
    <form name="documents-side-panel-hero">
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <!--add class'due'to title panel -->
                <i class="sl-icon-picture display-inline-block m-t--5"></i>
                <div class="display-inline-block">
                    @lang('lang.edit_main_header')
                </div>
                <span>
                    <i class="ti-close js-close-side-panels" data-target="documents-side-panel-hero"
                        id="documents-side-panel-hero-close-icon"></i>
                </span>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body documents-side-panel-hero-body  p-b-80" id="documents-side-panel-hero-body">



                <!--doc_heading-->
                <div class="form-group row">
                    <label
                        class="col-sm-12 text-left control-label col-form-label required">@lang('lang.main_heading')</label>
                    <div class="col-sm-12 col-lg-10">
                        <input type="text" class="form-control form-control-sm" id="doc_heading" name="doc_heading"
                            value="{{ $document->doc_heading ?? '' }}">
                    </div>
                    <div class="col-sm-12 col-lg-2">
                        <input type="color" class="form-control form-control-sm form-control-color" name="doc_heading_color"
                            value="{{ $document->doc_heading_color ?? ''}}" data-toggle="tooltip" title="@lang('lang.main_heading_color')">
                    </div>
                </div>

                <!--doc_title-->
                <div class="form-group row">
                    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.title')</label>
                    <div class="col-sm-12 col-lg-10">
                        <input type="text" class="form-control form-control-sm" id="doc_title" name="doc_title"
                            value="{{ $document->doc_title ?? '' }}">
                    </div>
                    <div class="col-sm-12 col-lg-2">
                        <input type="color" class="form-control form-control-sm form-control-color" name="doc_title_color"
                            value="{{ $document->doc_title_color ?? ''}}" data-toggle="tooltip" title="@lang('lang.title_color')">
                    </div>
                </div>


                <!--fileupload-->
                <div class="form-group row">
                    <label
                        class="col-sm-12 text-left control-label col-form-label required">@lang('lang.header_image')</label>
                    <div class="col-12">
                        <div class="dropzone dz-clickable" id="doc_hero_header_image">
                            <div class="dz-default dz-message">
                                <i class="icon-Upload-toCloud"></i>
                                <span>@lang('lang.change_header_image')</span>
                                <span>[1000 x 200px]</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--#fileupload-->

                <!--document type-->
                <input type="hidden" name="doc_type" value="{{ $document->doc_type ?? '' }}">

                <!--buttons-->
                <div class="buttons-block">
                    <button type="button" class="btn btn-rounded-x btn-info js-ajax-ux-request"
                        data-url="{{ url('documents/'.$document->doc_id.'/update/hero') }}" data-type="form"
                        data-form-id="documents-side-panel-hero"
                        data-ajax-type="post">@lang('lang.save_changes')</button>
                </div>
            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->