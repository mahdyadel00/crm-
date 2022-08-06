<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for home
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Home;

use Closure;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] foos
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //set lead statuses
        $this->leadStatuses();

        return $next($request);

    }

    //set lead statuses
    private function leadStatuses() {

        $lead_statuses = [];

        //lead statuses
        $statuses = \App\Models\LeadStatus::orderBy('leadstatus_position', 'asc')->get();

        foreach ($statuses as $status) {
            array_push($lead_statuses, [
                'id' => $status->leadstatus_id,
                'title' => $status->leadstatus_title,
                'color' => 'text-' . $status->leadstatus_color,
                'label' => 'label-' . $status->leadstatus_color,
                'colorcode'=> runtimeColorCode($status->leadstatus_color)
            ]);
        }

        config(['home.lead_statuses' => $lead_statuses]);

    }

}
