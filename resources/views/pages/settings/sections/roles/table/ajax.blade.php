@foreach($roles as $role)
<!--each row-->
<tr id="role_{{ $role->role_id }}">
    <td class="roles_col_name">
        {{ $role->role_name }}
        <!--default-->
        @if($role->role_system == 'yes')
        <span class="sl-icon-star text-warning p-l-5" data-toggle="tooltip" title="{{ cleanLang(__('lang.system_default')) }}"></span>
        @endif
    </td>
    <td class="roles_col_users">
        {{ $role->count_users }}
    </td>
    <td class="roles_col_type">
        {{ $role->role_type }}
    </td>
    <td class="roles_col_status">
        @if($role->role_system == 'yes')
        <span class="label label-outline-default">{{ cleanLang(__('lang.default')) }}</span>
        @else
        ---
        @endif
    </td>
    <td class="roles_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            @if($role->role_id != 1)
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/settings/roles/'.$role->role_id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="{{ cleanLang(__('lang.edit_user_role')) }}"
                data-action-url="{{ url('/settings/roles/'.$role->role_id) }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="roles-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i class="sl-icon-note"></i></span>
            @endif
            @if($role->role_system == 'no')
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_user_role')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE" data-url="{{ url('/') }}/settings/roles/{{ $role->role_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i class="sl-icon-trash"></i></span>
            @endif

        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->