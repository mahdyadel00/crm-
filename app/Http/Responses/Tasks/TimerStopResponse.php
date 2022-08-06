<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [timer] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class TimerStopResponse implements Responsable {

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

        //default
        $jsondata = [];

        if (isset($task_id) && is_numeric($task_id)) {
            //hide top buttons
            $jsondata['dom_visibility'][] = [
                'selector' => '#timer_button_stop_table_' . $task_id,
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#timer_button_stop_card_' . $task_id,
                'action' => 'hide',
            ];

            //show start buttons
            $jsondata['dom_visibility'][] = [
                'selector' => '#timer_button_start_table_' . $task_id,
                'action' => 'show',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#timer_button_start_card_' . $task_id,
                'action' => 'show',
            ];

            //removing running timers
            $jsondata['dom_classes'][] = array(
                'selector' => '#task_timer_table_' . $task_id,
                'action' => 'remove',
                'value' => 'timer-running');
            $jsondata['dom_classes'][] = array(
                'selector' => '#task_timer_card_' . $task_id,
                'action' => 'remove',
                'value' => 'timer-running');
            $jsondata['dom_visibility'][] = [
                'selector' => '#card-task-timer-' . $task_id,
                'action' => 'hide',
            ];

        }

        //reset and hide top nav timer
        $jsondata['dom_visibility'][] = [
            'selector' => '#my-timer-container-topnav',
            'action' => 'hide',
        ];
        $jsondata['dom_html'][] = [
            'selector' => '#my-timer-time-topnav',
            'action' => 'replace',
            'value' => runtimeSecondsHumanReadable(0, false),
        ];
        //update the dropdown details
        $jsondata['dom_html'][] = [
            'selector' => '#active-timer-topnav-container',
            'action' => 'replace',
            'value' => '',
        ];

        //response
        return response()->json($jsondata);

    }

}
