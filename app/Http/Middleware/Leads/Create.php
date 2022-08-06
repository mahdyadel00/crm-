<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;
use Closure;
use Log;

class Create {

    /**
     * This middleware does the following:
     *   1. checks users permissions to [create] a new resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.leads')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //frontend
        $this->fronteEnd();

        //does user have permission to create a new lead
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_leads >= 2) {
                //permission granted
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][leads][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //assigning a lead and setting its manager
        if (auth()->user()->role->role_assign_leads == 'yes') {
            config(['visibility.lead_modal_assign_fields' => true]);
        } else {
            //assign only to current user and also make manager
            request()->merge([
                'assigned' => [auth()->id()],
            ]);
        }

        //clicked from topnav 'add' button
        if (request('ref') == 'quickadd') {
            config([
                'visibility.lead_show_lead_option' => true,
            ]);
        }

    }
}
