@foreach($expenses as $expense)
<!--each row-->
<tr id="expense_{{ $expense->expense_id }}">
    @if(config('visibility.expenses_col_checkboxes'))
    <td class="expenses_col_checkbox checkitem" id="expenses_col_checkbox_{{ $expense->expense_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-expenses-{{ $expense->expense_id }}"
                name="ids[{{ $expense->expense_id }}]"
                class="listcheckbox listcheckbox-expenses filled-in chk-col-light-blue expenses-checkbox"
                data-actions-container-class="expenses-checkbox-actions-container"
                data-expense-id="{{ $expense->expense_id }}" data-unit="{{ cleanLang(__('lang.item')) }}" data-quantity="1"
                data-description="{{ $expense->expense_description }}" data-rate="{{ $expense->expense_amount }}">
            <label for="listcheckbox-expenses-{{ $expense->expense_id }}"></label>
        </span>
    </td>
    @endif
    @if(config('visibility.expenses_col_date'))
    <td class="expenses_col_date">
        {{ runtimeDate($expense->expense_date) }}
    </td>
    @endif
    @if(config('visibility.expenses_col_description'))
    <td class="expenses_col_description">
        @if(config('settings.trimmed_title'))
        <span
            title="{{ $expense->expense_description }}">{{ str_limit($expense->expense_description ?? '---', 15) }}</span>
        @else
        <span
            title="{{ $expense->expense_description }}">{{ str_limit($expense->expense_description ?? '---', 35) }}</span>
        @endif
    </td>
    @endif
    <!--column visibility-->
    @if(config('visibility.expenses_col_user'))
    <td class="expenses_col_user">
        <img src="{{ getUsersAvatar($expense->avatar_directory, $expense->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall"> {{ str_limit($expense->first_name ?? runtimeUnkownUser(), 8) }}
    </td>
    @endif
    <!--column visibility-->
    @if(config('visibility.expenses_col_client'))
    <td class="expenses_col_client">
        <a
            href="/clients/{{ $expense->expense_clientid }}">{{ str_limit($expense->client_company_name ?? '---', 12) }}</a>
    </td>
    @endif
    <!--column visibility-->
    @if(config('visibility.expenses_col_project'))
    <td class="expenses_col_project">
        <a href="/projects/{{ $expense->expense_projectid }}">{{ str_limit($expense->project_title ?? '---', 12) }}</a>
    </td>
    @endif
    @if(config('visibility.expenses_col_amount'))
    <td class="expenses_col_amount">
        {{ runtimeMoneyFormat($expense->expense_amount) }}
    </td>
    @endif
    @if(config('visibility.expenses_col_status'))
    <td class="expenses_col_status">

        @if($expense->expense_billable == 'billable')
        @if($expense->expense_billing_status == 'invoiced')
        <span class="table-icons">
            <a href="{{ url('/invoices/'.$expense->expense_billable_invoiceid) }}">
                <i class="mdi mdi-credit-card-plus text-danger" data-toggle="tooltip"
                    title="{{ cleanLang(__('lang.invoiced')) }} : {{ runtimeInvoiceIdFormat($expense->expense_billable_invoiceid) }}"></i>
            </a>
        </span>
        @else
        <span class="table-icons">
            <i class="mdi mdi-credit-card-plus text-success" data-toggle="tooltip" title="{{ cleanLang(__('lang.billable')) }} - {{ cleanLang(__('lang.not_invoiced')) }}"></i>
        </span>
        @endif
        @else
        <span class="table-icons">
            <i class="mdi mdi-credit-card-off text-disabled" data-toggle="tooltip" title="{{ cleanLang(__('lang.not_billable')) }}"></i>
        </span>
        @endif
    </td>
    @endif
    @if(config('visibility.expenses_col_action'))
    <td class="expenses_col_action actions_column" id="expenses_col_action_{{ $expense->expense_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE" data-url="{{ url('/') }}/expenses/{{ $expense->expense_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/expenses/'.$expense->expense_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_expense')) }}"
                data-action-url="{{ urlResource('/expenses/'.$expense->expense_id.'?ref=list') }}"
                data-action-method="PUT" data-action-ajax-class=""
                data-action-ajax-loading-target="expenses-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @endif
            <button type="button" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-tooltip show-modal-button btn btn-outline-info btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#plainModal" data-loading-target="plainModalBody"
                data-modal-title="{{ cleanLang(__('lang.expense_records')) }}" data-url="{{ url('/expenses/'.$expense->expense_id) }}">
                <i class="ti-new-window"></i>
            </button>

            <!--more button (team)-->
            @if(config('visibility.action_buttons_edit') == 'show')
            <span class="list-table-action dropdown font-size-inherit">
                <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" title="{{ cleanLang(__('lang.more')) }}" class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                    <i class="ti-more"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="listTableAction">
                    @if($expense->expense_billing_status == 'not_invoiced')
                    <!--actions button - attach project -->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title=" {{ cleanLang(__('lang.attach_to_project')) }}"
                        data-url="{{ url('/expenses/' . $expense->expense_id .'/attach-dettach') }}"
                        data-action-url="{{ urlResource('/expenses/' . $expense->expense_id .'/attach-dettach') }}"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.attach_dettach')) }}</a>
                    @endif
                    <!--actions button - change category-->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
                        data-url="{{ url('/expenses/change-category') }}"
                        data-action-url="{{ urlResource('/expenses/change-category?id='.$expense->expense_id) }}"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.change_category')) }}</a>
                </div>
            </span>
            @endif
            <!--more button-->
        </span>
        <!--action button-->

    </td>
    @endif
</tr>
@endforeach
<!--each row-->