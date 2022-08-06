<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the payments
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Payments;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for payments
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //prepend content on top of list or show full table
        if ($count == 1) {
            $html = view('pages/payments/components/table/table', compact('payments'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#payments-table-wrapper',
                'action' => 'replace',
                'value' => $html);
        } else {
            //prepend content on top of list
            $html = view('pages/payments/components/table/ajax', compact('payments'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#payments-td-container',
                'action' => 'prepend',
                'value' => $html);
        }

        //update invoice table row
        $html = view('pages/invoices/components/table/ajax', compact('invoices'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#invoice_" . $invoices->first()->bill_invoiceid,
            'action' => 'replace-with',
            'value' => $html);

        //show payment after adding
        if (request('ref') == 'quickadd' && request('show_after_adding') == 'on') {
            $jsondata['redirect_url'] = url("/payments/v/" . $payment->payment_id);
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
