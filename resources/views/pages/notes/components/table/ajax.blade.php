@foreach($notes as $note)
<!--each row-->
<tr id="note_{{ $note->note_id }}">
    @if(config('visibility.notes_col_checkboxes'))
    <td class="notes_col_checkbox checkitem" id="notes_col_checkbox_{{ $note->note_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-notes-{{ $note->note_id }}" name="ids[{{ $note->note_id }}]"
                class="listcheckbox listcheckbox-notes filled-in chk-col-light-blue"
                data-actions-container-class="notes-checkbox-actions-container">
            <label for="listcheckbox-notes-{{ $note->note_id }}"></label>
        </span>
    </td>
    @endif
    <td class="notes_col_added">
        <img src="{{ getUsersAvatar($note->avatar_directory, $note->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $note->first_name ?? runtimeUnkownUser() }}
    </td>
    <td class="notes_col_title">
        <a href="javascript:void(0)" class="show-modal-button js-ajax-ux-request" data-toggle="modal"
            data-url="{{ url('/') }}/notes/{{  $note->note_id }}" data-target="#plainModal"
            data-loading-target="plainModalBody" data-modal-title=" ">
            {{ str_limit($note->note_title, 65) }}
        </a>
    </td>
    <td class="notes_col_tags">
        <!--tag-->
        @if(count($note->tags) > 0)
        @foreach($note->tags->take(2) as $tag)
        <span class="label label-outline-default">{{ str_limit($tag->tag_title, 15) }}</span>
        @endforeach
        @else
        <span>---</span>
        @endif
        <!--/#tag-->

        <!--more tags (greater than tags->take(x) number above -->
        @if(count($note->tags) > 1)
        @php $tags = $note->tags; @endphp
        @include('misc.more-tags')
        @endif
        <!--more tags-->
    </td>

    <td class="notes_col_date {{ $page[ 'visibility_col_date'] ?? '' }} ">{{ runtimeDate($note->note_created) }}
    </td>
    </td>
    <td class="notes_col_action  actions_column {{ $page[ 'visibility_col_action'] ?? '' }} ">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            @if($note->permission_edit_delete_note)
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_note')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE" data-url="{{ url( '/') }}/notes/{{  $note->note_id }} ">
                <i class="sl-icon-trash"></i>
            </button>
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/notes/'.$note->note_id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="{{ cleanLang(__('lang.edit_note')) }}"
                data-action-url="{{ urlResource('/notes/'.$note->note_id.'?ref=list') }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="notes-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @else
            <span class="btn btn-outline-default btn-circle btn-sm disabled  {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i class="sl-icon-trash"></i></span>
            <span class="btn btn-outline-default btn-circle btn-sm disabled  {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i class="sl-icon-note"></i></span>
            @endif
            <a href="javascript:void(0)" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm show-modal-button js-ajax-ux-request"
                data-toggle="modal" data-url="{{ url( '/') }}/notes/{{  $note->note_id }} " data-target="#plainModal"
                data-loading-target="plainModalBody" data-modal-title="">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->