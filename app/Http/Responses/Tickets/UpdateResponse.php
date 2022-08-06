<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the tickets
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tickets;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

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

        //update the list row
        if ($edit_source == 'list') {
            //replace the row of this record
            $html = view('pages/tickets/components/table/ajax', compact('tickets'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#ticket_" . $tickets->first()->ticket_id,
                'action' => 'replace-with',
                'value' => $html);

            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');
            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
        }

        //edit left panel
        if ($edit_source == 'leftpanel') {
            $jsondata['redirect_url'] = url("tickets/$ticket_id");
        }

        //general page edits
        if ($edit_source == 'page') {
            //redirect to ticket page
            $jsondata['redirect_url'] = url('tickets/' . $ticket_id);
        }

        //response
        return response()->json($jsondata);

    }

}
