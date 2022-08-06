<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [team user validation] precheck processes
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\General;

use Closure;
use Log;

class TeamCheck {
    /**
     * Check if incoming request in from a team member
     * if not, show 403 error
     * @usage $this->middleware('teamCheck')->only(['create']);
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        //check if user is a team member. If user is not, show errors
        if (strtolower(auth()->user()->type) != 'team') {
            abort(403);
        }
        //continue
        return $next($request);
    }
}
