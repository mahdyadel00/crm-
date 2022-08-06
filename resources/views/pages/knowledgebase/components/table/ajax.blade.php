@foreach($knowledgebase as $kb)
<!--each row-->
<tr id="knowledgebase_{{ $kb->knowledgebase_id }}">
    <td class="knowledgebase_col_title"><a
            href="{{ url('/') }}/kb/article/{{ $kb->knowledgebase_slug }}">{{ $kb->knowledgebase_title }}</a></td>

    @if(auth()->user()->role->role_knowledgebase >= 2)
    <td class="knowledgebase_col_action actions_column w-px-80">
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="btn btn-outline-default-light btn-circle btn-sm">
                <i class="ti-more"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="listTableAction">
                <!--edit-->
                @if(config('visibility.action_buttons_edit'))
                <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                    data-toggle="modal" data-target="#commonModal"
                    data-url="{{ url('/kb/'.$kb->knowledgebase_id.'/edit?source='.request('source')) }}"
                    data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_article')) }}"
                    data-action-url="{{ url('/kb/'.$kb->knowledgebase_id) }}" data-action-method="PUT"
                    data-action-ajax-class=""
                    data-action-ajax-loading-target="knowledgebase-td-container">
                    {{ cleanLang(__('lang.edit')) }}
                </a>
                @endif
                <!--delete-->
                @if(config('visibility.action_buttons_delete'))
                <a class="dropdown-item actions-modal-button  confirm-action-danger"
                    data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                    data-ajax-type="DELETE" data-url="{{ url('/') }}/kb/{{ $kb->knowledgebase_id }}">
                    {{ cleanLang(__('lang.delete')) }}
                </a>
                @endif
            </div>
        </span>
    </td>
    @endif
</tr>
@endforeach
<!--each row-->