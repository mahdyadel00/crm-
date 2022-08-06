<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the payments
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Payments;
use Illuminate\Contracts\Support\Responsable;

class ShowResponse implements Responsable {

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

        //content & title
        $html = view('pages/payments/components/modals/show-payment', compact('payment'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#plainModalBody',
            'action' => 'replace',
            'value' => $html);
        $jsondata['dom_html'][] = array(
            'selector' => '#plainModalTitle',
            'action' => 'replace',
            'value' => __('lang.payment'));

        //update browser url
        $jsondata['dom_browser_url'] = [
            'title' => __('lang.payment') . ' - ' . $payment->payment_title,
            'url' => url("/payments/v/" . $payment->payment_id),
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
