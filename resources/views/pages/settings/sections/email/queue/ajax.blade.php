@foreach($emails as $email)
<!--each row-->
<tr class="settings-each-email" id="email_{{ $email->emailqueue_id }}">
    <td class="emails_col_emailqueue_created">
        {{ runtimeDate($email->emailqueue_created) }}
    </td>
    <td class="emails_col_emailqueue_to">
        {{ $email->emailqueue_to }}
    </td>
    <td class="emails_col_emailqueue_subject">
        {{ $email->emailqueue_subject }}
    </td>
    <td class="emails_col_emailqueue_status">
        {{ runtimeLang($email->emailqueue_status) }}
    </td>
    <td class="emails_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('settings/email/queue/'.$email->emailqueue_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--view email-->
            <button type="button"
                class="btn btn-outline-success btn-circle btn-sm data-toggle-action-tooltip edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                title="{{ cleanLang(__('lang.view')) }}"
                data-toggle="modal" data-target="#commonModal"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.to'): {{ $email->emailqueue_to ?? '---' }}"
                data-action-type="" data-action-form-id=""
                data-footer-visibility="hidden"
                data-url="{{ url('settings/email/queue/'.$email->emailqueue_id) }}">
                <i class="ti-book"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->