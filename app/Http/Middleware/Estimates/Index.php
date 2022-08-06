<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for estimates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Estimates;

use App\Models\Estimate;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] estimates
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.estimates')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //various frontend and visibility settings
        $this->fronteEnd();

        //embedded request: limit by supplied resource data
        if (request()->filled('estimateresource_type') && request()->filled('estimateresource_id')) {
            //client estimates
            if (request('estimateresource_type') == 'client') {
                request()->merge([
                    'filter_bill_clientid' => request('estimateresource_id'),
                ]);
            }
        }

        //client user permission
        if (auth()->user()->is_client) {
            if (auth()->user()->is_client_owner) {
                //exclude draft estimates
                request()->merge([
                    'filter_estimate_exclude_status' => 'draft',
                    'filter_bill_clientid' => auth()->user()->clientid,
                ]);
                return $next($request);
            }
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_estimates >= 1) {
                
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][estimates][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('estimate') with urlResource('estimate'), in blade templated
         * */
        if (request('estimateresource_type') != '' || is_numeric(request('estimateresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&estimateresource_type=' . request('estimateresource_type') . '&estimateresource_id=' . request('estimateresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.estimates_col_client' => true,
            'visibility.estimates_col_created_by' => true,
            'visibility.filter_panel_client' => true,
            'visibility.estimates_col_tags' => false,
        ]);

        //permissions -viewing
        if (auth()->user()->role->role_estimates >= 1) {
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
                    'visibility.estimates_col_client' => false,
                ]);
            }
        }

        //permissions - adding and editing
        if (auth()->user()->role->role_estimates >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.estimates_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_estimates >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('estimateresource_type') == 'client') {
            config([
                //visibility
                'visibility.estimates_col_client' => false,
                'visibility.estimates_col_created_by' => false,
                'visibility.filter_panel_client' => false,
                'visibility.filter_panel_clients_projects' => true,
                'visibility.estimates_col_tags' => false,
            ]);
        }

        //columns visibility
        if (auth()->user()->is_client) {
            config([
                //visibility
                'visibility.estimates_col_client' => false,
                'visibility.estimates_col_created_by' => false,
            ]);
        }
    }
}
