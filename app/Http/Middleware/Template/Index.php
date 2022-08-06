<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Foo;

use App\Models\Foo;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] foos
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //various frontend and visibility settings
        $this->fronteEnd();

        //embedded request: limit by supplied resource data
        if (request()->filled('fooresource_type') && request()->filled('fooresource_id')) {
            //project foos
            if (request('fooresource_type') == 'project') {
                request()->merge([
                    'filter_foo_projectid' => request('fooresource_id'),
                ]);
            }
            //client foos
            if (request('fooresource_type') == 'client') {
                request()->merge([
                    'filter_foo_clientid' => request('fooresource_id'),
                ]);
            }
        }

        //client user permission
        if (auth()->user()->is_client) {
            if (auth()->user()->role->role_foos >= 1) {
                //exclude draft foos
                request()->merge([
                    'filter_foo_exclude_status' => 'draft',
                ]);

                return $next($request);
            }
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_foos >= 1) {

                return $next($request);
            }
        }

        //client users
        if (auth()->user()->is_client) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[foos][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource_type and resource_id (for easy appending in blade templates - action url's)
         * [usage]
         *   replace the usual url('foo/edit/etc') with urlResource('foo/edit/etc'), in blade templated
         *   usually in the ajax.blade.php files (actions links)
         * */
        if (request('fooresource_type') != '' || is_numeric(request('fooresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&fooresource_type=' . request('fooresource_type') . '&fooresource_id=' . request('fooresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.foos_col_client' => true,
            'visibility.foos_col_project' => true,
            'visibility.foos_col_payments' => true,
            'visibility.filter_panel_client_project' => true,
        ]);

        //permissions -viewing
        if (auth()->user()->role->role_foos >= 1) {
            if (auth()->user()->is_team) {
                config([
                    //visibility
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                    'visibility.stats_toggle_button' => true,
                ]);
            }
            if (auth()->user()->is_client) {
                config([
                    //visibility
                    'visibility.list_page_actions_search' => true,
                    'visibility.foos_col_client' => false,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_foos >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.foos_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_foos >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('fooresource_type') == 'project') {
            config([
                //visibility
                'visibility.foos_col_client' => false,
                'visibility.foos_col_project' => false,
                'visibility.filter_panel_client_project' => false,
            ]);
        }

        //columns visibility
        if (request('fooresource_type') == 'client') {
            config([
                //visibility
                'visibility.foos_col_client' => false,
                'visibility.foos_col_payments' => false,
                'visibility.filter_panel_client_project' => false,
            ]);
        }
    }
}
