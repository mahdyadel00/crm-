<div class="subscription-details">



    <!--complete payment - action required-->
    @if(config('visibility.complete_payment_panel'))
    <div class="subscription-alert">
        <div class="x-title">
            @lang('lang.subscription_complete_your_payment')
        </div>
        <div class="x-button">
            <button type="button" class="btn btn-rounded-x btn-danger js-ajax-ux-request disable-on-click-please-wait" data-url="{{ url('/subscriptions/'.$subscription->subscription_id.'/pay') }}">
                @lang('lang.complete_your_payment')</button>
        </div>
    </div>
    @endif


    <!--payment failed - action required-->
    @if(config('visibility.payment_failed_panel'))
    <div class="subscription-alert">
        <div class="x-title">
            @lang('lang.subscription_payment_failed')
        </div>
        <div class="x-button">
            <button type="button" class="btn btn-rounded-x btn-danger edit-add-modal-button js-ajax-ux-request"
                data-toggle="modal" data-url="/tickets/22/edit?edit_type=all&amp;edit_source=leftpanel"
                data-action-url="/tickets/22" data-target="#commonModal" data-loading-target="commonModalBody"
                data-action-method="PUT" data-modal-title="Edit Support Ticket">
                @lang('lang.update_credit_card')</button>
        </div>
    </div>
    @endif

    <!--payment button-->
    <div id="subscription-pay-container">
        <!--dynamic-->
    </div>

    <!--payments-->
    @if(config('visibility.payments_list'))
    <div class="subscription-payments">
        <div class="x-heading">@lang('lang.payment_history')</div>
        <div id="subscription-payments">
            @if(count($invoices) > 0)
            @include('pages.subscription.ajax')
        </div>
        <!--load more button-->
        @if($page['load_more_visibility'] == 'visible')
        <div class="autoload loadmore-button-container" id="subscription_load_more_button">
            <a data-url="{{ $page['url'] ?? '' }}" href="javascript:void(0)"
                class="btn btn-rounded btn-secondary js-ajax-ux-request"
                id="load-more-button">{{ cleanLang(__('lang.show_more')) }}</a>
        </div>
        @endif
        <!--load more button-->
        @else
        <!--no records found-->
        <div class="splash-message m-t-40">
            <div class="splash-image">
                <img src="{{ url('/') }}/public/images/records-not-found.svg" alt="404 - Not found" />
            </div>
            <div class="splash-text">
                @lang('lang.no_payments_found')
            </div>
        </div>
        @endif
    </div>
    @endif


</div>