<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for estimates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Estimates;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] estimates
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.estimates')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //estimate id
        $bill_estimateid = $request->route('estimate');

        //frontend
        $this->fronteEnd();

        //does the estimate exist
        if ($bill_estimateid == '' || !$estimate = \App\Models\Estimate::Where('bill_estimateid', $bill_estimateid)->first()) {
            Log::error("estimate could not be found", ['process' => '[permissions][estimates][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'estimate id' => $bill_estimateid ?? '']);
            abort(409, __('lang.estimate_not_found'));
        }

        //permission: does user have permission edit estimates
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_estimates >= 2) {
                
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][estimates][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //vivibility
        config(['visibility.estimate_modal_client_project_fields' => false]);

    }
}
