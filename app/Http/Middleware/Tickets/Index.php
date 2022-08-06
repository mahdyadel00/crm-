<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tickets;

use App\Models\Ticket;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] tickets
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.tickets')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //various frontend and visibility settings
        $this->fronteEnd();

        //embedded request: limit by supplied resource data
        if (request()->filled('ticketresource_type') && request()->filled('ticketresource_id')) {
            //client tickets
            if (request('ticketresource_type') == 'client') {
                request()->merge([
                    'filter_ticket_clientid' => request('ticketresource_id'),
                ]);
            }
        }

        //client user permission
        if (auth()->user()->is_client) {
                return $next($request);
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tickets >= 1) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][tickets][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource_type and resource_id (for easy appending in blade templates - action url's)
         * [usage] 
         *   replace the usual url('ticket/edit/etc') with urlResource('ticket/edit/etc'), in blade templated
         *   usually in the ajax.blade.php files (actions links)
         * */
        if (request('ticketresource_type') != '' || is_numeric(request('ticketresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&ticketresource_type=' . request('ticketresource_type') . '&ticketresource_id=' . request('ticketresource_id'),
            ]);
        }else{
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.tickets_col_client' => true,
            'visibility.tickets_col_id' => true,
            'visibility.tickets_col_activity' => true,
            'visibility.filter_panel_client' => true,
        ]);

        //permissions -viewing
        if (auth()->user()->role->role_tickets >= 1) {
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
                    'visibility.list_page_actions_add_button_link' => true,
                    'visibility.tickets_col_client' => false,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_tickets >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button_link' => true,
                'visibility.action_buttons_edit' => true,
            ]);
            if (auth()->user()->is_client) {
                config([
                    'visibility.action_buttons_edit' => false,
                ]);
            }
        }

        //permissions -deleting
        if (auth()->user()->role->role_tickets >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
                'visibility.tickets_col_checkboxes' => true,
            ]);
        }

        //columns visibility
        if (request('ticketresource_type') == 'client') {
            config([
                //visibility
                'visibility.tickets_col_client' => false,
                'visibility.filter_panel_client_project' => false,
                'visibility.tickets_col_id' => false,
                'visibility.tickets_col_activity' => false,
            ]);
        }
    }
}
