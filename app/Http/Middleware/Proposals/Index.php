<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for product proposals
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Proposals;

use App\Models\Proposal;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] proposals
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //various frontend and visibility settings
        $this->fronteEnd();

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_proposals >= 1) {
                return $next($request);
            }
        }

        //client user permission
        if (auth()->user()->is_client) {
            request()->merge([
                'filter_proposal_exclude_status' => 'draft',
                'filter_doc_client_id' => auth()->user()->clientid,
            ]);
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][proposals][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('proposal') with urlResource('proposal'), in blade templated
         * */
        if (request('docresource_type') != '' || is_numeric(request('docresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&docresource_type=' . request('docresource_type') . '&docresource_id=' . request('docresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default buttons
        config([
            'visibility.list_page_actions_search' => true,
            'visibility.list_page_actions_filter_button' => true,
            'visibility.proposals_col_action' => true,
            'visibility.col_created_by' => false, //not enough space on table
            'visibility.col_client' => true,
        ]);

        //permissions -adding editing
        if (auth()->user()->role->role_proposals >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.proposals_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_proposals >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //calling this fron invoice page
        if (request('docresource_type') == 'client' || request('docresource_type') == 'lead') {
            config([
                'visibility.col_client' => false,
            ]);
        }

        //columns visibility
        if (auth()->user()->is_team) {
            config([
                'visibility.filter_panel_client' => true,
                'visibility.filter_panel_lead' => true,
                'visibility.stats_toggle_button' => true,
            ]);
        }

        //columns visibility
        if (auth()->user()->is_client) {
            config([
                //visibility
                'visibility.col_client' => false,
            ]);
        }
    }
}
