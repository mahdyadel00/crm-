<!--existing & new reminder-->
<div id="reminders-existing-new">
    <!--title-->
    <div class="filter-block">
        <div class="fields">
            <div class="row">
                <div class="col-md-12">
                    <input type="text" class="form-control form-control-sm" autocomplete="off" id="reminder_title"
                        name="reminder_title" placeholder="@lang('lang.reminder_title')"
                        value="{{ $payload['reminder_title'] ?? ''}}">
                </div>
            </div>
        </div>
    </div>

    <!--date and time picker-->
    <div style="overflow:hidden;">
        <div class="form-group  m-b-0">
            <div class="row">
                <div class="col-12">
                    <div id="reminders-datetimepicker" data-preset-date="{{ $payload['preset_date'] }}"></div>
                </div>
            </div>
        </div>
    </div>

    <!--title-->
    <div class="filter-block m-b-0 x-reminder-notes">
        <div class="fields">
            <div class="row">
                <div class="col-md-12">
                    <textarea class="form-control form-control-sm " rows="3" name="reminder_notes" id="reminder_notes"
                        placeholder="@lang('lang.reminder_notes')">{{ $payload['reminder_notes']?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!--data-->
    <input type="hidden" name="reminder_datetime" id="reminder_datetime" value="{{ $payload['preset_date'] }}">
    <input type="hidden" name="reminder_action" id="reminder_action">

    <!--buttons-->
    <div class="buttons-block  p-b-0 p-t-0">
        <!--close button (task/lead cards only-->
        @if(request('ref') =='card')
        <button type="button" class="btn btn-rounded-x btn-default btn-sm ajax-request" id="close_reminder_button" data-loading-class="loading-before-centre"
            data-loading-target="reminders-datetimepicker"
            data-url="{{ url('reminders/close?resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&ref='.request('ref')) }}">{{ cleanLang(__('lang.close')) }}</button>
        @endif
        <!--delete button-->
        @if($payload['show_delete_button'])
        <button type="button" class="btn btn-rounded-x btn-danger btn-sm js-ajax-ux-request" name="delete_reminder" id="delete_reminder"
            data-url="{{ url('reminders/delete?resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&ref='.request('ref').'&reminder_id='.$reminder->reminder_id) }}"
            data-loading-class="loading-before"
            >{{ cleanLang(__('lang.delete')) }}</button>
        @endif
        <!--save button-->
        <button type="button" class="btn btn-rounded-x btn-info btn-sm js-ajax-ux-request"
            data-url="{{ url('reminders/new?resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id'].'&ref='.request('ref')) }}"
            data-type="form" data-form-id="reminders-existing-new" data-loading-class="loading-before"
            data-loading-target="{{ $payload['reminder_ajax_loading_target'] }}"
            data-ajax-type="post">{{ cleanLang(__('lang.save')) }}</button>
    </div>

</div>