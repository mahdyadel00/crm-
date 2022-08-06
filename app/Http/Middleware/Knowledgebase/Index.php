<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for knowledgebase
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Knowledgebase;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] knowledgebases
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.knowledgebase')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }
        
        //various frontend and visibility settings
        $this->fronteEnd();

        //permission: does user have permission view knowledgebases
        if (auth()->user()->role->role_knowledgebase > 0) {

            //pretty url - get category_id parameter and inject in request object
            if (request()->route('slug')) {
                request()->merge([
                    'filter_category_slug' => request()->route('slug'),
                    'category_slug' => request()->route('slug'),
                ]);
            }
            //team user
            if (auth()->user()->is_team && !auth()->user()->is_admin) {
                request()->merge([
                    'filter_user_type' => 'team',
                ]);
            }

            //team user
            if (auth()->user()->is_client) {
                request()->merge([
                    'filter_user_type' => 'client',
                ]);
            }
            
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][knowledgebases][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //add & edit permissions
        if (auth()->user()->role->role_knowledgebase >= 2) {
            config([
                'visibility.list_page_actions_add_button' => true,
                'visibility.list_page_actions_search' => true,
                'visibility.action_buttons_edit' => true,
            ]);
        }

        //delete permissions
        if (auth()->user()->role->role_knowledgebase >= 3) {
            config([
                'visibility.action_buttons_delete' => true,
            ]);
        }
    }
}
