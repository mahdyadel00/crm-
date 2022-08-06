<?php

namespace App\Http\Middleware\Subscriptions;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the subscription exists
     *   2. checks users permissions to [view] the subscription
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.subscriptions')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //subscription id
        $subscription_id = $request->route('subscription');

        //does the subscription exist
        if ($subscription_id == '' || !$subscription = \App\Models\Subscription::Where('subscription_id', $subscription_id)->first()) {
            abort(404);
        }

        //frontend
        $this->fronteEnd($subscription);

        //team: does user have permission edit subscriptions
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_subscriptions >= 1) {
                return $next($request);
            }
        }

        //client: does user have permission edit subscriptions
        if (auth()->user()->is_client) {
            if ($subscription->subscription_clientid == auth()->user()->clientid) {
                if (auth()->user()->role->role_subscriptions >= 1) {
                    return $next($request);
                }
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][subscriptions][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($subscription) {

        //merge data
        request()->merge([
            'resource_query' => 'ref=page',
        ]);

        //team permissions
        if (auth()->user()->is_team) {
            //show client name
            config([
                'visibility.client_name' => true,
                'visibility.stripe_id' => true,
            ]);
            //show delete button
            if (auth()->user()->role->role_subscriptions >= 3) {
                config([
                    'visibility.delete_button' => true,
                ]);
            }

            //payments
            config([
                'visibility.payments_list' => true,
            ]);

        }

        //client permissions
        if (auth()->user()->is_client) {
            config([
                'visibility.client_name' => false,
                'visibility.stripe_js' => true,
            ]);
            //show edit button
            if (auth()->user()->is_client_owner) {
                config([
                    'visibility.client_actions_edit' => true,
                ]);
            }
            //show subscription failed - action required panel
            if (auth()->user()->is_client_owner) {

                //show account edit
                config([
                    'visibility.client_actions_edit' => true,
                ]);

                //show complete subscription panel
                if ($subscription->subscription_status == 'pending') {
                    config([
                        'visibility.complete_payment_panel' => true,
                    ]);
                }

                //show subscription failed - actionals panel
                if ($subscription->subscription_status == 'failed') {
                    config([
                        'visibility.payment_failed_panel' => true,
                    ]);
                }
                //payments
                if (in_array($subscription->subscription_status, ['active', 'cancelled'])) {
                    config([
                        'visibility.payments_list' => true,
                    ]);
                }
            }

        }

    }

}
