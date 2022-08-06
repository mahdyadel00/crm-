<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [pdf] process for the estimates
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Estimates;
use Illuminate\Contracts\Support\Responsable;
use PDF;

class PDFResponse implements Responsable {

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

        //view estimate in browser (https://domain.com/estimate/1/pdf?view=preview)
        if (request('view') == 'preview') {
            config(['css.bill_mode' => 'pdf-mode-preview']);
            return view('pages/bill/bill-pdf', compact('page', 'bill', 'taxrates', 'taxes', 'lineitems', 'elements', 'customfields'))->render();
        }

        //download pdf view
        config(['css.bill_mode' => 'pdf-mode-download']);
        $pdf = PDF::loadView('pages/bill/bill-pdf', compact('page', 'bill', 'taxrates', 'taxes', 'lineitems', 'elements', 'customfields'));
        $filename = strtoupper(__('lang.estimate')) . '-' . $bill->formatted_bill_estimateid . '.pdf'; //estate_inv0001.pdf
        return $pdf->download($filename);
    }
}
