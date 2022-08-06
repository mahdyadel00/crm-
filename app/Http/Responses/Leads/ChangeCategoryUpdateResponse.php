<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [category] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class ChangeCategoryUpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for leads
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //generate a new for for each record
        foreach ($allrows as $leads) {
            //lead id
            $id = $leads->first()->lead_id;
            //render html
            $html = view('pages/leads/components/table/ajax', compact('leads'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#lead_$id",
                'action' => 'replace-with',
                'value' => $html);
            //check the box again (only for bulk actions)
            if (request('type') == 'bulk') {
                $jsondata['dom_property'][] = [
                    'selector' => '#listcheckbox-leads-' . $id,
                    'prop' => 'checked',
                    'value' => true,
                ];
            }
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#actionsModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
