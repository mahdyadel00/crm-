<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the milestones settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Milestones;
use Illuminate\Contracts\Support\Responsable;

class UpdateCategoryResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for bars
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //full payload array
        $payload = $this->payload;

        //replace the row of this record
        $html = view('pages/settings/sections/milestones/table/ajax', compact('milestones'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#milestone_" . $milestones->first()->milestonecategory_id,
            'action' => 'replace-with',
            'value' => $html);

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '.modal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
