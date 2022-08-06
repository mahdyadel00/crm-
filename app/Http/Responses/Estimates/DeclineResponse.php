<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [decline] process for the estimates
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Estimates;
use Illuminate\Contracts\Support\Responsable;

class DeclineResponse implements Responsable {

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

        //defaults
        $jsondata['dom_visibility'][] = [
            'selector' => '.js-estimate-statuses',
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '.buttons-accept-decline ',
            'action' => 'hide',
        ];

        //update status - decline
        $jsondata['dom_visibility'][] = [
            'selector' => '#estimate-status-declined',
            'action' => 'show',
        ];

        //flash notice
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
