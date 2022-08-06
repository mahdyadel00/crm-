<div class="col-12 align-self-center hidden checkbox-actions box-shadow-minimum" id="expenses-checkbox-actions-container">
    <!--button-->
    @if(config('visibility.action_buttons_edit'))
    <div class="x-buttons">
        @if(config('visibility.action_buttons_delete'))
        <button type="button" class="btn btn-sm btn-default waves-effect waves-dark confirm-action-danger" 
                data-type="form"
                data-ajax-type="POST" 
                data-form-id="expenses-list-table" 
                data-url="{{ url('/expenses/delete?type=bulk') }}"
                data-confirm-title="{{ cleanLang(__('lang.delete_selected_items')) }}" 
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                id="checkbox-actions-delete-expenses">
            <i class="sl-icon-trash"></i> {{ cleanLang(__('lang.delete')) }}
        </button>
        @endif
        <button type="button"
            class="btn btn-sm btn-default waves-effect waves-dark actions-modal-button js-ajax-ux-request"
            data-toggle="modal" data-target="#actionsModal" data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
            data-url="{{ urlResource('/expenses/change-category') }}"
            data-action-url="{{ urlResource('/expenses/change-category?type=bulk') }}" data-action-method="POST"
            data-action-type="form" data-action-form-id="main-body" data-loading-target="actionsModalBody"
            data-skip-checkboxes-reset="TRUE" id="checkbox-actions-change-category-expenses">
            <i class="sl-icon-share-alt"></i> {{ cleanLang(__('lang.change_category')) }}
        </button>
    </div>
    @else
    <div class="x-notice">
        {{ cleanLang(__('lang.no_actions_available')) }}
    </div>
    @endif
</div>