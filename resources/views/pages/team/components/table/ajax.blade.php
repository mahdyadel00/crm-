@foreach( $users as $team)
<!--each row-->
<tr id="team_{{ $team->id }}">
    <td class="team_col_first_name">
        <img src="{{ $team->avatar }}" alt="user" class="img-circle avatar-xsmall"> {{ $team->first_name }}
        {{ runtimeCheckBlank($team->last_name) }}
        <!--administrator-->
        @if($team->primary_admin == 'yes')
        <span class="sl-icon-star text-warning p-l-5" data-toggle="tooltip"
            title="{{ cleanLang(__('lang.main_administrator')) }}" id="team_admin_{{ $team->id }}"></span>
        @endif
    </td>
    <td class="team_col_position">
        {{ str_limit(runtimeCheckBlank($team->position), 20) }}
    </td>
    @if(config('visibility.action_super_user'))
    <td class="team_col_role">{{ runtimeCheckBlank($team->role['role_name']) }}</td>
    @endif
    <td class="team_col_email">
        {{ runtimeCheckBlank($team->email) }}
    </td>
    <td class="team_col_phone">
        {{ runtimeCheckBlank($team->phone) }}
    </td>
    @if(config('visibility.action_super_user'))
    <td class="team_col_last_active">
        {{ $team->carbon_last_seen }}
    </td>
    @endif
    @if(config('visibility.action_super_user'))
    <td class="team_col_action">
        <!--action buttons-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(runtimeTeamPermissionDelete($team))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_user')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/team/{{ $team->id ?? '' }}">
                <i class="sl-icon-trash"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-trash"></i></span>
            @endif
            <!--delete-->
            <!--edit-->
            @if(runtimeTeamPermissionEdit($team))
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal" data-url="{{ urlResource('/team/'.$team->id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_user')) }}"
                data-action-url="{{ urlResource('/team/'.$team->id) }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="team-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-note"></i></span>
            @endif
            <!--edit-->
            <!--send email-->
            <button type="button" title="@lang('lang.send_email')"
                class="data-toggle-action-tooltip btn btn-outline-warning btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/appwebmail/compose?view=modal&resource_type=user&resource_id='.$team->id) }}"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.send_email')"
                data-action-url="{{ url('/appwebmail/send') }}" data-action-method="POST" data-modal-size="modal-xl"
                data-action-ajax-loading-target="clients-td-container">
                <i class="ti-email display-inline-block m-t-3"></i>
            </button>
            <!--change password-->
            @if(runtimeTeamPermissionEdit($team))
            <button type="button" title="{{ cleanLang(__('lang.update_password')) }}"
                class="data-toggle-action-tooltip btn btn-outline-default btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/user/updatepassword?team_id='.$team->id) }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.update_password')) }}"
                data-action-url="{{ urlResource('/user/updatepassword') }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="contacts-td-container">
                <i class="sl-icon-lock"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-lock"></i></span>
            @endif
        </span>
        <!--action buttons-->
    </td>
    @endif
</tr>
@endforeach
<!--each row-->