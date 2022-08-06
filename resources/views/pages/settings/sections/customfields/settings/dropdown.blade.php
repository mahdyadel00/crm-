@if(request('filter_field_type') =='dropdown')
<div class="col-12">
    <div class="form-group row m-b-0">
        <label class="col-12">List options (@lang('lang.type_list_options'))</label>
        <div class="col-12">
            <select name="customfields_datapayload[{{ $field->customfields_id }}]" id="customfields_datapayload[{{ $field->customfields_id }}]"
                class="form-control form-control-sm select2-basic select2-multiple select2-tags-with-spaces select2-hidden-accessible"
                multiple="multiple" tabindex="-1" aria-hidden="true">
                {!! runtimeCustomFieldsJsonLists($field->customfields_datapayload, true) !!}
            </select>
        </div>
    </div>
</div>
@endif