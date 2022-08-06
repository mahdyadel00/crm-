<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [admin validation] precheck processes for general processes
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\General;

use Closure;
use Log;


class AdminCheck {
    /**
     * Check if incoming request in from a user with the role 'administrator'
     * if not, show 403 error
     * @usage $this->middleware('adminCheck')->only(['create']);
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        //check if user is admin. If user is not, show errors
        if (auth()->user()->role->role_id !== 1) {
            abort(403);
        }
        //continue
        return $next($request);
    }
}
