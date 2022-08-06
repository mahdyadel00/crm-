@foreach($templates as $template)
<!--each row-->
<tr id="webmail_template_{{ $template->webmail_template_id }}">
    <td class="template_col_date">
        {{ $template->webmail_template_name }}
    </td>
    <td class="template_col_name">{{ runtimeDate($template->webmail_template_created) }}</td>
    <td class="template_col_created_by">
        <img src="{{ getUsersAvatar($template->avatar_directory, $template->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $template->first_name }}
    </td>
    <td class="template_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--edit-->
            <button type="button"
                class="btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('settings/webmail/templates/'.$template->webmail_template_id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="@lang('lang.edit')" 
                data-modal-size="modal-xl"
                data-action-url="{{ urlResource('settings/webmail/templates/'.$template->webmail_template_id) }}" data-action-method="PUT"
                data-action-ajax-class="js-ajax-ux-request" data-action-ajax-loading-target="table-responsive">
                <i class="sl-icon-note"></i>
            </button>

            
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ urlResource('settings/webmail/templates/'.$template->webmail_template_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->