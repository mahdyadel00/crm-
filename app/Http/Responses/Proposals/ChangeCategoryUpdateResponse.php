<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [categorry] process for the proposals
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Proposals;
use Illuminate\Contracts\Support\Responsable;

class ChangeCategoryUpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for proposals
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
        foreach ($allrows as $proposals) {
            //proposal id
            $id = $proposals->first()->doc_id;
            //render html
            $html = view('pages/proposals/components/table/ajax', compact('proposals'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#proposal_$id",
                'action' => 'replace-with',
                'value' => $html);
            //check the box again (only for bulk actions)
            if (request('type') == 'bulk') {
                $jsondata['dom_property'][] = [
                    'selector' => '#listcheckbox-proposals-' . $id,
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
