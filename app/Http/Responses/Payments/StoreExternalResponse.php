<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the payments
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Payments;
use Illuminate\Contracts\Support\Responsable;

class StoreExternalResponse implements Responsable {

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

        //request came from invoice list page, so replace the row with new
        if (request('source') == 'list' || request('source') == '') {
            $html = view('pages/invoices/components/table/ajax', compact('invoices'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#invoice_" . $id,
                'action' => 'replace-with',
                'value' => $html);

            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
            
        } else {
            //success message
            request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
            //add overlay while page redirects
            $jsondata['dom_classes'][] = [
                'selector' => '#invoice-container',
                'action' => 'add',
                'value' => 'overlay',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#invoice-wrapper',
                'action' => 'add',
                'value' => 'loading',
            ];
            //request came from invoice page, reload page
            $jsondata['delayed_redirect_url'] = url("/invoices/$id");
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //response
        return response()->json($jsondata);

    }

}
