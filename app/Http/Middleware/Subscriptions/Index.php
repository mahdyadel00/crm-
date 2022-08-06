<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Subscriptions;

use App\Models\Subscription;
use Closure;
use Log;

class Index {

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

        //various frontend and visibility settings
        $this->fronteEnd();

        //embedded request: limit by supplied resource data
        if (request()->filled('subscriptionresource_type') && request()->filled('subscriptionresource_id')) {
            //project subscriptions
            if (request('subscriptionresource_type') == 'project') {
                request()->merge([
                    'filter_subscription_projectid' => request('subscriptionresource_id'),
                ]);
            }
            //client subscriptions
            if (request('subscriptionresource_type') == 'client') {
                request()->merge([
                    'filter_subscription_clientid' => request('subscriptionresource_id'),
                ]);
            }
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_subscriptions >= 1) {
                return $next($request);
            }
        }

        //client users
        if (auth()->user()->is_client) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][subscriptions][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource_type and resource_id (for easy appending in blade templates - action url's)
         * [usage]
         *   replace the usual url('subscription/edit/etc') with urlResource('subscription/edit/etc'), in blade templated
         *   usually in the ajax.blade.php files (actions links)
         * */
        if (request('subscriptionresource_type') != '' || is_numeric(request('subscriptionresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&subscriptionresource_type=' . request('subscriptionresource_type') . '&subscriptionresource_id=' . request('subscriptionresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.subscriptions_col_client' => true,
            'visibility.subscriptions_col_project' => true,
            'visibility.filter_panel_client_project' => true,
        ]);

        //permissions -viewing
        if (auth()->user()->role->role_subscriptions >= 1) {
            if (auth()->user()->is_team) {
                config([
                    //visibility
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                    'visibility.stats_toggle_button' => false,//[FUTURE] set to true when stats panel has been done
                ]);
            }
            if (auth()->user()->is_client) {
                config([
                    //visibility
                    'visibility.list_page_actions_search' => true,
                    'visibility.subscriptions_col_client' => false,
                    'visibility.action_buttons_cancel' => true,
                    'visibility.stripe_js' => true,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_subscriptions >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.action_buttons_cancel' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_subscriptions >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('subscriptionresource_type') == 'project') {
            config([
                //visibility
                'visibility.subscriptions_col_client' => false,
                'visibility.subscriptions_col_project' => false,
                'visibility.filter_panel_client_project' => false,
            ]);
        }

        //columns visibility
        if (request('subscriptionresource_type') == 'client') {
            config([
                //visibility
                'visibility.subscriptions_col_client' => false,
                'visibility.subscriptions_col_payments' => false,
                'visibility.filter_panel_client_project' => false,
            ]);
        }
    }
}
