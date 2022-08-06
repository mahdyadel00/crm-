<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the estimates
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Estimates;
use Illuminate\Contracts\Support\Responsable;

class DocumentEditingResponse implements Responsable {

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

        $html = view('pages/bill/bill-web', compact('page', 'bill', 'taxrates', 'taxes', 'elements', 'units', 'lineitems', 'customfields'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#documents-side-panel-billing-content',
            'action' => 'replace',
            'value' => $html,
        ];

        //visibility of infomation panel
        $jsondata['dom_visibility'][] = [
            'selector' => '#documents-side-panel-billing-info',
            'action' => 'show',
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXDOCEstimateInitialise',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
