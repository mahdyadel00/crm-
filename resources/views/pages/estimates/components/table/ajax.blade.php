@foreach($estimates as $estimate)
<!--each row-->
<tr id="estimate_{{ $estimate->bill_estimateid }}">
    @if(config('visibility.estimates_col_checkboxes'))
    <td class="estimates_col_checkbox checkitem" id="estimates_col_checkbox_{{ $estimate->bill_estimateid }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-estimates-{{ $estimate->bill_estimateid }}"
                name="ids[{{ $estimate->bill_estimateid }}]"
                class="listcheckbox listcheckbox-estimates filled-in chk-col-light-blue"
                data-actions-container-class="estimates-checkbox-actions-container">
            <label for="listcheckbox-estimates-{{ $estimate->bill_estimateid }}"></label>
        </span>
    </td>
    @endif
    <td class="estimates_col_id" id="estimates_col_id_{{ $estimate->bill_estimateid }}">
        <a href="/estimates/{{ $estimate->bill_estimateid }}">{{ $estimate->formatted_bill_estimateid }}</a>
    </td>
    <td class="estimates_col_date" id="estimates_col_date_{{ $estimate->bill_estimateid }}">
        {{ runtimeDate($estimate->bill_date) }}
    </td>
    @if(config('visibility.estimates_col_client'))
    <td class="estimates_col_company" id="estimates_col_company_{{ $estimate->bill_estimateid }}">
        <a href="/clients/{{ $estimate->bill_clientid }}">
            {{ str_limit($estimate->client_company_name ?? '---', 30) }}</a>
    </td>
    @endif
    @if(config('visibility.estimates_col_created_by'))
    <td class="estimates_col_created_by" id="estimates_col_created_by_{{ $estimate->bill_estimateid }}">
        <img src="{{ getUsersAvatar($estimate->avatar_directory, $estimate->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $estimate->first_name ?? runtimeUnkownUser() }}
    </td>
    @endif
    @if(config('visibility.estimates_col_expires'))
    <td class="estimates_col_expires" id="estimates_col_expires_{{ $estimate->bill_estimateid }}">
        {{ runtimeDate($estimate->bill_expiry_date) }}</td>
    @endif

    @if(config('visibility.estimates_col_tags'))
    <td class="estimates_col_tags" id="estimates_col_tags_{{ $estimate->bill_estimateid }}">
        <!--tag-->
        @if(count($estimate->tags) > 0)
        <span class="label label-outline-default">{{ str_limit($estimate->tags->first()->tag_title, 15) }}</span>
        @else
        <span>---</span>
        @endif
        <!--/#tag-->

        <!--more tags-->
        @if(count($estimate->tags) > 1)
        @php $tags = $estimate->tags; @endphp
        @include('misc.more-tags')
        @endif
        <!--more tags-->
    </td>
    @endif
    <td class="estimates_col_amount" id="estimates_col_amount_{{ $estimate->bill_estimateid }}">
        {{ runtimeMoneyFormat($estimate->bill_final_amount) }}
    </td>
    <td class="estimates_col_status" id="estimates_col_status_{{ $estimate->bill_estimateid }}">
        <span class="label {{ runtimeEstimateStatusColors($estimate->bill_status, 'label') }}">{{
            runtimeLang($estimate->bill_status) }}</span>


        @if(config('system.settings_estimates_show_view_status') == 'yes' && auth()->user()->is_team && ($estimate->bill_status == 'new' || $estimate->bill_status == 'revised'))
        <!--estimate not viewed-->
        @if($estimate->bill_viewed_by_client == 'no')
        <span class="label label-icons label-icons-default" data-toggle="tooltip" data-placement="top"
            title="@lang('lang.client_has_not_opened')"><i class="sl-icon-eye"></i></span>
        @endif
        <!--estimate viewed-->
        @if($estimate->bill_viewed_by_client == 'yes')
        <span class="label label-icons label-icons-info" data-toggle="tooltip" data-placement="top"
            title="@lang('lang.client_has_opened')"><i class="sl-icon-eye"></i></span>
        @endif
        @endif
    </td>
    <td class="estimates_col_action actions_column" id="estimates_col_action_{{ $estimate->bill_estimateid }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/estimates/{{ $estimate->bill_estimateid }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <a href="/estimates/{{ $estimate->bill_estimateid }}/edit-estimate" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="sl-icon-note"></i>
            </a>
            @endif
            <a href="/estimates/{{ $estimate->bill_estimateid }}" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->

        <!--more button (team)-->
        @if(config('visibility.action_buttons_edit') == 'show')
        <span class="list-table-action dropdown  font-size-inherit">
            <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                title="{{ cleanLang(__('lang.more')) }}" title="{{ cleanLang(__('lang.more')) }}"
                class="data-toggle-tooltip data-toggle-tooltip btn btn-outline-default-light btn-circle btn-sm">
                <i class="ti-more"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="listTableAction">
                <!--actions button - email client -->
                <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                    data-confirm-title="{{ cleanLang(__('lang.email_to_client')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                    data-url="{{ url('/estimates') }}/{{ $estimate->bill_estimateid }}/resend?ref=list">
                    {{ cleanLang(__('lang.email_to_client')) }}</a>
                <!--actions button - change category-->
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                    data-modal-title="{{ cleanLang(__('lang.change_status')) }}"
                    data-url="{{ urlResource('/estimates/'.$estimate->bill_estimateid.'/change-status') }}"
                    data-action-url="{{ urlResource('/estimates/'.$estimate->bill_estimateid.'/change-status') }}"
                    data-loading-target="actionsModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.change_status')) }}</a>
                <!--actions button - change category-->
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                    data-modal-title="{{ cleanLang(__('lang.change_category')) }}"
                    data-url="{{ url('/estimates/change-category') }}"
                    data-action-url="{{ urlResource('/estimates/change-category?id='.$estimate->bill_estimateid) }}"
                    data-loading-target="actionsModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.change_category')) }}</a>
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-modal-title="{{ cleanLang(__('lang.convert_to_invoice')) }}"
                    data-url="{{ urlResource('/estimates/'.$estimate->bill_estimateid.'/convert-to-invoice') }}"
                    data-action-url="{{ urlResource('/estimates/'.$estimate->bill_estimateid.'/convert-to-invoice') }}"
                    data-loading-target="commonModalBody" data-action-method="POST">
                    {{ cleanLang(__('lang.convert_to_invoice')) }}</a>
            </div>
        </span>
        @endif
        <!--more button-->

    </td>
</tr>
@endforeach
<!--each row-->