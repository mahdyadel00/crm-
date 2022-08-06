<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for user templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Team;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] users
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //user id
        $user_id = $request->route('team');

        //frontend
        $this->fronteEnd();

        //does the user exist
        if ($user_id == '' || !$user = \App\Models\User::Where('id', $user_id)->Where('type', 'team')->first()) {
            Log::error("user could not be found", ['process' => '[user middleware][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user id' => $user_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit users
        if (auth()->user()->is_team) {
            if (auth()->user()->is_admin || auth()->user()->role->role_team == 3) {
                //all else ol
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][user middleware][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

    }
}
