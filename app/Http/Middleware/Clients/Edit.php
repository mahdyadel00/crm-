<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for clients
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Clients;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] clients
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
            Log::error("client could not be found", ['process' => '[permissions][clients][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'client id' => $client_id ?? '', 'user_id' => auth()->id()]);
            abort(409, __('lang.client_not_found'));
        }

        //permission: does user have permission edit clients
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_clients >= 2) {

                return $next($request);
            }
        }

        //client: does user have permission edit foos
        if (auth()->user()->is_client) {
            if ($client_id == auth()->user()->clientid) {
                return $next($request);
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][clients][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id()]);
        abort(403);
    }
}
