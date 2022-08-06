<!--reminder-->
@if($payload['has_reminder'])
@include('pages.reminders.cards.reminder-show')
@else
@include('pages.reminders.cards.reminder-add')
@endif