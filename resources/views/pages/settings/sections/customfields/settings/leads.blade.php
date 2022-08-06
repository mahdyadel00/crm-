@if(request('customfields_type') == 'leads')
<tr class="customfields-table-settings hidden toggle-table-settings-row  toggle-table-settings-row-{{ $field->customfields_id }}"
    id="customfields_settings_row_{{ $field->customfields_id }}">
    <td class="b-0">
        <div class="row">
            <!--dropdown options-->
            @include('pages.settings.sections.customfields.settings.dropdown')

            <!--use in standard (app) form-->
            <div class="col-4">
                <input type="checkbox" id="customfields_standard_form_status[{{ $field->customfields_id }}]"
                    name="customfields_standard_form_status[{{ $field->customfields_id }}]"
                    class="filled-in chk-col-pink" {{ runtimePrechecked($field->customfields_standard_form_status) }}>
                <label
                    for="customfields_standard_form_status[{{ $field->customfields_id }}]">@lang('lang.use_in_standard_form')</label>
            </div>
            <!--show on lead card-->
            <div class="col-4">
                <input type="checkbox" id="customfields_show_lead_summary[{{ $field->customfields_id }}]"
                    name="customfields_show_lead_summary[{{ $field->customfields_id }}]"
                    class="filled-in chk-col-light-blue"
                    {{ runtimePrechecked($field->customfields_show_lead_summary) }}>
                <label
                    for="customfields_show_lead_summary[{{ $field->customfields_id }}]">@lang('lang.show_lead')</label>
            </div>
            <!--show in filter [FUTURE]-->
            <div class="col-4 hidden">
                <input type="checkbox" id="customfields_show_filter_panel[{{ $field->customfields_id }}]"
                    name="customfields_show_filter_panel[{{ $field->customfields_id }}]"
                    class="filled-in chk-col-light-blue"
                    {{ runtimePrechecked($field->customfields_show_filter_panel) }}>
                <label
                    for="customfields_show_filter_panel[{{ $field->customfields_id }}]">@lang('lang.show_in_filter_panel')</label>
            </div>

            <!--field status-->
            <div class="col-4">
                <div class="switch" id="customfields_settings_status_{{ $field->customfields_id }}">
                    @lang('lang.status') 
                    <label>
                        <input {{ runtimePrechecked($field->customfields_status ?? '') }} type="checkbox" name="customfields_status[{{ $field->customfields_id }}]">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>

            <!--delete-field-->
            <div class="col-4">
                <button type="button" class="btn btn-danger btn-sm btn-sm confirm-action-danger"
                    data-confirm-title="@lang('lang.delete_item')" data-confirm-text="@lang('lang.are_you_sure')"
                    data-ajax-type="DELETE" data-url="{{ url('/settings/customfields/'.$field->customfields_id)}}">
                    @lang('lang.delete_item')
                </button>
            </div>
    </td>
    <td class="b-0">
    </td>
</tr>
@endif