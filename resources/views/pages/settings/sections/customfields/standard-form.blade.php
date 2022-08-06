@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<div class="table-responsive">
    <table class="table" id="standard-fields-sorting" data-type="form" data-form-id="standard-fields-sorting"
        data-ajax-type="post" data-url="{{ url('settings/customfields/update-standard-form-positions') }}"
        data-progress-bar='hidden'>
        <thead>
            <tr>


                <th><span>@lang('lang.form_field_name')</span></th>
                <th class="w-px-140">@lang('lang.required')</th>
                <th class="w-px-120">@lang('lang.options')</th>
            </tr>
        </thead>
        <tbody id="standard-fields-container">
            @foreach($fields as $field)
            <tr class="toggle-table-settings-row-{{ $field->customfields_id }}"
                id="toggle-table-settings-row-{{ $field->customfields_id }}">
                <td>
                    <!--sorting data-->
                    <input type="hidden" name="sort-fields[{{ $field->customfields_id }}]"
                        value="{{ $field->customfields_id }}">
                    <span class="mdi mdi-drag-vertical cursor-pointer"></span> <span>
                        {{ $field->customfields_title }}</span></td>
                <td class="p-t-11 p-b-0">
                    <input type="checkbox" id="customfields_required[{{ $field->customfields_id }}]"
                        name="customfields_required[{{ $field->customfields_id }}]"
                        class="filled-in chk-col-light-blue custom-fields-standard-form-required-checkbox"
                        data-url="{{ url('/settings/customfields/standard-form-required')}}"
                        data-form-id="standard-fields-sorting" data-progress-bar='hidden' data-loading-target=""
                        data-ajax-type="PUT" data-type="form" {{ runtimePrechecked($field->customfields_required) }}>
                    <label class="m-0" for="customfields_required[{{ $field->customfields_id }}]"></label>
                </td>
                <td> <button type="button" class="btn btn-danger btn-xs confirm-action-danger"
                        data-confirm-title="@lang('lang.delete_item')" data-confirm-text="@lang('lang.are_you_sure')"
                        data-ajax-type="DELETE" data-url="{{ url('/settings/customfields/'.$field->customfields_id)}}">
                        @lang('lang.remove')
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="alert alert-info" id="custom-fields-display-container">
        <input type="checkbox" 
            name="custom_fields_display_setting" id="custom_fields_display_setting"
            class="filled-in chk-col-light-blue"
            data-url="{{ url('/settings/customfields/standard-form-display-settings?tab_menu_type='.$payload['tab_menu_type'])}}" data-form-id="custom-fields-display-container"
            data-loading-target="" data-ajax-type="PUT" data-type="form"
            {{ runtimePrechecked($payload['display_setting']) }}>
        <label class="m-0" for="custom_fields_display_setting">@lang('lang.show_form_fields_in_collapsed_toggle')<a class="fancybox"
                href="{{ url('/storage/system/images/info-customfields-toggle.jpg') }}"
                title="2020-06-21_214237.jpg" alt="2020-06-21_214237.jpg">
                 <i class="ti-info-alt"></i></a>
        </label>
    </div>

    <div class="alert alert-info">@lang('lang.info_standard_form_info_1')</div>

</div>
@endsection