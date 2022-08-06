@foreach($files as $file)
<!--each row-->
<tr id="file_{{ $file->file_id }}">
    @if(config('visibility.files_col_checkboxes'))
    <td class="files_col_checkbox checkitem" id="files_col_checkbox_{{ $file->file_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-files-{{ $file->file_id }}" name="ids[{{ $file->file_id }}]"
                class="listcheckbox listcheckbox-files filled-in chk-col-light-blue"
                data-actions-container-class="files-checkbox-actions-container">
            <label for="listcheckbox-files-{{ $file->file_id }}"></label>
        </span>
    </td>
    @endif
    <td class="files_col_file" id="files_col_file_{{ $file->file_id }}">
        @if($file->file_type == 'image')
        <!--dynamic inline style-->
        <div>
            <a class="fancybox preview-image-thumb"
                href="storage/files/{{ $file->file_directory }}/{{ $file->file_filename  }}"
                title="{{ str_limit($file->file_filename, 60) }}" alt="{{ str_limit($file->file_filename, 60) }}">
                <img class="lists-table-thumb"
                    src="{{ url('storage/files/' . $file->file_directory .'/'. $file->file_thumbname) }}">
            </a>
        </div>
        @else
        <div class="lists-table-thumb">
            <a class="preview-image-thumb" href="files/download?file_id={{ $file->file_uniqueid }}" download>
                {{ $file->file_extension }}
            </a>
        </div>
        @endif
    </td>
    <td class="files_col_file_name" id="files_col_file_name_{{ $file->file_id }}">
        <a href="files/download?file_id={{ $file->file_uniqueid }}" title="{{ $file->file_filename ?? '---' }}"
            download>{{ str_limit($file->file_filename ?? '---', 70) }}</a>
    </td>
    <td class="files_col_added_by" id="files_col_added_by_{{ $file->file_id }}">
        <img src="{{ getUsersAvatar($file->avatar_directory, $file->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $file->first_name ?? runtimeUnkownUser() }}
    </td>
    <td class="files_col_size" id="files_col_size_{{ $file->file_id }}">{{ $file->file_size }}</td>
    <td class="files_col_date" id="files_col_date_{{ $file->file_id }}">
        {{ runtimeDate($file->file_created) }}
    </td>
    @if(config('visibility.files_col_visibility'))
    <td class="files_col_visible_to_client" id="files_col_visible_to_client_{{ $file->file_id }}">
        <div class="switch" id="file_edit_visibility_{{ $file->file_id }}">
            <label>
                <input {{ runtimePrechecked($file['file_visibility_client'] ?? '') }} type="checkbox"
                    class="js-ajax-ux-request-default" name="visible_to_client"
                    id="visible_to_client_{{ $file->file_id }}" data-url="{{ url('/files') }}/{{ $file->file_id }}"
                    data-ajax-type="PUT" data-type="form" data-form-id="file_edit_visibility_{{ $file->file_id }}"
                    data-progress-bar='hidden'>
                <span class="lever switch-col-light-blue"></span>
            </label>
        </div>
    </td>
    @endif
    <td class="files_col_action actions_column" id="files_col_action_{{ $file->file_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--edit-->
            @if($file->permission_edit_file)
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm actions-modal-button edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#actionsModal"
                data-url="{{ urlResource('/files/'.$file->file_id.'/edit') }}" data-loading-target="actionsModalBody"
                data-modal-title="{{ cleanLang(__('lang.edit_file')) }}"
                data-action-url="{{ urlResource('/files/'.$file->file_id.'/rename') }}" data-action-method="POST"
                data-action-ajax-class="" data-action-ajax-loading-target="files-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled  {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="@lang('lang.actions_not_available')"><i class="sl-icon-note"></i></span>

            @endif
            @if($file->permission_delete_file)
            <button type="button" title="@lang('lang.delete')"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.delete_file')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('/') }}/files/{{ $file->file_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled  {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="@lang('lang.actions_not_available')"><i class="sl-icon-trash"></i></span>
            @endif
            <a href="files/download?file_id={{ $file->file_uniqueid }}" title="@lang('lang.download')"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm" download>
                <i class="ti-download"></i>
            </a>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->