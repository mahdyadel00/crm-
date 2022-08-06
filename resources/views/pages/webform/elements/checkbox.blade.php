<!--check box-->
<div class="form-group form-group-checkbox row">
    <div class="col-12 text-left" style="padding-top:5px;">
        <input type="checkbox" id="{{ $payload['name'] }}" name="{{ $payload['name'] }}"
            class="filled-in chk-col-light-blue">
        <label class="p-l-30 control-label {{ runtimeWebformRequiredBold($payload['required']) }}" for="{{ $payload['name'] }}">
            {{ $payload['label'] }}{{ runtimeWebformRequiredAsterix($payload['required']) }} @if($payload['tooltip'] !=
            '')
            <span class="align-middle text-default" data-toggle="tooltip" title="{{ $payload['tooltip'] }}"
                data-placement="top" style="font-size:16px;"><i class="ti-info-alt"></i></span>
            @endif</label>
    </div>
</div>