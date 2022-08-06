<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [publish] process for the estimates
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Estimates;
use Illuminate\Contracts\Support\Responsable;

class PublishRevisedResponse implements Responsable {

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

        //hide publish button
        $jsondata['dom_visibility'][] = [
            'selector' => '#estimate-action-publish-estimate',
            'action' => 'hide',
        ];


        //change status
        $jsondata['dom_visibility'][] = [
            'selector' => '.js-estimate-statuses',
            'action' => 'hide',
        ];
        //update status - accepted
        $jsondata['dom_visibility'][] = [
            'selector' => '#estimate-status-revised',
            'action' => 'show',
        ];

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }
}
