<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the leads
 * controller
 *
 * [IMPORTANT] All Left Panel code must be reproduced in the file ContentResponse.php
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class CloneStoreResponse implements Responsable {

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

        //full payload array
        $payload = $this->payload;

        //kanban - add a new card
        $board['leads'] = $leads;
        $html = view('pages/leads/components/kanban/card', compact('board'))->render();
        $jsondata['dom_html_end'][] = [
            'selector' => '#kanban-board-wrapper-' . $lead->lead_status,
            'action' => 'prepend',
            'value' => $html,
        ];

        //table - add a new row
        $html = view('pages/leads/components/table/ajax', compact('leads'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#leads-td-container',
            'action' => 'prepend',
            'value' => $html);

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
