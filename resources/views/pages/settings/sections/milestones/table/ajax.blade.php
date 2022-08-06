@foreach($milestones as $milestone)
<!--each row-->
<tr id="milestone_{{ $milestone->milestonecategory_id }}">
    <td class="milestones_col_name">
        <span class="mdi mdi-drag-vertical cursor-pointer"></span>
        {{ runtimeLang($milestone->milestonecategory_title) }}
        <!--sorting data-->
        <input type="hidden" name="sort-milestones[{{ $milestone->milestonecategory_id }}]"
            value="{{ $milestone->milestonecategory_id }}">
    </td>
    <td class="milestones_col_date">
        {{ runtimeDate($milestone->milestonecategory_created) }}
    </td>
    <td class="milestone_col_created_by">
        <img src="{{ getUsersAvatar($milestone->avatar_directory, $milestone->avatar_filename, $milestone->milestonecategory_creatorid) }}" alt="user"
            class="img-circle avatar-xsmall">
            {{ checkUsersName($milestone->first_name, $milestone->milestonecategory_creatorid)  }}
    </td>

    <td class="milestone_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-tooltip  btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('/settings/milestones/'.$milestone->milestonecategory_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_milestone')) }}"
                data-action-url="{{ url('/settings/milestones/'.$milestone->milestonecategory_id) }}"
                data-action-method="PUT" data-action-ajax-class=""
                data-action-ajax-loading-target="milestone-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_milestone')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE"
                data-url="{{ url('/') }}/settings/milestones/{{ $milestone->milestonecategory_id }}">
                <i class="sl-icon-trash"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->