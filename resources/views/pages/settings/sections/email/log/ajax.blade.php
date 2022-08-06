@foreach($emails as $email)
<!--each row-->
<tr class="settings-each-email" id="email_{{ $email->emaillog_id }}">
    <td class="emails_col_emaillog_created">
        {{ runtimeDate($email->emaillog_created) }}
    </td>
    <td class="emails_col_emaillog_to">
        {{ $email->emaillog_email }}
    </td>
    <td class="emails_col_emaillog_subject">
        {{ $email->emaillog_subject }}
    </td>
    <td class="emails_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('settings/email/log/'.$email->emaillog_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--view email-->
            <button type="button"
                class="btn btn-outline-success btn-circle btn-sm data-toggle-action-tooltip edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                title="{{ cleanLang(__('lang.view')) }}"
                data-toggle="modal" data-target="#commonModal"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.to'): {{ $email->emaillog_email ?? '---' }}"
                data-action-type="" data-action-form-id=""
                data-footer-visibility="hidden"
                data-url="{{ url('settings/email/log/'.$email->emaillog_id) }}">
                <i class="ti-book"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->