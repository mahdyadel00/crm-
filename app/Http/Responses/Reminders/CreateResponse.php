<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the reminder
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reminders;
use Illuminate\Contracts\Support\Responsable;

class CreateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for reminder members
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

        //show formz
        $html = view('pages/reminders/misc/reminder', compact('payload'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#reminders-side-panel-body',
            'action' => 'replace',
            'value' => $html);

        //reminder action
        $jsondata['dom_val'][] = [
            'selector' => '#reminder_action',
            'value' => 'new',
        ];

        //hide delete buttons
        $jsondata['dom_visibility'][] = [
            'selector' => '#delete_reminder',
            'action' => 'hide',
        ];

        //new datepicker
        $jsondata['postrun_functions'][] = [
            'value' => 'NXremindersDatePicker',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
