<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for clients
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Clients;

use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] clients
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //various frontend and visibility settings
        $this->fronteEnd();

        //permission: does user have permission view clients
        if (auth()->user()->role->role_clients >= 1) {
            return $next($request);
        }

        //client: updating their profile
        if ($request->isMethod('put')) {
            if (auth()->user()->is_client) {
                if ($request->route('client') == auth()->user()->clientid) {
                    return $next($request);
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][clients][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id()]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('client') with urlResource('client'), in blade templated
         * */
        if (request('clientresource_type') != '' || is_numeric(request('clientresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&clientresource_type=' . request('clientresource_type') . '&clientresource_id=' . request('clientresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //permissions -viewing
        if (auth()->user()->role->role_clients >= 1) {
            config([
                'visibility.list_page_actions_filter_button' => true,
                'visibility.list_page_actions_search' => true,
            ]);
        }

        //permissions -adding
        if (auth()->user()->role->role_clients >= 2) {
            config([
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.clients_col_checkboxes' => true,
                'visibility.action_column' => true,
                'visibility.list_page_actions_importing' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_clients >= 3) {
            config([
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_delete' => true,
            ]);
        }
    }
}
