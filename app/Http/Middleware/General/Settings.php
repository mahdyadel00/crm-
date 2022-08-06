<?php

/** ---------------------------------------------------------------------------------------------------------------
 * The purpose of this middleware it to set fallback config values
 * for older versions of Grow CRM that are upgrading. Reason being that new
 * values in the file /config/settings.php will not exist for them (as settings files in not included in updates)
 *
 *
 *
 * @package    Grow CRM
 * @author     NextLoop
 * @revised    9 July 2021
 *--------------------------------------------------------------------------------------------------------------*/

namespace App\Http\Middleware\General;
use Closure;

class Settings {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        /**
         * how many rows to show in settings. Defaults to a hard set value, if not present
         * fallback value: 5
         */
        config(['settings.custom_fields_display_limit' => config('settings.custom_fields_display_limit') ?? 5]);

        /**
         * set task statuses
         */
        if (env('SETUP_STATUS') == 'COMPLETED') {
            $statuses = \App\Models\TaskStatus::orderBy('taskstatus_position', 'ASC')->get();
            config(['task_statuses' => $statuses]);
            $arr = [];
            foreach ($statuses as $status) {
                $arr[] = $status->taskstatus_id;
            }
            config(['task_statuses_array' => $arr]);
        }

        return $next($request);

    }

}