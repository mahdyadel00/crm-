@foreach($contracts as $contract)
<!--each row-->
<tr id="contract_{{ $contract->doc_id }}">
    @if(config('visibility.contracts_col_checkboxes'))
    <td class="contracts_col_checkbox checkcontract p-l-0" id="contracts_col_checkbox_{{ $contract->doc_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-contracts-{{ $contract->doc_id }}"
                name="ids[{{ $contract->doc_id }}]"
                class="listcheckbox listcheckbox-contracts filled-in chk-col-light-blue contracts-checkbox"
                data-actions-container-class="contracts-checkbox-actions-container"
                data-contract-id="{{ $contract->doc_id }}">
            <label for="listcheckbox-contracts-{{ $contract->doc_id }}"></label>
        </span>
    </td>
    @endif

    <!--doc_id-->
    <td class="col_doc_id">
        <a href="{{ url('/contracts/'.$contract->doc_id) }}">{{ runtimeContractIdFormat($contract->doc_id) }}</a>
    </td>

    <!--doc_date_start-->
    <td class="col_doc_date_start">
        {{ runtimeDate($contract->doc_date_start) }}
    </td>

    <!--client-->
    <td class="col_client">
        <a href="{{ url('/clients/'.$contract->client_id) }}"
            title="{{ $contract->client_company_name ?? '---' }}">{{ str_limit($contract->client_company_name ?? '---', 25) }}</a>
    </td>

    <!--doc_title-->
    <td class="col_doc_title">
        <a href="{{ url('/contract/'.$contract->doc_title) }}">{{ str_limit($contract->doc_title ?? '---', 20) }}</a>
    </td>

    <!--project-->
    <td class="col_client">
        <a href="{{ url('/projeccts/'.$contract->project_id) }}"
            title="{{ $contract->project_title ?? '---' }}">{{ str_limit($contract->project_title ?? '---', 25) }}</a>
    </td>

    @if(config('visibility.col_created_by'))
    <td class="col_created_by">
        <img src="{{ getUsersAvatar($contract->avatar_directory, $contract->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $contract->first_name ?? runtimeUnkownUser() }}
    </td>
    @endif

    <!--doc_date_end-->
    <td class="col_doc_date_start">
        {{ runtimeDate($contract->doc_date_end ?? '---') }}
    </td>

    <!--status-->
    <td class="col_foo">
        <span
            class="label {{ runtimeProposalStatusColors($contract->doc_status, 'label') }}">{{ runtimeLang($contract->doc_status) }}</span>
    </td>

    @if(config('visibility.contracts_col_action'))
    <td class="contracts_col_action actions_column" id="contracts_col_action_{{ $contract->doc_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_product')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/contracts/{{ $contract->doc_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <a type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm"
                href="{{ url('/contracts/'.$contract->doc_id.'/edit') }}">
                <i class="sl-icon-note"></i>
            </a>
            @endif
            <!--more button (team)-->
            @if(config('visibility.action_buttons_edit') == 'show')
            <span class="list-table-action dropdown font-size-inherit">
                <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" title="{{ cleanLang(__('lang.more')) }}"
                    class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                    <i class="ti-more"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="listTableAction">
                    <!--actions button - email client -->
                    <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                        data-confirm-title="{{ cleanLang(__('lang.email_to_client')) }}"
                        data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-url="{{ url('/contracts') }}/{{ $contract->doc_id }}/resend?ref=list">
                        {{ cleanLang(__('lang.email_to_client')) }}</a>
                    <!--actions button - change category-->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
                        data-url="{{ url('/contracts/change-category') }}"
                        data-action-url="{{ urlResource('/contracts/change-category?id='.$contract->doc_id) }}"
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