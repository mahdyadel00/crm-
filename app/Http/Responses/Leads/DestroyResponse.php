<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [destroy] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class DestroyResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * remove the item from the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //hide and remove all deleted rows
        foreach ($allrows as $id) {
            $jsondata['dom_visibility'][] = array(
                'selector' => '#lead_' . $id,
                'action' => 'slideup-slow-remove',
            );
        }

        //hide and remove all deleted cards (if kanbanview)
        foreach ($allrows as $id) {
            $jsondata['dom_visibility'][] = array(
                'selector' => '#card_lead_' . $id,
                'action' => 'slideup-slow-remove',
            );
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //close lead modal(if applicable)
        $jsondata['dom_visibility'][] = array('selector' => '#cardModal', 'action' => 'close-modal');

        //response
        return response()->json($jsondata);

    }

}
