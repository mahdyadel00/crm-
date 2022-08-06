@if(config('visibility.signing_public'))
<!--first name-->
<div class="form-group row">
    <label class="col-4 text-left control-label col-form-label required">@lang('lang.first_name')</label>
    <div class="col-8">
        <input type="text" class="form-control form-control-sm" id="doc_signed_first_name" name="doc_signed_first_name">
    </div>
</div>

<!--last name-->
<div class="form-group row">
    <label class="col-4 text-left control-label col-form-label required">@lang('lang.last_name')</label>
    <div class="col-8">
        <input type="text" class="form-control form-control-sm" id="doc_signed_last_name" name="doc_signed_last_name">
    </div>
</div>
@endif

<!--signature pad-->
<div class="row">
    <label class="col-4 text-left control-label col-form-label required">@lang('lang.draw_your_signature')</label>
    <div class="col-8" id="signature-col-wrapper">
        <div id="signature-wrapper">
            <canvas id="signature-pad" height="180"></canvas>
        </div>
    </div>
    <input type="hidden" name="signature_code" id="signature_code">
</div>
<script src="public/vendor/js/signaturepad/signature_pad.min.js?v={{ config('system.versioning') }}"></script>
<script src="public/js/dynamic/sign.document.js?v={{ config('system.versioning') }}"></script>