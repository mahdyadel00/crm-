<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [timer] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class TimerStartResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for bars
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //reset and show top nav timer
        $jsondata['dom_visibility'][] = [
            'selector' => '#my-timer-container-topnav',
            'action' => 'show',
        ];
        $jsondata['dom_html'][] = [
            'selector' => '#my-timer-time-topnav',
            'action' => 'replace',
            'value' => runtimeSecondsHumanReadable(0, false),
        ];

        //update the dropdown details
        $html = view('misc/timer-topnav-details', compact('task'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#active-timer-topnav-container',
            'action' => 'replace',
            'value' => $html,
        ];

        //response
        return response()->json($jsondata);
    }

}
