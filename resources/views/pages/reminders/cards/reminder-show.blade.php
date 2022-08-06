 <!--active reminder-->
 <div class="card-a-reminder {{ $reminder->reminder_status }}" id="">
     <div class="x-top clearfix">
        <div class="x-edit-icon" id="card-a-reminder-edit-button"><i class="mdi mdi-pencil-circle"></i></div>
         <div class="x-icon"><i class="ti-alarm-clock m-t--4 p-r-6"></i></div>
         <div class="x-content">
             <div class="x-time">{{ runtimeTime($reminder->reminder_datetime) }}</div>
             <div class="x-date">{{ runtimeDate($reminder->reminder_datetime) }}</div>
         </div>
     </div>
     <div class="x-title">{{ $reminder->reminder_title }}</div>
     <div class="x-buttons hidden" id="card-a-reminder-buttons">
         <button type="button" class="btn btn-rounded-x btn-default btn-xs ajax-request" 
             data-loading-class="loading-before-centre"
             data-loading-target="card-reminders-container"
             data-url="{{ url('reminders/edit?ref=card&resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&reminder_id='.$reminder->reminder_id) }}"
             id="card-a-reminder-button-see-notes">@lang('lang.edit')</button>
         <button type="button" class="btn btn-rounded-x btn-danger btn-xs ajax-request"   
             data-loading-class="loading-before-centre"
             data-loading-target="card-reminders-container"
             data-url="{{ url('reminders/delete?ref=card&resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&reminder_id='.$reminder->reminder_id) }}"
             id="card-a-reminder-button-delete">@lang('lang.delete')</button>
     </div>
 </div>