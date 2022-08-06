<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [destroy] process for the reminders
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reminders;
use Illuminate\Contracts\Support\Responsable;

class DestroyCardResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * remove the reminder from the view
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

        $html = view('pages/reminders/cards/wrapper', compact('payload'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#card-reminders-container',
            'action' => 'replace',
            'value' => $html,
        ];
            
        //response
        return response()->json($jsondata);

    }

}
