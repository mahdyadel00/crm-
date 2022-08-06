<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for clients
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Clients;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the client exists
     *   2. checks users permissions to [view] the client
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //client id
        $client_id = $request->route('client');

        //frontend
        $this->fronteEnd();

        //does the client exist
        if ($client_id == '' || !$client = \App\Models\Client::Where('client_id', $client_id)->first()) {
            Log::error("item not found", ['process' => '[permissions][clients][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'client id' => $client_id ?? '', 'user_id' => auth()->id()]);
            abort(404);
        }

        //team: does user have permission edit clients
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_clients >= 1) {

                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][clients][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'client id' => $client_id ?? '', 'user_id' => auth()->id()]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //merge data
        request()->merge([
            'resource_query' => 'ref=page',
            'visibility.client_show_custom_fields' => false,
        ]);

        //page level javascript
        config(['js.section' => 'client']);

        //edit permissions
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_clients >= 2) {
                config([
                    'visibility.edit_client_button' => true,
                ]);
            }
        }

        //show we show the customer fields left panel section
        $count = \App\Models\CustomField::where('customfields_type', 'clients')->where('customfields_standard_form_status', 'enabled')->where('customfields_status', 'enabled')->count();
        if ($count > 0) {
            config([
                'visibility.client_show_custom_fields' => true,
            ]);
        }
    }
}
