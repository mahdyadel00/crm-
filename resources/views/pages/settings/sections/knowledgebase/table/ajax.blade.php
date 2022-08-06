@foreach($categories as $category)
<!--each row-->
<tr id="category_{{ $category->kbcategory_id }}">
    <td class="category_col_name">
        <span class="mdi mdi-drag-vertical cursor-pointer"></span>
        {{ runtimeLang($category->kbcategory_title) }}
        <!--sorting data-->
        <input type="hidden" name="sort-categories[{{ $category->kbcategory_id }}]"
            value="{{ $category->kbcategory_id }}">
    </td>
    <td class="category_col_type">
        {{ runtimeLang($category->kbcategory_type) }}
    </td>
    <td class="category_col_articles">
        {{ $category->count_articles }}
    </td>
    <td class="category_col_visible_to">
        {{ runtimeLang($category->kbcategory_visibility) }}
    </td>
    <td class="category_col_created_by">
        <img src="{{ getUsersAvatar($category->avatar_directory, $category->avatar_filename, $category->kbcategory_creatorid) }}" alt="user"
            class="img-circle avatar-xsmall">
            {{ checkUsersName($category->first_name, $category->kbcategory_creatorid)  }}
    </td>

    <td class="category_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-tooltip  btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/settings/knowledgebase/'.$category->kbcategory_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_category')) }}"
                data-action-url="{{ url('/settings/knowledgebase/'.$category->kbcategory_id) }}"
                data-action-method="PUT" data-action-ajax-class=""
                data-action-ajax-loading-target="categories-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_category')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE"
                data-url="{{ url('/') }}/settings/knowledgebase/{{ $category->kbcategory_id }}">
                <i class="sl-icon-trash"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->