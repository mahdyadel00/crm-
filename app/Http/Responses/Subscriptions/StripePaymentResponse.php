<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the subscription
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Subscriptions;
use Illuminate\Contracts\Support\Responsable;

class StripePaymentResponse implements Responsable {

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

        $html = view('pages/subscription/stripe', compact('session_id', 'subscription', 'interval'))->render();

        if (request('source') == 'list') {
            $jsondata['dom_html'][] = [
                'selector' => '#actionsModalBody',
                'action' => 'replace',
                'value' => $html,
            ];
        } else {
            $jsondata['dom_html'][] = [
                'selector' => '#subscription-pay-container',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        $jsondata['dom_visibility'][] = [
            'selector' => '.subscription-alert',
            'action' => 'hide',
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXStripePaymentButton',
        ];

        return response()->json($jsondata);

    }

}
