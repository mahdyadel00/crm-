<!--text aread-->
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label {{ runtimeWebformRequiredBold($payload['required']) }}">
        {{ $payload['label'] }}{{ runtimeWebformRequiredAsterix($payload['required']) }} @if($payload['tooltip'] != '')
        <span class="align-middle text-default" data-toggle="tooltip" title="{{ $payload['tooltip'] }}"
            data-placement="top" style="font-size:16px;"><i class="ti-info-alt"></i></span>
        @endif</label>
    <div class="col-12">
        <textarea class="{{ $payload['class'] }}" rows="5" name="{{ $payload['name'] }}"
            placeholder="{{ $payload['placeholder'] }}" id="{{ $payload['name'] }}"></textarea>
    </div>
</div>