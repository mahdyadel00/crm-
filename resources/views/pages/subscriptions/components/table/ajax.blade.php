@foreach($subscriptions as $subscription)
<!--each row-->
<tr id="subscription_{{ $subscription->subscription_id }}">
    <td class="subscriptions_col_id">
        <a href="/subscriptions/{{ $subscription->subscription_id }}">{{ runtimeSubscriptionIdFormat($subscription->subscription_id) }}
        </a></td>
    @if(config('visibility.subscriptions_col_client'))
    <td class="subscriptions_col_company">
        <a
            href="/clients/{{ $subscription->client_id }}">{{ str_limit($subscription->client_company_name ?? '---', 12) }}</a>
    </td>
    @endif
    <td class="subscriptions_col_plan">
        <a
            href="/subscriptions/{{ $subscription->subscription_id }}">{{ str_limit($subscription->subscription_gateway_product_name ?? '---', 25) }}</a>
    </td>
    <td class="subscriptions_col_amount">
        {{ runtimeMoneyFormat($subscription->subscription_final_amount) }}
    </td>
    <td class="subscriptions_col_renewed">
        {{ runtimeDate($subscription->subscription_date_renewed) }}
    </td>
    <td class="subscriptions_col_payments">
        {{ runtimeMoneyFormat($subscription->sum_payments) }}
    </td>
    <td class="subscriptions_col_status">
        <span class="label {{ runtimeSubscriptionsColors($subscription->subscription_status, 'label') }}">{{
            runtimeLang($subscription->subscription_status) }}</span>
    </td>
    <td class="subscriptions_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/') }}/subscriptions/{{ $subscription->subscription_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif

            @if(auth()->user()->is_client)
            <!--pay now-->
            @if($subscription->subscription_status == 'pending')
            <button class="btn btn-rounded btn-danger btn-sm btn-action-danger waves-effect actions-modal-button js-ajax-ux-request "
                data-toggle="modal" data-target="#actionsModal" data-loading-target="actionsModalBody"
                data-modal-title="@lang('lang.subscription_payment')"
                data-url="{{ url('/subscriptions/'.$subscription->subscription_id.'/pay?source=list') }}">@lang('lang.pay')</button>
            @endif
            <!--add new card-->
            @if($subscription->subscription_status == 'failed')
            <button class="btn btn-rounded btn-danger action-btn btn-sm waves-effect text-left"
                data-url="">@lang('lang.pay')</button>
            @endif
            <!--just view-->
            @if($subscription->subscription_status == 'cancelled' || $subscription->subscription_status == 'active')
            <a href="/subscriptions/{{ $subscription->subscription_id }}" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
            @endif
            @endif


            <!--more button (team)-->
            @if(auth()->user()->is_team)
            <!--view-->
            <a href="/subscriptions/{{ $subscription->subscription_id }}" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
            <span class="list-table-action dropdown font-size-inherit">
                <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" title="{{ cleanLang(__('lang.more')) }}"
                    class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                    <i class="ti-more"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="listTableAction">
                    <!--cancel subscription-->
                    @if(config('visibility.action_buttons_cancel'))
                    <a class="dropdown-item confirm-action-danger" href="javascript:void(0)"
                        data-confirm-title="{{ cleanLang(__('lang.cancel_subscription')) }}"
                        data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-url="{{ url('/subscriptions/'.$subscription->subscription_id.'/cancel') }}">
                        {{ cleanLang(__('lang.cancel_subscription')) }}</a>
                    @endif
                    <a class="dropdown-item"
                        href="/subscriptions/{{ $subscription->subscription_id }}">
                        @lang('lang.view_subscription')</a>
                </div>
            </span>
            @endif
            <!--more button-->

        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->