<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [check] process for the updates settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Updates;
use Illuminate\Contracts\Support\Responsable;

class CheckResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for updates
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //default, hide all elements
        $jsondata['dom_visibility'][] = [
            'selector' => '.updates-card ',
            'action' => 'hide',
        ];

        //a server error
        if ($type == 'server-error') {
            //show error message
            $jsondata['dom_visibility'][] = [
                'selector' => '#updates-server-error',
                'action' => 'show',
            ];
        }

        //license key
        if ($type == 'license-error') {
            //show error message
            $jsondata['dom_visibility'][] = [
                'selector' => '#updates-invalid-purchase-code',
                'action' => 'show',
            ];
        }

        //generic error
        if ($type == 'generic-error') {
            //show error message
            $jsondata['dom_visibility'][] = [
                'selector' => "#$dom",
                'action' => 'show',
            ];
        }

        //success
        if ($type == 'success') {
            //show error message
            $jsondata['dom_visibility'][] = [
                'selector' => "#updates-available",
                'action' => 'show',
            ];
            $jsondata['dom_attributes'][] = array(
                'selector' => '#updated-download-link',
                'attr' => 'href',
                'value' => $url);
            $jsondata['dom_html'][] = [
                'selector' => '#updated-current-version',
                'action' => 'replace',
                'value' => $update_version,
            ];
        }

        //ajax response
        return response()->json($jsondata);
    }
}
