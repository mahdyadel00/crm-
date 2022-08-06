<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the lead
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class ActivateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $jsondata = [];

        //update initiated on a list page
        if (request('ref') == 'list' || request('ref') == '') {

            //table view
            $html = view('pages/leads/components/table/ajax', compact('leads'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#lead_" . $leads->first()->lead_id,
                'action' => 'replace-with',
                'value' => $html);

            //hide and show buttons
            $jsondata['dom_classes'][] = [
                'selector' => ".card_archive_button_" . $leads->first()->lead_id,
                'action' => 'remove',
                'value' => 'hidden',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => ".card_restore_button_" . $leads->first()->lead_id,
                'action' => 'add',
                'value' => 'hidden',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => "#archived_icon_" . $leads->first()->lead_id,
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => "#card_archived_notice_" . $leads->first()->lead_id,
                'action' => 'hide',
            ];

            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
        }

        //editing from main page
        if (request('ref') == 'page') {
            //session
            request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
            //redirect to lead page
            $jsondata['redirect_url'] = url("lead/" . $lead->first()->lead_id);
        }

        //response
        return response()->json($jsondata);
    }

}
