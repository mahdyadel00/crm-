<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the invoices
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Invoices;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

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

        //update initiated on a list page
        if (request('ref') == 'list') {
            //replace the row of this record
            $html = view('pages/invoices/components/table/ajax', compact('invoices'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#invoice_" . $invoices->first()->bill_invoiceid,
                'action' => 'replace-with',
                'value' => $html);

            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '.modal', 'action' => 'close-modal');

            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        }

        //update initiated on the invoice page
        if (request('ref') == 'page') {
            //invoice
            $invoice = $invoices->first();

            //recurring icon
            $jsondata['dom_visibility'][] = [
                'selector' => '#invoice-recurring-icon',
                'action' => ($invoice->bill_recurring == 'yes') ? 'show' : 'hide',
            ];

            //recurring icon
            $jsondata['dom_visibility'][] = [
                'selector' => '#invoice-action-view-children',
                'action' => ($invoice->bill_recurring == 'yes') ? 'show' : 'hide',
            ];
            //recurring icon
            $jsondata['dom_visibility'][] = [
                'selector' => '#invoice-action-stop-recurring',
                'action' => ($invoice->bill_recurring == 'yes') ? 'show' : 'hide',
            ];

            //reset status
            $jsondata['dom_visibility'][] = [
                'selector' => '.js-invoice-statuses',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#invoice-status-' . $invoice->bill_status,
                'action' => 'show',
            ];

            //attach or detattch project
            if (is_numeric($invoice->bill_projectid)) {
                $jsondata['dom_html'][] = [
                    'selector' => '#InvoiceTitleProject',
                    'action' => 'replace',
                    'value' => __('lang.project') . ' - ' . safestr($invoice->project_title),
                ];
                $jsondata['dom_attributes'][] = [
                    'selector' => '#InvoiceTitleAttached',
                    'attr' => 'href',
                    'value' =>  url('invoices/'.$invoice->bill_projectid),
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#InvoiceTitleAttached',
                    'action' => 'show',
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#InvoiceTitleNotAttached',
                    'action' => 'hide',
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#bill-actions-attach-project',
                    'action' => 'hide',
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#bill-actions-dettach-project',
                    'action' => 'show',
                ];
            } else {
                $jsondata['dom_visibility'][] = [
                    'selector' => '#InvoiceTitleAttached',
                    'action' => 'hide',
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#InvoiceTitleNotAttached',
                    'action' => 'show',
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#bill-actions-attach-project',
                    'action' => 'show',
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#bill-actions-dettach-project',
                    'action' => 'hide',
                ];
            }

            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '.modal', 'action' => 'close-modal');
        }

        //response
        return response()->json($jsondata);

    }

}
