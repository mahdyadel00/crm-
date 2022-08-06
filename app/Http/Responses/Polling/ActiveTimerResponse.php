<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [timers] process for the polling
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Polling;
use Illuminate\Contracts\Support\Responsable;

class ActiveTimerResponse implements Responsable {

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

        //update active time
        if ($action == 'update') {

            //update the time
            $jsondata['dom_html'][] = array(
                'selector' => '#my-timer-time-topnav',
                'action' => 'replace',
                'value' => runtimeSecondsHumanReadable($seconds, false));

            //update the dropdown details
            $html = view('misc/timer-topnav-details', compact('task'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#active-timer-topnav-container',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        //there is no actve timer
        if ($action == 'hide') {
            $jsondata['dom_visibility'][] = [
                'selector' => '#my-timer-container-topnav',
                'action' => 'hide',
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#my-timer-time-topnav',
                'action' => 'replace',
                'value' => runtimeSecondsHumanReadable(0, false),
            ];
        }

        //skip dom initialization
        $jsondata['skip_dom_reset'] = true;

        //skip tinymce reload
        $jsondata['skip_dom_tinymce'] = true;

        //response
        return response()->json($jsondata);

    }
}
