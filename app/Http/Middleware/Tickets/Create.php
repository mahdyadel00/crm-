<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tickets;
use Closure;
use Log;

class Create {

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

        //frontend
        $this->fronteEnd();

        //permission: does user have permission create tickets
        if (auth()->user()->role->role_tickets >= 2) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][tickets][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        
        //client prefill
        if (auth()->user()->is_client) {
            request()->merge([
                'ticket_priority' => 'normal',
                'ticket_clientid' => auth()->user()->clientid,
            ]);
        }


    }
}
