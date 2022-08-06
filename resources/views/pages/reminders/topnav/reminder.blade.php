@foreach($reminders as $reminder)
<!--each reminder-->
<div class="topnav-reminder clearfix" id="topnav_reminder_{{ $reminder->reminder_id }}">
    <div class="x-icon">
        @if($reminder->reminderresource_type == 'client')
        <i class="sl-icon-people"></i>
        @endif
        @if($reminder->reminderresource_type == 'project')
        <i class="ti-folder"></i>
        @endif
        @if($reminder->reminderresource_type == 'invoice')
        <i class="ti-wallet"></i>
        @endif
        @if($reminder->reminderresource_type == 'estimate')
        <i class="ti-wallet"></i>
        @endif
        @if($reminder->reminderresource_type == 'task')
        <i class="ti-menu-alt"></i>
        @endif
        @if($reminder->reminderresource_type == 'lead')
        <i class="sl-icon-call-in"></i>
        @endif
        @if($reminder->reminderresource_type == 'ticket')
        <i class="ti-comments"></i>
        @endif
    </div>
    <div class="x-content">
        <div class="x-date-time clearfix">
            <div class="x-time">{{ runtimeTime($reminder->reminder_datetime) }}</div>
            <div class="x-date"><span>{{ runtimeDate($reminder->reminder_datetime) }}</span>
                <span class="js-reminder-mark-read-single"
                    data-container="topnav_reminder_{{ $reminder->reminder_id }}" data-progress-bar='hidden'
                    data-url="{{ url('reminders/'.$reminder->reminder_id.'/delete-reminder') }}">
                    <input class="radio-col-info" name="group4" type="radio"
                        id="reminder_checkbox_{{ $reminder->reminder_id }}">
                    <label for="reminder_checkbox_{{ $reminder->reminder_id }}"></label></span>
            </div>
        </div>
        <div class="x-title">
            {{ $reminder->reminder_title }}
        </div>
        <div class="x-link">
            @if($reminder->reminderresource_type == 'client')
            <a
                href="{{ url('clients/'.$reminder->reminderresource_id) }}">{{ str_limit($reminder->reminder_meta ?? __('lang.client'), 33) }}</a>
            @endif
            @if($reminder->reminderresource_type == 'project')
            <a
                href="{{ url('projects/'.$reminder->reminderresource_id) }}">{{ str_limit($reminder->reminder_meta ?? __('lang.project'), 33) }}</a>
            @endif
            @if($reminder->reminderresource_type == 'invoice')
            <a
                href="{{ url('invoices/'.$reminder->reminderresource_id) }}">{{ str_limit($reminder->reminder_meta ?? __('lang.invoice'), 33) }}</a>
            @endif
            @if($reminder->reminderresource_type == 'estimate')
            <a
                href="{{ url('estmates/'.$reminder->reminderresource_id) }}">{{ str_limit($reminder->reminder_meta ?? __('lang.estimate'), 33) }}</a>
            @endif
            @if($reminder->reminderresource_type == 'task')
            <a
                href="{{ url('tasks/v/'.$reminder->reminderresource_id.'/view') }}">{{ str_limit($reminder->reminder_meta ?? __('lang.task'), 33) }}</a>
            @endif
            @if($reminder->reminderresource_type == 'lead')
            <a
                href="{{ url('leads/v/'.$reminder->reminderresource_id.'/view') }}">{{ str_limit($reminder->reminder_meta ?? __('lang.lead'), 33) }}</a>
            @endif
            @if($reminder->reminderresource_type == 'ticket')
            <a
                href="{{ url('tickets/'.$reminder->reminderresource_id) }}">{{ str_limit($reminder->reminder_meta ?? __('lang.ticket'), 33) }}</a>
            @endif
        </div>
    </div>
</div>
@endforeach