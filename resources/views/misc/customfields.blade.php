@foreach($fields as $field)
<div class="form-group row">

    <!--text-->
    @if($field->customfields_datatype =='text')
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12 col-lg-9">
        <input type="text" class="form-control form-control-sm a-custom-field" id="{{ $field->customfields_name }}"
            name="{{ $field->customfields_name }}" value="{{ $field->current_value ?? ''}}">
    </div>
    @endif


    <!--paragraph-->
    @if($field->customfields_datatype =='paragraph')
    <label
        class="col-sm-12 text-left control-label col-form-label {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12">
        <textarea class="form-control form-control-sm tinymce-textarea a-custom-field" rows="5" name="{{ $field->customfields_name }}"
            id="{{ $field->customfields_name }}">{{ $field->current_value ?? ''}}</textarea>
    </div>
    @endif

    <!--number & decimal-->
    @if($field->customfields_datatype =='number' || $field->customfields_datatype =='decimal')
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12 col-lg-9">
        <input type="number" class="form-control form-control-sm a-custom-field" id="{{ $field->customfields_name }}"
            name="{{ $field->customfields_name }}" value="{{ $field->current_value ?? ''}}">
    </div>
    @endif

    <!--date-->
    @if($field->customfields_datatype =='date')
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12 col-lg-9">
        <input type="text" class="form-control form-control-sm pickadate a-custom-field" name="{{ $field->customfields_name }}" value="{{ runtimeDatepickerDate($field->current_value ?? '') }}"
            autocomplete="off">
        <input class="mysql-date" type="hidden" name="{{ $field->customfields_name }}"
            id="{{ $field->customfields_name }}" value="{{ $field->current_value ?? ''}}">
    </div>
    @endif

    <!--dropdown-->
    @if($field->customfields_datatype =='dropdown')
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic form-control form-control-sm select2-preselected a-custom-field" id="{{ $field->customfields_name }}"
            name="{{ $field->customfields_name }}" data-preselected="{{ $field->current_value ?? ''}}">
            <option value=""></option>
            {!! runtimeCustomFieldsJsonLists($field->customfields_datapayload) !!}
        </select>
    </div>
    @endif


    <!--checkbox-->
    @if($field->customfields_datatype =='checkbox')
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12 col-lg-9">
        <input type="checkbox" id="{{ $field->customfields_name }}" name="{{ $field->customfields_name }}" class="filled-in chk-col-light-blue a-custom-field" {{ runtimePrechecked($field->current_value ?? '') }}>
        <label  class="p-l-0" for="{{ $field->customfields_name }}"></label>
    </div>
    @endif

</div>
@endforeach