<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the subscription
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Subscriptions;
use Illuminate\Contracts\Support\Responsable;

class CreateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for subscription members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //render the form
        $html = view('pages/subscriptions/components/modals/add-edit-inc', compact('page', 'show', 'message', 'products', 'categories'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html);

        //show modal subscriptionter
        $jsondata['dom_visibility'][] = array('selector' => '#commonModalSubscriptionter', 'action' => 'show');

        //error state
        if ($show == 'form') {
            $jsondata['dom_visibility'][] = [
                'selector' => '#commonModalSubmitButton',
                'action' => 'show',
            ];
        } else {
            $jsondata['dom_visibility'][] = [
                'selector' => '#commonModalSubmitButton',
                'action' => 'hide',
            ];
        }

        //hide actions button
        if (count($products) > 0) {
            $jsondata['dom_visibility'][] = [
                'selector' => '#commonModalFooter',
                'action' => 'show',
            ];
        }else{
            $jsondata['dom_visibility'][] = [
                'selector' => '#commonModalFooter',
                'action' => 'hide',
            ];
        }

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXSubscriptionCreate',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
