<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] precheck processes for clients
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Clients;
use Closure;
use Log;

class Destroy {

    /**
     * This middleware does the following
     *   1. validates that the client exists
     *   2. checks users permissions to [delete] the client
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //client id
        $client_id = $request->route('client');

        //does the client exist
        if ($client_id == '' || !$client = \App\Models\Client::Where('client_id', $client_id)->first()) {
            abort(409, __('lang.client_not_found'));
        }

        //team: does user have permission delete clients
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_clients >= 3) {
                
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][clients][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'client id' => $client_id ?? '', 'user_id' => auth()->id()]);
        abort(403);
    }
}
