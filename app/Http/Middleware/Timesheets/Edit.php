<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for product timesheets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Timesheets;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] timesheets
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //timesheet id
        $timesheet_id = $request->route('timesheet');

        //does the timesheet exist
        if ($timesheet_id == '' || !$timesheet = \App\Models\Timer::Where('timer_id', $timesheet_id)->first()) {
            Log::error("timesheet could not be found", ['process' => '[permissions][timesheets][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'timesheet id' => $timesheet_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit timesheets
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_timesheets >= 2) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][timesheets][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
