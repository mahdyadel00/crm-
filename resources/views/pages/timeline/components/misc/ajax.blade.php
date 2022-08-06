@foreach($events as $event)
@if($event->event_show_in_timeline == 'yes')
<!--each events item-->
<div class="sl-item timeline">
    <div class="sl-left">
        <img src="{{ getUsersAvatar($event->avatar_directory, $event->avatar_filename, $event->event_creatorid)  }}" alt="user"
            class="img-circle" />
    </div>
    <div class="sl-right">
        <div>
            <div class="x-meta"><a href="javascript:void(0)" class="link">
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
            </a> <span class="sl-date">{{ runtimeDateAgo($event->event_created) }}</span>
            </div>
            <div class="x-title">
                <!--assigned event - viewed by third party-->
                @if($event->event_notification_category == 'notifications_new_assignement' && (auth()->user()->id != $event->event_item_content2))
                <span>{{ runtimeLang($event->event_item_lang_alt) }} {{ $event->event_item_content3 }}<span>
                @else
                <span>{{ runtimeLang($event->event_item_lang) }}<span>
                @endif
                <!--do for project time lines-->
                @if(request('timelineresource_type') == 'project' && ($event->event_parent_type =='project' || $event->event_parent_type =='file'))
                <!--do nothing-->
                @else
                @include('pages.events.includes.parent')
                @endif
            </div>
            @if($event->event_show_item == 'yes')
            @include('pages.events.includes.content')
            @endif
        </div>
    </div>
</div>
<!--each events item-->
@endif
@endforeach