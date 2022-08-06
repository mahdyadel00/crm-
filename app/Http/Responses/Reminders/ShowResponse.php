<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the reminder
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reminders;
use Illuminate\Contracts\Support\Responsable;

class ShowResponse implements Responsable {

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

        //none found - create a new one
        if ($status == 'none-found') {
            $html = view('pages/reminders/misc/none-found', compact('payload'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#reminders-side-panel-body',
                'action' => 'replace',
                'value' => $html);
        }

        //none found - create a new one
        if ($status == 'found') {
            $html = view('pages/reminders/misc/show', compact('reminder', 'payload'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#reminders-side-panel-body',
                'action' => 'replace',
                'value' => $html);
        }

        //ajax response
        return response()->json($jsondata);

    }

}
