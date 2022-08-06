<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-6 align-self-center text-right parent-page-actions p-b-9"
    id="list-page-actions-container">
    <div id="list-page-actions">

        <!--reminder-->
        @if(config('visibility.modules.reminders'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.reminder')) }}"
            id="reminders-panel-toggle-button"
            class="reminder-toggle-panel-button list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-reminder-panel ajax-request {{ $project->reminder_status }}"
            data-url="{{ url('reminders/start?resource_type=project&resource_id='.$project->project_id) }}"
            data-loading-target="reminders-side-panel-body" data-progress-bar='hidden'
            data-target="reminders-side-panel" data-title="@lang('lang.my_reminder')">
            <i class="ti-alarm-clock"></i>
        </button>
        @endif

        @if(auth()->user()->role->role_assign_projects == 'yes')
        <button type="button" title="{{ cleanLang(__('lang.assigned_users')) }}"
            class="data-toggle-action-tooltip list-actions-button btn btn-page-actions waves-effect edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
            data-toggle="modal" data-target="#commonModal"
            data-url="{{ urlResource('/projects/'.$project->project_id.'/assigned') }}"
            data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.assigned_users')) }}"
            data-action-url="{{ urlResource('/projects/'.$project->project_id.'/assigned?ref=page') }}" data-action-method="PUT"
            data-modal-size="modal-sm"
            data-action-ajax-class="ajax-request"
            data-action-ajax-class="" data-action-ajax-loading-target="projects-td-container">
            <i class="sl-icon-people"></i>
        </button>
        @endif

        <!--edit project-->
        @if(config('visibility.edit_project_button'))
        <span class="dropdown">
            <button type="button" data-toggle="dropdown" title="{{ cleanLang(__('lang.edit')) }}" aria-haspopup="true"
                aria-expanded="false"
                class="data-toggle-tooltip list-actions-button btn btn-page-actions waves-effect waves-dark">
                <i class="sl-icon-note"></i>
            </button>

            <div class="dropdown-menu" aria-labelledby="listTableAction">
                <!--edit-->
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-url="{{ urlResource('/projects/'.$project->project_id.'/edit') }}"
                    data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_project')) }}"
                    data-action-url="{{ urlResource('/projects/'.$project->project_id.'?ref=page') }}"
                    data-action-method="PUT" data-action-ajax-class=""
                    data-action-ajax-loading-target="projects-td-container">
                    {{ cleanLang(__('lang.edit_project')) }}</a>


                <!--update progress-->
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                    data-modal-title="{{ cleanLang(__('lang.update_progress')) }}"
                    data-url="{{ url('/projects/'.$project->project_id.'/progress?ref=page') }}"
                    data-action-url="{{ url('/projects/'.$project->project_id.'/progress?ref=page') }}"
                    data-loading-target="actionsModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.update_progress')) }}</a>


                <!--change category-->
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                    data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
                    data-url="{{ url('/projects/change-category') }}"
                    data-action-url="{{ urlResource('/projects/change-category?ref=page&id='.$project->project_id) }}"
                    data-loading-target="actionsModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.change_category')) }}</a>

                <!--change status-->
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                    data-modal-title="{{ cleanLang(__('lang.change_status')) }}"
                    data-url="{{ urlResource('/projects/'.$project->project_id.'/change-status') }}"
                    data-action-url="{{ urlResource('/projects/'.$project->project_id.'/change-status?ref=page') }}"
                    data-loading-target="actionsModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.change_status')) }}</a>
                <!--stop all timers-->
                <a href="javascript:void(0)" class="dropdown-item confirm-action-danger"
                    data-confirm-title="{{ cleanLang(__('lang.stop_all_timers')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ urlResource('/projects/'.$project->project_id.'/stop-all-timers') }}">
                    {{ cleanLang(__('lang.stop_all_timers')) }}
                </a>

                <!--archive-->
                @if($project->project_active_state == 'active')
                <a href="javascript:void(0)" class="dropdown-item confirm-action-info"
                    data-confirm-title="{{ cleanLang(__('lang.archive_project')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ url('/projects/'.$project->project_id.'/archive?ref=page') }}">
                    {{ cleanLang(__('lang.archive')) }}
                </a>
                @endif

                <!--activate-->
                @if($project->project_active_state == 'archived')
                <a href="javascript:void(0)" class="dropdown-item confirm-action-info"
                    data-confirm-title="{{ cleanLang(__('lang.restore_project')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ url('/projects/'.$project->project_id.'/activate?ref=page') }}">
                    {{ cleanLang(__('lang.restore')) }}
                </a>
                @endif

            </div>
        </span>

        <!--clone-->
        <span class="dropdown">
            <button type="button" class="data-toggle-tooltip list-actions-button btn btn-page-actions waves-effect waves-dark 
                                actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                title="{{ cleanLang(__('lang.clone_project')) }}" data-toggle="modal" data-target="#commonModal"
                data-modal-title="{{ cleanLang(__('lang.clone_project')) }}"
                data-url="{{ url('/projects/'.$project->project_id.'/clone') }}"
                data-action-url="{{ url('/projects/'.$project->project_id.'/clone') }}"
                data-loading-target="actionsModalBody" data-action-method="POST">
                <i class=" mdi mdi-content-copy"></i>
            </button>
        </span>
        @endif


        <!--delete project-->
        @if(config('visibility.delete_project_button'))
        <!--delete-->
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.delete_project')) }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark confirm-action-danger"
            data-confirm-title="{{ cleanLang(__('lang.delete_project')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
            data-url="{{ url('/projects/'.$project->project_id.'?source=page') }}"><i
                class="sl-icon-trash"></i></button>
        @endif
    </div>
</div>
<!-- action buttons -->