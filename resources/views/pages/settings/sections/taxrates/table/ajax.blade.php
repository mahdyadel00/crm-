@foreach($taxrates as $taxrate)
<!--each row-->
<tr id="taxrate_{{ $taxrate->taxrate_id }}">
    <td class="taxrates_col_name">
        {{ $taxrate->taxrate_name }}
    </td>
    <td class="taxrates_col_value">
        {{ $taxrate->taxrate_value }}%
    </td>
    <td class="taxrates_col_created_by">
        <img src="{{ getUsersAvatar($taxrate->avatar_directory, $taxrate->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $taxrate->first_name ?? runtimeUnkownUser() }}
    </td>
    <td class="taxrates_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/settings/taxrates/'.$taxrate->taxrate_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_lead_source')) }}"
                data-action-url="{{ url('/settings/taxrates/'.$taxrate->taxrate_id) }}" data-action-method="PUT"
                data-action-ajax-class="js-ajax-ux-request" data-action-ajax-loading-target="taxrates-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE" data-url="{{ url('/') }}/settings/taxrates/{{ $taxrate->taxrate_id }}">
                <i class="sl-icon-trash"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->