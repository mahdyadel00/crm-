<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [stripe] process for the pay
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Pay;
use Illuminate\Contracts\Support\Responsable;

class StripePaymentResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for invoices
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //generate paynow button
        $html = view('pages/pay/stripe', compact('session_id'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#invoice-paynow-buttons-container',
            'action' => 'replace',
            'value' => $html);

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXStripePaymentButton',
        ];

        //response
        return response()->json($jsondata);
    }

}
