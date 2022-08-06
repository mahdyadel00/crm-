<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles precheck processes for setup processes
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\General;
use Cache;
use Closure;
use Auth;

class Setup {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        /* -
        ---------------------------------------------------------
         * [SETUP CHECK]
         * If setup has not happened, delete the session, incase a 
         * user is reinstalling on a server, that has previousy had
         * the app installed. This will prevent 504 timeout trying
         * to connect to mysql database to authenticate the user
         * ----------------------------------------------------------*/
        if (env('SETUP_STATUS') != 'COMPLETED') {
            Auth::logout();
        }

        return $next($request);
    }

}