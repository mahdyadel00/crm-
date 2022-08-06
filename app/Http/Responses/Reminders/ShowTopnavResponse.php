<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the reminder
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reminders;
use Illuminate\Contracts\Support\Responsable;

class ShowTopnavResponse implements Responsable {

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

        //show reminders
        $html = view('pages/reminders/topnav/reminder', compact('reminders'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#topnav-reminders-container',
            'action' => 'replace',
            'value' => $html);

        //do we have reminders
        if ($reminders->count() > 0) {
            $jsondata['dom_visibility'][] = [
                'selector' => '#topnav-reminders-container-footer',
                'action' => 'show',
            ];
        }

        //ajax response
        return response()->json($jsondata);

    }

}
