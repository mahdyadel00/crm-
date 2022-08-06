<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class updateCustomFieldsResponse implements Responsable {

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

        //json output
        $html = view('pages/lead/components/custom-fields', compact('lead', 'fields'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#custom-fields-panel-content',
            'action' => 'replace',
            'value' => $html,
        ];

        //json output
        $html = view('pages/lead/components/custom-fields-edit', compact('lead', 'fields'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#custom-fields-panel-edit-container',
            'action' => 'replace',
            'value' => $html,
        ];


        $jsondata['dom_visibility'][] = [
            'selector' => '#custom-fields-panel',
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#custom-fields-panel-edit',
            'action' => 'show',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#custom-fields-panel-edit',
            'action' => 'show',
        ];

        //response
        return response()->json($jsondata);

    }

}
