<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the estimates
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Estimates;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for estimates
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
            $html = view('pages/estimates/components/table/ajax', compact('estimates'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#estimate_$bill_estimateid",
                'action' => 'replace-with',
                'value' => $html);

            //close modals
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');
            $jsondata['dom_visibility'][] = array('selector' => '#actionsModal', 'action' => 'close-modal');
        }

        //refresh stats
        if (isset($stats)) {
            $html = view('misc/list-pages-stats-content', compact('stats'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#list-pages-stats-widget',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        //update initiated on the estimate page
        if (request('ref') == 'page') {
            //estimate
            $estimate = $estimates->first();

            //status
            $jsondata['dom_visibility'][] = [
                'selector' => '.js-estimate-statuses',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#estimate-status-' . $estimate->bill_status,
                'action' => 'show',
            ];

            //attach or detattch project
            if (is_numeric($estimate->bill_projectid)) {
                $jsondata['dom_html'][] = [
                    'selector' => '#InvoiceTitleProject',
                    'action' => 'replace',
                    'value' => __('lang.project') . ' - ' . $estimate->project_title,
                ];
                $jsondata['dom_attributes'][] = [
                    'selector' => '#InvoiceTitleAttached',
                    'attr' => 'href',
                    'value' =>  url('projects/'.$estimate->bill_projectid),
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

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
