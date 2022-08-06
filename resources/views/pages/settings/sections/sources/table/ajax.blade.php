@foreach($sources as $source)
<!--each row-->
<tr id="source_{{ $source->leadsources_id }}">
    <td class="sources_col_date">
        {{ $source->leadsources_title }}
    </td>
    <td class="sources_col_name">{{ runtimeDate($source->leadsources_created) }}</td>
    <td class="sources_col_created_by">
        <img src="{{ getUsersAvatar($source->avatar_directory, $source->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $source->first_name ?? runtimeUnkownUser() }}
    </td>
    <td class="sources_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button"
            title="{{ cleanLang(__('lang.edit')) }}" class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/settings/sources/'.$source->leadsources_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_lead_source')) }}"
                data-action-url="{{ url('/settings/sources/'.$source->leadsources_id) }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="sources-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}" class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger" data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/settings/sources/{{ $source->leadsources_id }}">
                <i class="sl-icon-trash"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->