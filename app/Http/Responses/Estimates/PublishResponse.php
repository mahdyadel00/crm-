<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [publish] process for the estimates
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Estimates;
use Illuminate\Contracts\Support\Responsable;

class PublishResponse implements Responsable {

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

        //update status (due or overdue)
        $jsondata['dom_visibility'][] = [
            'selector' => '#estimate-status-draft',
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#estimate-status-new',
            'action' => 'show',
        ];

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }
}
