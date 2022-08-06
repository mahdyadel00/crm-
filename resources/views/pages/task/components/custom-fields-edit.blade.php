@foreach($fields as $field)
<div class="form-group row m-b-8">
    <label
        class="col-sm-12 text-left control-label col-form-label font-13 p-b-1 {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm" id="{{ $field->customfields_name }}"
            name="{{ $field->customfields_name }}"
            value="{{ customFieldValue($field->customfields_name, $task, 'form') }}">
    </div>
</div>
@endforeach