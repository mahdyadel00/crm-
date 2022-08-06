<!--no reminder-->
<div class="reminders-none-existing" id="reminders-none-existing">
    <div class="x-image">
        <img src="{{ url('/public/images/reminders_none.svg') }}" alt="404 - Not found" />
    </div>
    <div class="x-text">
        @lang('lang.you_do_not_have_a_reminder_for_item')
    </div>
    <div class="x-button">
        <button type="button" class="btn btn-rounded-x btn-info btn-sm js-ajax-ux-request apply-filter-button"
            data-url="{{ url('reminders/new?resource_type='.$payload['resource_type'].'&resource_id='.$payload['resource_id']) }}"
            data-loading-target="reminders-side-panel-body"
            data-progress-bar='hidden'>{{ cleanLang(__('lang.add_a_reminder')) }}</button>
    </div>
</div>