<!-- right-sidebar (reusable)-->
<div class="right-sidebar right-sidepanel-with-menu" id="sidepanel-notifications">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <div class="x-top">
                    <i class="sl-icon-bell"></i>{{ cleanLang(__('lang.notifications')) }}
                    <span>
                        <i class="ti-close js-close-side-panels" data-target="sidepanel-notifications"></i>
                    </span>
                </div>
                <div class="x-top-nav">
                    <a class="right-sidepanel-menu active ajax-request" href="javascript:void(0);"
                        id="right-sidepanel-menu-unread"
                        data-url="{{ url('events/topnav?eventtracking_status=unread') }}"
                        data-loading-target="sidepanel-notifications-body" data-target="sidepanel-notifications"
                        data-progress-bar='hidden'>@lang('lang.unread')</a>
                    <span class="x-spacer">|</span>
                    <a class="right-sidepanel-menu ajax-request" href="javascript:void(0);"
                        data-url="{{ url('events/topnav') }}" data-loading-target="sidepanel-notifications-body"
                        data-target="sidepanel-notifications" data-progress-bar='hidden'>@lang('lang.all')</a>
                </div>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body p-t-40" id="sidepanel-notifications-body">
                <!--mark all read-->
                <div class="sidepanel-notifications-mark-all-read hidden" id="sidepanel-notifications-mark-all-read">
                    <a href="javascript:void(0);" id="topnav-notification-mark-all-read"
                    data-url="{{ url('events/mark-allread-my-events') }}" data-progress-bar='hidden'>@lang('lang.dismiss_notifications')</button>
                </div>

                <div id="sidepanel-notifications-events">
                    <!--dynamic content-->
                </div>

                <!--load more button-->
                <div class="autoload" id="events-panel-loadmore-button-container">
                    <a data-url="{{ $page['url'] ?? '' }}" data-loading-target="{{ $page['loading_target'] ?? '' }}"
                        href="javascript:void(0)" class="btn btn-rounded btn-secondary js-ajax-ux-request"
                        id="events-panel-load-more-button">{{ cleanLang(__('lang.show_more')) }}</a>
                </div>
                <!--load more button-->
            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->