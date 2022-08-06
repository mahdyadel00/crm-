<!--each checklist-->
@foreach($checklists as $checklist)
<div class="checklist-item clearfix" id="lead_checklist_container_{{ $checklist->checklist_id }}"
    data-id="{{ $checklist->checklist_id }}">
    <input type="checkbox" class="filled-in chk-col-light-blue js-ajax-ux-request-default" name="card_checklist"
        data-url="{{ urlResource('/leads/toggle-checklist-status/'.$checklist->checklist_id) }}" data-ajax-type="post"
        data-type="form" data-form-id="lead_checklist_container_{{ $checklist->checklist_id }}" data-notifications="disabled"
        id="lead_checklist_{{ $checklist->checklist_id }}" 
        {{ runtimeChecklistCheckbox($checklist->permission_edit_delete_checklist) }}
        {{ runtimePrechecked($checklist->checklist_status) }}>
    <label class="checklist-label" for="lead_checklist_{{ $checklist->checklist_id }}"></label>
    <span class="checklist-text {{ runtimePermissions('lead-edit-checklist', $checklist->permission_edit_delete_checklist) }}" data-toggle="edit" data-id="{{ $checklist->checklist_id }}"
        data-action-url="{{ url('/leads/update-checklist/'.$checklist->checklist_id) }}">{{ $checklist->checklist_text}}</span>
    <!--delete action-->
    @if($checklist->permission_edit_delete_checklist)
    <a href="javascript:void(0)" class="x-action-delete checklist-item-delete hidden js-delete-ux js-ajax-ux-request"
        data-ajax-type="DELETE" data-parent-container="lead_checklist_container_{{ $checklist->checklist_id }}"
        data-progress-bar="hidden" data-url="{{ url('/leads/delete-checklist/'.$checklist->checklist_id) }}"><i
            class="mdi mdi-delete text-danger"></i></a>
   @endif
</div>
@endforeach