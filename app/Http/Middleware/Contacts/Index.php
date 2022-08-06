<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for contacts
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Contacts;

use App\Models\Contact;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] contacts
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
        if (request()->filled('contactresource_type') && request()->filled('contactresource_id')) {
            //client contacts
            if (request('contactresource_type') == 'client') {
                request()->merge([
                    'filter_contact_clientid' => request('contactresource_id'),
                ]);
            }
        }

        //client user permission
        if (auth()->user()->is_client) {
            if (auth()->user()->role->role_contacts >= 1) {
                //exclude draft contacts
                request()->merge([
                    'filter_contact_exclude_status' => 'draft',
                ]);
                
                return $next($request);
            }
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_contacts >= 1) {
                
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][contacts][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource_type and resource_id (for easy appending in blade templates - action url's)
         * [usage]
         *   replace the usual url('contact/edit/etc') with urlResource('contact/edit/etc'), in blade templated
         *   usually in the ajax.blade.php files (actions links)
         * */
        if (request('contactresource_type') != '' || is_numeric(request('contactresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&contactresource_type=' . request('contactresource_type') . '&contactresource_id=' . request('contactresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.contacts_col_client' => true,
            'visibility.contacts_col_last_active' => true,
        ]);

        //permissions -viewing
        if (auth()->user()->role->role_contacts >= 1) {
            if (auth()->user()->is_team) {
                config([
                    //visibility
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                    'visibility.filter_panel_client' => true,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_contacts >= 2) {
            config([
                //visibility
                'visibility.action_column' => true,
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.action_buttons_change_password' => true,
                'visibility.contacts_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->is_client) {
            if (auth()->user()->account_owner == 'yes') {
                config([
                    //visibility
                    'visibility.action_buttons_edit' => true,
                    'visibility.action_buttons_delete' => true,
                    'visibility.list_page_actions_add_button' => true,
                ]);
            }
        }

        //client user
        if (auth()->user()->role->role_contacts >= 3) {
            config([
                //visibility
                'visibility.action_buttons_edit' => true,
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('contactresource_type') == 'client') {
            config([
                //visibility
                'visibility.contacts_col_client' => false,
                'visibility.contacts_col_last_active' => false,
                'visibility.filter_panel_client' => false,
            ]);
        }
    }
}
