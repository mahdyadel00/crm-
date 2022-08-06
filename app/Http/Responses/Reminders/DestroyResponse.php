<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [destroy] process for the reminders
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reminders;
use Illuminate\Contracts\Support\Responsable;

class DestroyResponse implements Responsable {

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

        $html = view('pages/reminders/misc/none-found', compact('payload'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#reminders-side-panel-body',
            'action' => 'replace',
            'value' => $html);

        //remove button highlights
        $jsondata['dom_classes'][] = [
            'selector' => '.reminder-toggle-panel-button',
            'action' => 'remove',
            'value' => 'active',
        ];

        //remove button highlights
        $jsondata['dom_classes'][] = [
            'selector' => '.reminder-toggle-panel-button',
            'action' => 'remove',
            'value' => 'due',
        ];

        //response
        return response()->json($jsondata);

    }

}
