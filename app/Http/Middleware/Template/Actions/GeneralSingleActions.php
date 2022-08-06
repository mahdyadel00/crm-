<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [general] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Template\Actions;
use Closure;
use Log;

class GeneralSingleActions {

    /**
     * This middleware does the following
     *   1. validates that the foo exists
     *   2. checks users permissions to [edit] the foo
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //NOTE: F4 change [action-bar] to the action name e.g. [change-status]

        //foo id
        $foo_id = request('id');

        //does the foo exist
        if (!$foo = \App\Models\Foo::Where('foo_id', $foo_id)->first()) {
            Log::error("foo could not be found", ['process' => '[permissions][foo][action-bar]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'category id' => $category_id ?? '']);
            abort(404);
        }

        //permission: does user have permission to attach a project this foo
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_foos >= 2) {
                
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][foos][attach-project]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
