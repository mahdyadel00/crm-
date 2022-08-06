@foreach($categories as $category)
<!--each row-->
<tr id="category_{{ $category->category_id }}">
    <td class="categories_col_name">
        {{ str_limit($category->category_name ?? '---', 60) }}
        <!--default-->
        @if($category->category_system_default == 'yes')
        <span class="sl-icon-star text-warning p-l-5" data-toggle="tooltip"
            title="{{ cleanLang(__('lang.system_default')) }}"></span>
        @endif
    </td>
    <td class="categories_col_created_by">
        <img src="{{ getUsersAvatar($category->avatar_directory, $category->avatar_filename, $category->category_creatorid) }}"
            alt="user" class="img-circle avatar-xsmall">
        {{ checkUsersName($category->first_name, $category->category_creatorid)  }}
    </td>
    <td class="categories_col_date">
        {{ runtimeDate($category->category_created) }}
    </td>
    <td class="categories_col_items">{{ $category->count }}</td>
    <td class="categories_col_status">
        @if($category->category_system_default == 'yes')
        <span class="label label-outline-default">{{ cleanLang(__('lang.default')) }}</span>
        @else
        ---
        @endif
    </td>
    @if(request('filter_category_type')=='project')
    <td class="categories_col_team" id="category_user_count_{{ $category->category_id }}">{{ $category->count_users }}
    </td>
    @endif
    <td class="categories_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            @if($category->category_system_default == 'no')
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/categories/{{ $category->category_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @else
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled {{ runtimePlaceholdeActionsButtons() }}"
                data-toggle="tooltip" title="{{ cleanLang(__('lang.actions_not_available')) }}"><i
                    class="sl-icon-trash"></i></span>
            @endif
            <!--edit-->
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/categories/'.$category->category_id.'/edit?filter_category_type='.$category->category_type) }}"
                data-loading-target="commonModalBody" data-modal-title="{{ $page['edit_modal_action_title'] ?? '' }}"
                data-action-url="{{ url('/categories/'.$category->category_id.'?filter_category_type='.$category->category_type) }}"
                data-action-method="PUT" data-action-ajax-class=""
                data-action-ajax-loading-target="categories-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <!--team members-->
            @if(request('filter_category_type')=='project')
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-warning btn-circle btn-sm edit-add-modal-button  js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/categories/'.$category->category_id.'/team') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ $page['edit_team_members'] ?? '' }}"
                data-action-url="{{ url('/categories/'.$category->category_id.'/team') }}" data-action-method="put"
                data-action-ajax-class="" data-action-ajax-loading-target="categories-td-container">
                <i class="sl-icon-people"></i>
            </button>
            @endif
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->