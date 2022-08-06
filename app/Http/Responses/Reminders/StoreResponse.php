<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the reminder
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Reminders;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

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

        $html = view('pages/reminders/misc/show', compact('reminder', 'payload'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#reminders-side-panel-body',
            'action' => 'replace',
            'value' => $html);

        //remove any class on the reminder button
        $jsondata['dom_classes'][] = [
            'selector' => '#reminders-panel-toggle-button',
            'action' => 'remove',
            'value' => 'due',
        ];

        //remove any class on the reminder button
        $jsondata['dom_classes'][] = [
            'selector' => '#reminders-panel-toggle-button',
            'action' => 'add',
            'value' => 'active',
        ];

        //show delete button
        $jsondata['dom_visibility'][] = [
            'selector' => '#delete_reminder',
            'action' => 'show',
        ];

        //response
        return response()->json($jsondata);

    }

}
