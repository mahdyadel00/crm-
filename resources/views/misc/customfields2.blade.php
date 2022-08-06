@foreach($fields as $field)
<div class="form-group row">
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label {{ runtimeCustomFieldsRequiredCSS($field->customfields_required) }}">
        {{ $field->customfields_title }}{{ runtimeCustomFieldsRequiredAsterix($field->customfields_required) }}</label>
    <div class="col-sm-12 col-lg-9">

        <!--text field-->
        @if($field->customfields_datatype =='text')
        <input type="text" class="form-control form-control-sm" id="{{ $field->customfields_name }}"
            name="{{ $field->customfields_name }}" value="{{ $field->current_value ?? ''}}">
        @endif

        <!--textbox field-->
        @if($field->customfields_datatype =='paragraph')
        <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="{{ $field->customfields_name }}"
            id="{{ $field->customfields_name }}">{{ $field->current_value ?? ''}}</textarea>
        @endif

        <!--numbers-->
        @if($field->customfields_datatype =='number' || $field->customfields_datatype =='decimal')
        <input type="number" class="form-control form-control-sm" id="{{ $field->customfields_name }}"
            name="{{ $field->customfields_name }}" value="{{ $field->current_value ?? ''}}">
        @endif

        <!--date field-->
        @if($field->customfields_datatype =='date')
        <input type="text" class="form-control form-control-sm pickadate" name="{{ $field->customfields_name }}"
            autocomplete="off">
        <input class="mysql-date" type="hidden" name="{{ $field->customfields_name }}"
            id="{{ $field->customfields_name }}" value="{{ $field->current_value ?? ''}}">
        @endif

        <!--checkbox field-->
        @if($field->customfields_datatype =='dropdown')
        <select class="select2-basic form-control form-control-sm select2-preselected" id="{{ $field->customfields_name }}"
            name="{{ $field->customfields_name }}" data-preselected="{{ $field->current_value ?? ''}}">
            {!! runtimeCustomFieldsJsonLists($field->customfields_datapayload) !!}
        </select>
        @endif

        <!--dropdown field-->
        @if($field->customfields_datatype =='checkbox')
        <input type="checkbox" id="{{ $field->customfields_name }}" name="customfield_{{ $field->field_id }}" class="filled-in chk-col-light-blue" {{ runtimePrechecked($field->current_value ?? '') }}>
        <label  class="p-l-0" for="{{ $field->customfields_name }}"></label>
        @endif

    </div>
</div>
@endforeach