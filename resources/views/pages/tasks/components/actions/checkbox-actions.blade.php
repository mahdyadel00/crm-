@if(isset($page['page']) && $page['page'] == 'tasks')
<div class="col-md-12 col-lg-7 align-self-center hidden checkbox-actions" id="tasks-checkbox-actions-container">
    <!--button-->
    @if(config('visibility.action_buttons_edit'))
    <div class="x-buttons">
        @if(config('visibility.action_buttons_delete'))
        <button type="button" class="btn btn-page-actions waves-effect waves-dark confirm-action-danger" 
                data-type="form"
                data-ajax-type="POST" 
                data-form-id="tasks-list-table" 
                data-url="{{ url('/tasks/delete?type=bulk') }}"
                data-confirm-title="{{ cleanLang(__('lang.delete_selected_items')) }}" 
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                id="checkbox-actions-delete-tasks">
            <i class="sl-icon-trash"></i> {{ cleanLang(__('lang.delete')) }}
        </button>
        @endif
        <button type="button"
            class="btn btn-page-actions waves-effect waves-dark actions-modal-button js-ajax-ux-request"
            data-toggle="modal" data-target="#actionsModal" data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
            data-url="{{ urlResource('/tasks/change-category') }}"
            data-action-url="{{ urlResource('/tasks/change-category?type=bulk') }}" data-action-method="POST"
            data-action-type="form" data-action-form-id="main-body" data-loading-target="actionsModalBody"
            data-skip-checkboxes-reset="TRUE" id="checkbox-actions-change-category-tasks">
            <i class="sl-icon-share-alt"></i> {{ cleanLang(__('lang.change_category')) }}
        </button>
    </div>
    @else
    <div class="x-notice">
        {{ cleanLang(__('lang.no_actions_available')) }}
    </div>
    @endif
</div>
@endif