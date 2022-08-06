<!--no reminder-->
<div class="reminders-existing-show {{ $reminder->reminder_status }}" id="reminders-existing-show">
   
    <div class="x-splash"><i class="ti-alarm-clock"></i></div>
    <div class="x-time">{{ runtimeTime($reminder->reminder_datetime) }}</div>
    <div class="x-date">{{ runtimeDate($reminder->reminder_datetime) }}</div>
    <div class="x-title">{{ $reminder->reminder_title }}</div>
    @if($reminder->reminder_notes != '')
    <div class="x-notes">{{ $reminder->reminder_notes }}</div>
    @endif
    <div class="x-buttons">
        <button type="button" class="btn btn-rounded-x btn-default btn-sm ajax-request" 
            data-loading-class="loading-before-centre"
            data-loading-target="card-reminders-container"
            data-url="{{ url('reminders/edit?&resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&reminder_id='.$reminder->reminder_id) }}"
            id="card-a-reminder-button-see-notes">@lang('lang.edit')</button>
        <button type="button" class="btn btn-rounded-x btn-danger btn-sm ajax-request"  
            data-loading-class="loading-before-centre"
            data-loading-target="card-reminders-container"
            data-url="{{ url('reminders/delete?resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&reminder_id='.$reminder->reminder_id) }}"
            id="card-a-reminder-button-delete">@lang('lang.delete')</button>
    </div>
</div>