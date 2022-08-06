<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for foo templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Team;

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

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->is_admin || auth()->user()->role->role_team >= 1) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[foo templates][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //permissions -deleting
        if (auth()->user()->is_admin || auth()->user()->role->role_team == 3) {
            config([
                //visibility
                'visibility.action_super_user' => true,
                'visibility.list_page_actions_add_button' => true,
            ]);
        }
    }
}
