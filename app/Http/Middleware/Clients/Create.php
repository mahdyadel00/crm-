<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for clients
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Clients;

use Closure;
use Log;

class Create {

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
        
        //[MT] - check resource limits
        $this->multiTenancy();

        //permission: does user have permission view clients
        if (auth()->user()->role->role_clients >= 2) {
            return $next($request);
        }


        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][clients][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id()]);
        abort(403);
    }

    /**
     * //[MT]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function multiTenancy() {

        //ignore for standalone
        if (config('system.settings_type') == 'standalone') {
            return;
        }

        //ignore for unlimited
        if (config('system.settings_saas_package_limits_clients') == -1) {
            return;
        }

        //check limits
        $usage = \App\Models\Client::count();
        if ($usage >= config('system.settings_saas_package_limits_clients')) {
            abort(409, __('lang.maximum_resources_reached'));
        }
    }
}
