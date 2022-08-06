<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for estimates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Estimates;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the estimate exists
     *   2. checks users permissions to [view] the estimate
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
            abort(404);
        }

        //team: does user have permission edit estimates
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_estimates >= 1) {
                return $next($request);
            }
        }

        //client: does user have permission edit estimates
        if (auth()->user()->is_client) {
            if ($estimate->bill_clientid == auth()->user()->clientid) {

                //estimate is still in draft mode
                if($estimate->bill_status == 'draft'){
                    abort(404);
                }

                //all ok
                if (auth()->user()->role->role_estimates >= 1) {
                    return $next($request);
                }
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][estimates][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'estimate id' => $bill_estimateid ?? '']);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //merge data
        request()->merge([
            'resource_query' => 'ref=page',
        ]);

        //default view (not editing)
        config([
            'visibility.bill_mode' => 'viewing',
        ]);

        //are we in editing mode
        if (request()->segment(3) == 'edit-estimate') {
            config([
                'visibility.bill_mode' => 'editing',
                'css.bill_mode' => 'editing',
            ]);
        }

        //page level javascript
        config(['js.section' => 'bill']);
    }
}
