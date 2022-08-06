<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-6 align-self-center text-right parent-page-actions p-b-9"
    id="list-page-actions-container">
    <div id="list-page-actions">

        <!--edit project-->
        @if(config('visibility.edit_project_button'))
        <span class="dropdown">
            <button type="button" title="{{ cleanLang(__('lang.edit_template')) }}" class="data-toggle-tooltip list-actions-button btn btn-page-actions waves-effect waves-dark 
                actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/templates/projects/'.$project->project_id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="{{ cleanLang(__('lang.edit_project_template')) }}" data-action-url="{{ urlResource('/templates/projects/'.$project->project_id).'?ref=page' }}"
                data-action-method="PUT" data-action-ajax-class="js-ajax-ux-request"
                data-action-ajax-loading-target="projects-td-container">
                <i class="sl-icon-note"></i>
            </button>
        </span>
        @endif


        <!--delete project-->
        @if(config('visibility.delete_project_button'))
        <!--delete-->
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.delete_project')) }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark confirm-action-danger"
            data-confirm-title="{{ cleanLang(__('lang.delete_template')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
            data-url="{{ url('/templates/projects/'.$project->project_id.'?source=page') }}"><i
                class="sl-icon-trash"></i></button>
        @endif
    </div>
</div>
<!-- action buttons -->