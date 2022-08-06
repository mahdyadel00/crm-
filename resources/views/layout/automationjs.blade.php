<!--flash messages-->
@if (Session::has('success-notification'))
<span id="js-trigger-session-message" data-type="success"
    data-message="{{ Session::get('success-notification') }}"></span>
@endif
@if (Session::has('error-notification'))
<span id="js-trigger-session-message" data-type="warning"
    data-message="{{ Session::get('error-notification') }}"></span>
@endif

<!--flash messages longer duration-->
@if (Session::has('success-notification-longer'))
<span id="js-trigger-session-message" data-type="success"
    data-message="{{ Session::get('success-notification-longer') }}"></span>
@endif

@if (Session::has('error-notification-longer'))
<span id="js-trigger-session-message" data-type="warning"
    data-message="{{ Session::get('error-notification-longer') }}"></span>
@endif

<!--force user password change-->
@if(Auth::user() && Auth::user()->force_password_change == 'yes')
<span id="js-trigger-force-password-change" class="hidden edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
    data-toggle="modal" data-target="#commonModal" data-url="{{ url('user/updatepassword') }}"
    data-loading-target="commonModalBody" data-action-url="{{ url('user/updatepassword') }}" data-action-method="PUT"
    data-action-ajax-class="" data-modal-size="modal-sm" data-form-design="form-material"
    data-header-visibility="hidden" data-close-button-visibility="hidden"
    data-action-ajax-loading-target="commonModalBody"></span>
@endif

<!--polling - general data [only when debug mode is disabled, else it resets the debug toolbar]-->
@if(Auth::user() && env('APP_DEBUG_TOOLBAR') === false)
<span id="js-trigger-general-polling" class="hidden" data-progress-bar='hidden' data-loading-target="hidden"
    data-skip-checkboxes-reset="TRUE" data-url="{{ url('polling/general') }}"></span>
@endif



<!--poll timers (every 60 seconds)-->
@if(Auth::user() && auth()->user()->is_team && env('APP_DEBUG_TOOLBAR') === false)
<span id="js-trigger-general-timers" class="hidden" data-type="form" data-progress-bar='hidden' data-notifications="disabled"
    data-skip-checkboxes-reset="TRUE" data-form-id="tasks-view-wrapper" data-ajax-type="post"
    data-url="{{ url('/polling/timers?ref=list') }}"></span>
@endif

<!--dynamic load - a expense-->
@if(config('visibility.dynamic_load_modal'))
<span class="hidden" id="js-trigger-dynamic-modal" data-payload="{{ config('settings.dynamic_trigger_dom') }}"></span>
@endif


<!--updates - updating modals-->
@if(Auth::user() && Auth::user()->role_id == 1 && config('updating.count_pending_actions') > 0)
<span id="js-trigger-force-password-change" class="hidden edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
    data-toggle="modal" data-target="#commonModal" data-url="{{ config('updating.updating_request_path') }}"
    data-loading-target="commonModalBody" data-action-url="{{ config('updating.updating_update_path') }}" data-action-method="PUT"
    data-modal-size="modal-lg"
    data-action-ajax-class="js-ajax-ux-request"
    data-header-visibility="hidden" data-close-button-visibility="hidden"
    data-action-ajax-loading-target="commonModalBody"></span>
@endif