<!--item-->
<form id="document-editor-wrapper">
    <div class="form-group row">
        <div class="col-12">
            <textarea class="form-control form-control-sm tinymce-document-textarea" rows="5" name="doc_body"
                id="doc_body">{{ $document->doc_body ?? '' }}</textarea>
        </div>
    </div>

    <!--document type-->
    <input type="hidden" name="doc_type" value="{{ $document->doc_type }}">

    <!--form buttons-->
    <div class="text-right p-t-30">
        @if($document->doc_type == 'proposal')
        <a type="button" class="btn btn-secondary btn-sm waves-effect text-left"
            href="{{ url('/proposals/'.$document->doc_id) }}">@lang('lang.exit_editing_mode')</a>
        @else
        <a type="button" class="btn btn-secondary btn-sm waves-effect text-left"
            href="{{ url('/proposals/'.$document->doc_id) }}">@lang('lang.exit_editing_mode')</a>
        @endif
        <button type="submit" id="submitButton" class="btn btn-danger btn-sm waves-effect text-left ajax-request"
            data-url="{{ url('/documents/'.$document->doc_id.'/update/body') }}" data-loading-target=""
            data-ajax-type="POST" data-button-loading-annimation="yes"
            data-on-start-submit-button="disable">@lang('lang.save_changes')</button>
    </div>
</form>