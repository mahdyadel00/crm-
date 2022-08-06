@foreach($fields as $field)
<tr class="toggle-table-settings-row-{{ $field->customfields_id }}">
    <!--title-->
    <td class="p-r-40">
        <input type="text" class="form-control form-control-sm x-form-field js-settings-customfiel-input"
            id="add_invoices_date" data-settings-button-id="settings-customfiel-options-{{ $field->customfields_id }}"
            name="customfields_title[{{ $field->customfields_id }}]" value="{{ $field->customfields_title }}">
    </td>

    <!--settings-->
    <td class="td-checkbox">
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" title=""
                data-settings-row-id="customfields_settings_row_{{ $field->customfields_id }}"
                data-settings-common-rows="toggle-table-settings-row-{{ $field->customfields_id }}"
                class="data-toggle-action-tooltip btn btn-outline-default btn-circle btn-sm js-toggle-table-settings-row">
                <i class="sl-icon-settings"></i>
            </button>
            <!--info tooltip-->
            <span class="align-middle text-info display-inline-block m-t-5" data-toggle="tooltip"
                title="{{ $field->customfields_name }}" data-placement="top"
                style="font-size:16px;"><i class="ti-info-alt"></i></span>
        </span>
    </td>
</tr>


<!--leads settings-->
@include('pages.settings.sections.customfields.settings.leads')
@include('pages.settings.sections.customfields.settings.clients')
@include('pages.settings.sections.customfields.settings.projects')
@include('pages.settings.sections.customfields.settings.tasks')
@endforeach