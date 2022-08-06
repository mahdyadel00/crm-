<!--flash messages-->
<?php if(Session::has('success-notification')): ?>
<span id="js-trigger-session-message" data-type="success"
    data-message="<?php echo e(Session::get('success-notification')); ?>"></span>
<?php endif; ?>
<?php if(Session::has('error-notification')): ?>
<span id="js-trigger-session-message" data-type="warning"
    data-message="<?php echo e(Session::get('error-notification')); ?>"></span>
<?php endif; ?>

<!--flash messages longer duration-->
<?php if(Session::has('success-notification-longer')): ?>
<span id="js-trigger-session-message" data-type="success"
    data-message="<?php echo e(Session::get('success-notification-longer')); ?>"></span>
<?php endif; ?>

<?php if(Session::has('error-notification-longer')): ?>
<span id="js-trigger-session-message" data-type="warning"
    data-message="<?php echo e(Session::get('error-notification-longer')); ?>"></span>
<?php endif; ?>

<!--force user password change-->
<?php if(Auth::user() && Auth::user()->force_password_change == 'yes'): ?>
<span id="js-trigger-force-password-change" class="hidden edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
    data-toggle="modal" data-target="#commonModal" data-url="<?php echo e(url('user/updatepassword')); ?>"
    data-loading-target="commonModalBody" data-action-url="<?php echo e(url('user/updatepassword')); ?>" data-action-method="PUT"
    data-action-ajax-class="" data-modal-size="modal-sm" data-form-design="form-material"
    data-header-visibility="hidden" data-close-button-visibility="hidden"
    data-action-ajax-loading-target="commonModalBody"></span>
<?php endif; ?>

<!--polling - general data [only when debug mode is disabled, else it resets the debug toolbar]-->
<?php if(Auth::user() && env('APP_DEBUG_TOOLBAR') === false): ?>
<span id="js-trigger-general-polling" class="hidden" data-progress-bar='hidden' data-loading-target="hidden"
    data-skip-checkboxes-reset="TRUE" data-url="<?php echo e(url('polling/general')); ?>"></span>
<?php endif; ?>



<!--poll timers (every 60 seconds)-->
<?php if(Auth::user() && auth()->user()->is_team && env('APP_DEBUG_TOOLBAR') === false): ?>
<span id="js-trigger-general-timers" class="hidden" data-type="form" data-progress-bar='hidden' data-notifications="disabled"
    data-skip-checkboxes-reset="TRUE" data-form-id="tasks-view-wrapper" data-ajax-type="post"
    data-url="<?php echo e(url('/polling/timers?ref=list')); ?>"></span>
<?php endif; ?>

<!--dynamic load - a expense-->
<?php if(config('visibility.dynamic_load_modal')): ?>
<span class="hidden" id="js-trigger-dynamic-modal" data-payload="<?php echo e(config('settings.dynamic_trigger_dom')); ?>"></span>
<?php endif; ?>


<!--updates - updating modals-->
<?php if(Auth::user() && Auth::user()->role_id == 1 && config('updating.count_pending_actions') > 0): ?>
<span id="js-trigger-force-password-change" class="hidden edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
    data-toggle="modal" data-target="#commonModal" data-url="<?php echo e(config('updating.updating_request_path')); ?>"
    data-loading-target="commonModalBody" data-action-url="<?php echo e(config('updating.updating_update_path')); ?>" data-action-method="PUT"
    data-modal-size="modal-lg"
    data-action-ajax-class="js-ajax-ux-request"
    data-header-visibility="hidden" data-close-button-visibility="hidden"
    data-action-ajax-loading-target="commonModalBody"></span>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\application\resources\views/layout/automationjs.blade.php ENDPATH**/ ?>