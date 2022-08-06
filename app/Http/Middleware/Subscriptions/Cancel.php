<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles all request to cancel a subscription
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Subscriptions;
use Closure;
use Log;

class Cancel {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] subscriptions
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
            Log::error("subscription could not be found", ['process' => '[permissions][subscriptions][cancel]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription id' => $subscription_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit subscriptions
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_subscriptions >= 2) {
                return $next($request);
            }
        }

        //client: does user have permission edit subscriptions
        if (auth()->user()->is_client) {
            if ($subscription->subscription_clientid == auth()->user()->clientid) {
                return $next($request);
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][subscriptions][cancel]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
