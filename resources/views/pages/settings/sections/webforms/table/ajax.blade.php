@foreach($webforms as $webform)
<!--each row-->
<tr id="webform_{{ $webform->webform_id }}">
    <td class="webform_col_date">
        <a href="{{ url('app/settings') }}/formbuilder/{{ $webform->webform_id }}/build">{{ $webform->webform_title }}</a>
    </td>
    <td class="webform_col_name">{{ runtimeDate($webform->webform_created) }}</td>
    <td class="webform_col_created_by">
        <img src="{{ getUsersAvatar($webform->avatar_directory, $webform->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $webform->first_name }}
    </td>
    <td class="webforms_col_submitted">
        {{ $webform->webform_submissions }}
    </td>
    <td class="webform_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--edit-->
            <a title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm"
                href="{{ url('app/settings') }}/formbuilder/{{ $webform->webform_id }}/build">
                <i class="sl-icon-note"></i>
            </a>
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/settings') }}/webforms/{{ $webform->webform_id }}">
                <i class="sl-icon-trash"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->