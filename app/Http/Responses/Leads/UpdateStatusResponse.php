<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update status] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class UpdateStatusResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for leads
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

        //card
        $board['leads'] = $leads;
        $html = view('pages/leads/components/kanban/card', compact('board'))->render();

        //update kanban card completely
        if ($old_status == $new_status) {
            $jsondata['dom_html'][] = array(
                'selector' => "#card_lead_" . $leads->first()->lead_id,
                'action' => 'replace-with',
                'value' => $html);
        }

        //update table row
        $html = view('pages/leads/components/table/ajax', compact('leads'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#lead_" . $leads->first()->lead_id,
            'action' => 'replace-with',
            'value' => $html);

        //move card to new board
        if ($old_status != $new_status) {
            //render card
            $html = view('pages/leads/components/kanban/card', compact('board'))->render();

            //remove from current board
            $jsondata['dom_visibility'][] = [
                'selector' => '#card_lead_' . $leads->first()->lead_id,
                'action' => 'hide-remove',
            ];
            //add new board
            $jsondata['dom_html_end'][] = [
                'selector' => '#kanban-board-wrapper-' . $new_status,
                'action' => 'prepend',
                'value' => $html,
            ];
        }

        //update card text
        $jsondata['dom_html'][] = [
            'selector' => '#card-lead-status-text',
            'action' => 'replace',
            'value' => $new_lead_status,
        ];

        //remove loading
        $jsondata['dom_classes'][] = [
            'selector' => '#card-lead-status-text',
            'action' => 'remove',
            'value' => 'loading',
        ];

        //response
        return response()->json($jsondata);

    }

}
