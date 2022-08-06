@foreach($contacts as $contact)
<!--each row-->
<tr id="contact_{{ $contact->id }}">
    @if(config('visibility.contacts_col_checkboxes'))
    <td class="contacts_col_checkbox checkitem" id="contacts_col_checkbox_{{ $contact->id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-contacts-{{ $contact->id }}" name="ids[{{ $contact->id }}]"
                class="listcheckbox listcheckbox-contacts filled-in chk-col-light-blue"
                data-actions-container-class="contacts-checkbox-actions-container"
                {{ runtimeDisabledContactsChecboxes($contact->account_owner) }}>
            <label for="listcheckbox-contacts-{{ $contact->id }}"></label>
        </span>
    </td>
    @endif
    <td class="contacts_col_first_name" id="contacts_col_first_name_{{ $contact->id }}">
        <span class="user-avatar-container"><img src="{{ $contact->avatar }}" alt="user"
                class="img-circle avatar-xsmall">
            @if($contact->is_online)
            <span class="online-status bg-success" data-toggle="tooltip"
                title="{{ cleanLang(__('lang.user_is_online')) }}"></span>
            @endif
        </span> <span>{{ $contact->first_name }}</span>
        {{ $contact->last_name }}
        <!--account owner-->
        @if($contact->account_owner == 'yes')
        <span class="sl-icon-star text-warning p-l-5" data-toggle="tooltip"
            title="{{ cleanLang(__('lang.account_owner')) }}" id="account_owner_icon_{{ $contact->clientid }}"></span>
        @endif

    </td>
    @if(config('visibility.contacts_col_client'))
    <td class="contacts_col_company" id="contacts_col_company_{{ $contact->id }}">
        <a href="{{ url('/clients') }}/{{ $contact->clientid }}">{{ str_limit($contact->client_company_name, 15) }}</a>
    </td>
    @endif
    <td class="contacts_col_email" id="contacts_col_email_{{ $contact->id }}">
        {{ $contact->email }}
    </td>
    <td class="contacts_col_phone" id="contacts_col_phone_{{ $contact->id }}">{{ $contact->phone ?? '---'}}</td>
    @if(config('visibility.contacts_col_last_active'))
    <td class="contacts_col_last_active" id="contacts_col_last_active_{{ $contact->id }}">
        {{ $contact->carbon_last_seen }}
    </td>
    @endif
    @if(config('visibility.action_column'))
    <td class="contacts_col_action actions_column" id="contacts_col_action_{{ $contact->id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete') == 'show' && $contact->account_owner == 'no')
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_user')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/contacts/{{ $contact->id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-trash"></i></span>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/contacts/'.$contact->id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="{{ cleanLang(__('lang.edit_user')) }}"
                data-action-url="{{ urlResource('/contacts/'.$contact->id.'?ref=list') }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="contacts-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @endif

            <!--send email-->
            <button type="button" title="@lang('lang.send_email')"
                class="data-toggle-action-tooltip btn btn-outline-warning btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/appwebmail/compose?view=modal&resource_type=user&resource_id='.$contact->id) }}"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.send_email')"
                data-action-url="{{ url('/appwebmail/send') }}" data-action-method="POST"
                data-modal-size="modal-xl"
                data-action-ajax-loading-target="clients-td-container">
                <i class="ti-email display-inline-block m-t-3"></i>
            </button>

            <!--change password-->
            @if(config('visibility.action_buttons_change_password'))
            <button type="button" title="{{ cleanLang(__('lang.update_password')) }}"
                class="data-toggle-action-tooltip btn btn-outline-default btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/user/updatepassword?contact_id='.$contact->id) }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.update_password')) }}"
                data-action-url="{{ urlResource('/user/updatepassword') }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="contacts-td-container">
                <i class="sl-icon-lock"></i>
            </button>
            @endif

        </span>
        <!--action button-->
    </td>
    @endif
</tr>
@endforeach
<!--each row-->