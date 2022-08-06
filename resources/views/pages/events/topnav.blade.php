@if(count($events) >0)
@foreach($events as $event)
@if(is_numeric($event->event_id))
<div class="display-flex flex-row topevent" id="event_{{ $event->event_id }}">
    <!--avatar-->
    <div class="">
        <img src="{{ getUsersAvatar($event->avatar_directory, $event->avatar_filename, $event->event_creatorid)  }}"
            class="avatar img-circle" alt="user" width="35">
    </div>
    <div class="x-content">
        <!--user-->
        <div class="x-name clearfix">
            @if($event->event_creatorid == 0 || $event->event_creatorid == -1)
            @if($event->event_creatorid == 0)
            {{ cleanLang(__('lang.system_bot_name')) }}
            @else
            <!--non registered users-->
            {{ $event->event_creator_name }}
            @endif
            @else
            {{ $event->first_name ?? runtimeUnkownUser() }}
            @endif
            <!--mark as read-->
            <span class="pull-right js-notification-mark-read-single" id="fx-top-nav-mark-read"
                data-container="event_{{ $event->event_id }}" data-progress-bar='hidden'
                data-url="{{ url('events/'.$event->event_id.'/mark-read-my-event') }}">
                @if($event->eventtracking_status == 'unread')
                <input name="group4" type="radio" id="event_checkbox_{{ $event->event_id }}" class="radio-col-info">
                <label for="event_checkbox_{{ $event->event_id }}"></label>
                @endif
            </span>
            <span class="x-time pull-right muted">{{ runtimeDateAgo($event->event_created) }}</span></div>
        <!--main title-->
        <div class="x-title"><span>{{ runtimeLang($event->event_item_lang) }}</span>
        </div>
        <!--parent link-->
        <div class="x-ref-title">
            @include('pages.events.includes.parent')
        </div>
        <!--sub content-->
        @if($event->event_show_item == 'yes')
        @include('pages.events.includes.content')
        @endif
    </div>
</div>
@endif
@endforeach
@else
<div class="top-nav-no-evenets">
    <img src="{{ url('/') }}/public/images/relax.png" alt="No events found" />
    <div class="x-message">{{ cleanLang(__('lang.no_notifications_found')) }}</div>
</div>
@endif