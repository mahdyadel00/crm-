<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class updateCustomFieldsResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for tasks
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
        $html = view('pages/task/components/custom-fields', compact('task', 'fields'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#custom-fields-panel-content',
            'action' => 'replace',
            'value' => $html,
        ];

        //json output
        $html = view('pages/task/components/custom-fields-edit', compact('task', 'fields'))->render();
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
