<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store reply] process for the tickets
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tickets;
use Illuminate\Contracts\Support\Responsable;

class StoreReplyResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for tickets
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //prepend use on top of list
        $html = view('pages/ticket/components/replies', compact('replies'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#ticket-replies-container',
            'action' => 'append',
            'value' => $html);

        //update left panel
        $html = view('pages/ticket/components/panel', compact('ticket'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#ticket-left-panel",
            'action' => 'replace-with',
            'value' => $html);

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //response
        return response()->json($jsondata);

    }

}
