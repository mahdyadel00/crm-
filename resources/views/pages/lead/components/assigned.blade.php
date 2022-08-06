<!--user-->
@foreach($assigned as $user)
<span class="x-assigned-user js-card-settings-button-static card-lead-assigned card-assigned-listed-user" tabindex="0"
        data-user-id="{{ $user->id }}" data-popover-content="card-lead-team"
        data-title="{{ cleanLang(__('lang.assign_users')) }}"><img
                src="{{ getUsersAvatar($user->avatar_directory, $user->avatar_filename) }}" data-toggle="tooltip"
                title="" data-placement="top" alt="Brian" class="img-circle avatar-xsmall"
                data-original-title="{{ $user->first_name }}"></span>
@endforeach