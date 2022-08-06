@foreach($projects as $project)
<tr id="project_{{ $project->project_id }}">
    @if(config('visibility.projects_col_checkboxes'))
    <td class="projects_col_checkbox checkitem" id="projects_col_checkbox_{{ $project->project_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-projects-{{ $project->project_id }}"
                name="ids[{{ $project->project_id }}]"
                class="listcheckbox listcheckbox-projects filled-in chk-col-light-blue"
                data-actions-container-class="projects-checkbox-actions-container">
            <label for="listcheckbox-projects-{{ $project->project_id }}"></label>
        </span>
    </td>
    @endif
    <td class="projects_col_id">
        <a href="{{ _url('/projects/'.$project->project_id) }}">{{ $project->project_id }}</label></a>
    </td>
    <td class="projects_col_project">
        <a href="{{ _url('/projects/'.$project->project_id) }}">{{ str_limit($project->project_title ??'---', 20) }}<a />
    </td>
    @if(config('visibility.projects_col_client'))
    <td class="projects_col_client">
        <a
            href="/clients/{{ $project->project_clientid }}">{{ str_limit($project->client_company_name ??'---', 12) }}</a>
    </td>
    @endif
    <td class="projects_col_start_date hidden">
        {{ runtimeDate($project->project_date_start) }}
    </td>
    <td class="projects_col_end_date">{{ runtimeDate($project->project_date_due) }}</td>
    @if(config('visibility.projects_col_tags'))
    <td class="projects_col_tags">
        <!--tag-->
        @if(count($project->tags) > 0)
        @foreach($project->tags->take(1) as $tag)
        <span class="label label-outline-default">{{ str_limit($tag->tag_title, 15) }}</span>
        @endforeach
        @else
        <span>---</span>
        @endif
        <!--/#tag-->

        <!--more tags (greater than tags->take(x) number above -->
        @if(count($project->tags) > 1)
        @php $tags = $project->tags; @endphp
        @include('misc.more-tags')
        @endif
        <!--more tags-->
    </td>
    @endif
    <td class="projects_col_progress p-r-20">
        <div class="progress" data-toggle="tooltip" title="{{ $project->project_progress }}%">
            @if($project->project_progress == 100)
            <div class="progress-bar bg-success w-100 h-px-10 font-11 font-weight-500" data-toggle="tooltip"
                title="100%" role="progressbar"></div>
            @else
            <!--dynamic inline style-->
            <div class="progress-bar bg-info h-px-10 font-16 font-weight-500 w-{{ round($project->project_progress) }}"
                role="progressbar"></div>
            @endif
        </div>
    </td>
    @if(config('visibility.projects_col_team'))
    <td class="projects_col_team">
        <!--assigned users-->
        @if(count($project->assigned) > 0)
        @foreach($project->assigned->take(2) as $user)
        <img src="{{ $user->avatar }}" data-toggle="tooltip" title="{{ $user->first_name }}" data-placement="top"
            alt="{{ $user->first_name }}" class="img-circle avatar-xsmall">
        @endforeach
        @else
        <span>---</span>
        @endif
        <!--assigned users-->
        <!--more users-->
        @if(count($project->assigned) > 2)
        @php $more_users_title = __('lang.assigned_users'); $users = $project->assigned; @endphp
        @include('misc.more-users')
        @endif
        <!--more users-->
    </td>
    @endif
    <td class="projects_col_status">
        <span
            class="label {{ runtimeProjectStatusColors($project->project_status, 'label') }}">{{ runtimeLang($project->project_status) }}</span>
        <!--archived-->
        @if($project->project_active_state == 'archived' && runtimeArchivingOptions())
        <span class="label label-icons label-icons-default" data-toggle="tooltip" data-placement="top"
            title="@lang('lang.archived')"><i class="ti-archive"></i></span>
        @endif
    </td>
    <td class="projects_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            @if(config('visibility.action_buttons_delete'))
            <!--[delete]-->
            @if($project->permission_delete_project)
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ _url('/projects/'.$project->project_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled  {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-trash"></i></span>
            @endif
            @endif
            <!--[edit]-->
            @if(config('visibility.action_buttons_edit'))
            @if($project->permission_edit_project)
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/projects/'.$project->project_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_project')) }}"
                data-action-url="{{ urlResource('/projects/'.$project->project_id) }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="projects-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled  {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-note"></i></span>
            @endif
            @if(auth()->user()->role->role_assign_projects == 'yes')
            <button type="button" title="{{ cleanLang(__('lang.assigned_users')) }}"
                class="btn btn-outline-warning btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form data-toggle-action-tooltip"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/projects/'.$project->project_id.'/assigned') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.assigned_users')) }}"
                data-action-url="{{ urlResource('/projects/'.$project->project_id.'/assigned') }}" data-action-method="PUT"
                data-modal-size="modal-sm"
                data-action-ajax-class="ajax-request"
                data-action-ajax-class="" data-action-ajax-loading-target="projects-td-container">
                <i class="sl-icon-people"></i>
            </button>
            @endif
            @endif
            <!--view-->
            <a href="{{ _url('/projects/'.$project->project_id) }}" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->
        <!--more button (team)-->
        @if(config('visibility.action_buttons_edit'))
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                title="{{ cleanLang(__('lang.more')) }}"
                class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                <i class="ti-more"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="listTableAction">
                @include('pages.projects.views.common.dropdown-menu-team')
            </div>
        </span>
        @endif
    </td>
</tr>
@endforeach
<!--each row-->