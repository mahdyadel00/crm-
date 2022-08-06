<!--date field-->
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label {{ runtimeWebformRequiredBold($payload['required']) }}">
        {{ $payload['label'] }}{{ runtimeWebformRequiredAsterix($payload['required']) }} @if($payload['tooltip'] != '')
        <span class="align-middle text-default" data-toggle="tooltip" title="{{ $payload['tooltip'] }}"
            data-placement="top" style="font-size:16px;"><i class="ti-info-alt"></i></span>
        @endif</label>
    <div class="col-12">
        <input type="text" class="{{ $payload['class'] }} pickadate" name="{{ $payload['name'] }}" autocomplete="off"
        placeholder="{{ $payload['placeholder'] }}">
        <input class="mysql-date" type="hidden" name="{{ $payload['name'] }}" id="{{ $payload['name'] }}" value="" >
    </div>
</div>