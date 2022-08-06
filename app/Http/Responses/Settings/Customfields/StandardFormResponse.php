<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the temp settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Customfields;
use Illuminate\Contracts\Support\Responsable;

class StandardFormResponse implements Responsable {

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

        $payload = $this->payload;

        $template = 'pages/settings/sections/customfields/standard-form';
        $html = view($template, compact('page', 'fields', 'payload'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#settings-wrapper",
            'action' => 'replace',
            'value' => $html);

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXSettingsStandardFormDragDrop',
        ];

        //ajax response
        return response()->json($jsondata);
    }
}
