<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the reminder
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reminders;
use Illuminate\Contracts\Support\Responsable;

class EditCardResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $payload = $this->payload;

        //show active calender
        $html = view('pages/reminders/misc/reminder', compact('payload', 'reminder'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#card-reminders-container',
            'action' => 'replace',
            'value' => $html,
        ];

        
        //new datepicker
        $jsondata['postrun_functions'][] = [
            'value' => 'NXremindersDatePicker',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
