<div class="x-element x-action ajax-request x-element-info"
    data-url="{{ url('reminders/new?ref=card&resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&reminder_ref='.request('ref')) }}"
    data-loading-target="card-reminder-create-button" id="card-reminder-create-button"><i
        class="ti-alarm-clock m-t--4 p-r-6"></i>
    <span class="x-highlight"> @lang('lang.add_a_reminder')</span>
</div>
<div class="card-reminder card-reminder-create-container reminders-side-panel hidden"
    id="card-reminder-create-container">
    <div class="card-reminder-create-wrapper reminders-side-panel-body" id="card-reminder-create-wrapper">
        <!--dynamic-->
    </div>
    <input name="reminder_action" id="reminder_action" type="hidden">
</div>