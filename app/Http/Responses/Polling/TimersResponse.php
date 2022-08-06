<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [timers] process for the polling
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Polling;
use Illuminate\Contracts\Support\Responsable;

class TimersResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * various common responses. Add more as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        $jsondata = [];

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        /** ----------------------------------------------------------------
         * General Timers
         * ---------------------------------------------------------------*/
        // update timers which were stopped ....see Tasks controller for explanation
        foreach ($timers as $timer) {
            //list timer
            $jsondata['dom_html'][] = array(
                'selector' => '#task_timer_table_' . $timer->timer_taskid,
                'action' => 'replace',
                'value' => runtimeSecondsHumanReadable($timer->timers_sum, config('settings.timers.display_seconds')));
            //card timer
            $jsondata['dom_html'][] = array(
                'selector' => '#task_timer_card_' . $timer->timer_taskid,
                'action' => 'replace',
                'value' => runtimeSecondsHumanReadable($timer->timers_sum, config('settings.timers.display_seconds')));
        }

        /** ----------------------------------------------------------------
         * Topnav active timer
         * ---------------------------------------------------------------*/
        if ($update_top_nav_timer) {
            $jsondata['dom_html'][] = array(
                'selector' => '#my-timer-time-topnav',
                'action' => 'replace',
                'value' => runtimeSecondsHumanReadable($seconds, false));
        }

        //skip dom initialization
        $jsondata['skip_dom_reset'] = true;

        //skip tinymce reload
        $jsondata['skip_dom_tinymce'] = true;

        //response
        return response()->json($jsondata);

    }
}
