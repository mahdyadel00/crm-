<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for product items
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Items;

use App\Models\Item;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] items
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
            if (auth()->user()->role->role_items >= 1) {

                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][items][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('item') with urlResource('item'), in blade templated
         * */
        if (request('itemresource_type') != '' || is_numeric(request('itemresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&itemresource_type=' . request('itemresource_type') . '&itemresource_id=' . request('itemresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.items_col_action' => true,
            'visibility.items_col_category' => true,
        ]);

        //default buttons
        config([
            'visibility.list_page_actions_filter_button' => false,
            'visibility.list_page_actions_search' => true,
            'visibility.list_page_actions_filter_button' => true,
        ]);

        //trimming content
        config([
            'settings.trimmed_title' => true,
            'settings.item_description' => true,
        ]);

        //permissions -adding editing
        if (auth()->user()->role->role_items >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.items_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_items >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //calling this fron invoice page
        if (request('itemresource_type') == 'invoice') {
            config([
                'visibility.items_col_action' => false,
                'settings.trimmed_title' => false,
            ]);
        }
    }
}
