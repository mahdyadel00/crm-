@foreach($leads as $lead)
<!--each row-->
<tr id="lead_{{ $lead->lead_id }}">
    @if(config('visibility.leads_col_checkboxes'))
    <td class="leads_col_checkbox checkitem" id="leads_col_checkbox_{{ $lead->lead_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-leads-{{ $lead->lead_id }}"
                name="ids[{{ $lead->lead_id }}]"
                class="listcheckbox listcheckbox-leads filled-in chk-col-light-blue"
                data-actions-container-class="leads-checkbox-actions-container">
            <label for="listcheckbox-leads-{{ $lead->lead_id }}"></label>
        </span>
    </td>
    @endif
    <td class="leads_col_title" id="leads_col_title_{{ $lead->lead_id }}">
        <a class="show-modal-button reset-card-modal-form js-ajax-ux-request" data-toggle="modal"
            href="javascript:void(0)" data-target="#cardModal" data-url="{{ urlResource('/leads/'.$lead->lead_id) }}"
            data-loading-target="main-top-nav-bar" id="table_lead_title_{{ $lead->lead_id }}">
            {{ str_limit($lead->lead_title, 20) }}</a>
    </td>
    <td class="leads_col_contact" id="leads_col_contact_{{ $lead->lead_id }}">
        {{ str_limit($lead->full_name, 15) }}
    </td>
    <td class="leads_col_date" id="leads_col_date_{{ $lead->lead_id }}">
        {{ runtimeDate($lead->lead_created) }}
    </td>
    <td class="leads_col_company" id="leads_col_company_{{ $lead->lead_id }}">
        {{ str_limit($lead->category_name ?? '---', 15) }}
    </td>
    <td class="leads_col_assigned" id="leads_col_assigned_{{ $lead->lead_id }}">
        <!--assigned users-->
        @if(count($lead->assigned) > 0)
        @foreach($lead->assigned->take(2) as $user)
        <img src="{{ $user->avatar }}" data-toggle="tooltip" title="{{ $user->first_name }}" data-placement="top"
            alt="{{ $user->first_name }}" class="img-circle avatar-xsmall">
        @endforeach
        @else
        <span>---</span>
        @endif
        <!--assigned users-->
        <!--more users-->
        @if(count($lead->assigned) > 2)
        @php $more_users_title = __('lang.assigned_users'); $users = $lead->assigned; @endphp
        @include('misc.more-users')
        @endif
        <!--more users-->
    </td>
    <td class="leads_col_stage" id="leads_col_stage_{{ $lead->lead_id }}">
        <span class="label {{ bootstrapColors($lead->leadstatus->leadstatus_color ?? '', 'label') }}">
            <!--notes: alternatve lang for lead status will need to be added manally by end user in lang files-->
            {{ runtimeLang($lead->leadstatus->leadstatus_title ?? '') }}</span>

            <!--archived-->
        @if($lead->lead_active_state == 'archived' && runtimeArchivingOptions())
        <span class="label label-icons label-icons-default" data-toggle="tooltip" data-placement="top"
            title="@lang('lang.archived')"><i class="ti-archive"></i></span>
        @endif
    </td>
    <td class="leads_col_value" id="leads_col_value_{{ $lead->lead_id }}">
        {{ runtimeMoneyFormat($lead->lead_value) }}
    </td>
    <td class="leads_col_action actions_column" id="leads_col_action_{{ $lead->lead_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            @if(config('visibility.action_buttons_delete'))
            <!--[delete]-->
            @if($lead->permission_delete_lead)
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/leads/{{ $lead->lead_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled  {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-trash"></i></span>
            @endif
            @endif
            <!--view-->
            <button type="button" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm show-modal-button reset-card-modal-form js-ajax-ux-request"
                data-toggle="modal" data-target="#cardModal" data-url="{{ urlResource('/leads/'.$lead->lead_id) }}"
                data-loading-target="main-top-nav-bar">
                <i class="ti-new-window"></i>
            </button>
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
                <!--change category-->
                @if($lead->permission_edit_lead)
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                    data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
                    data-url="{{ url('/leads/change-category') }}"
                    data-action-url="{{ urlResource('/leads/change-category?id='.$lead->lead_id) }}"
                    data-loading-target="actionsModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.change_category')) }}</a>
                <!--change status-->
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                    data-modal-title="{{ cleanLang(__('lang.change_status')) }}"
                    data-url="{{ urlResource('/leads/'.$lead->lead_id.'/change-status') }}"
                    data-action-url="{{ urlResource('/leads/'.$lead->lead_id.'/change-status') }}"
                    data-loading-target="actionsModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.change_status')) }}</a>

                <!--archive-->
                @if($lead->lead_active_state == 'active' && runtimeArchivingOptions())
                <a class="dropdown-item confirm-action-info"
                    data-confirm-title="{{ cleanLang(__('lang.archive_lead')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ urlResource('/leads/'.$lead->lead_id.'/archive') }}">
                    {{ cleanLang(__('lang.archive')) }}
                </a>
                @endif

                <!--activate-->
                @if($lead->lead_active_state == 'archived' && runtimeArchivingOptions())
                <a class="dropdown-item confirm-action-info"
                    data-confirm-title="{{ cleanLang(__('lang.restore_lead')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ urlResource('/leads/'.$lead->lead_id.'/activate') }}">
                    {{ cleanLang(__('lang.restore')) }}
                </a>
                @endif


                @else
                <span class="small">--- no options avaiable</span>
                @endif
            </div>
        </span>
        @endif
        <!--more button-->
    </td>
</tr>
@endforeach
<!--each row-->