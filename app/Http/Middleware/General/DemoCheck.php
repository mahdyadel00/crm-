<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [demo] precheck processes
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\General;

use Closure;

class DemoCheck {

    /**
     *  Check if we are in demo mode
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //permission: does user have permission for settings
        if (config('app.application_demo_mode')) {
            abort(409, __('lang.this_feature_is_disabled_in_demo'));
        }
        return $next($request);
    }
}
