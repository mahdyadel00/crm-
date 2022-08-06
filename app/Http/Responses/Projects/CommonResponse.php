<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [common] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class CommonResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * various common responses. Add more as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //success notification
        if ($type == 'success-notification') {
            $jsondata['notification'] = [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ];
        }

        //error notification
        if ($type == 'error-notification') {
            $jsondata['notification'] = [
                'type' => 'success',
                'value' => __('lang.request_could_not_be_completed'),
            ];
        }

        //response
        return response()->json($jsondata);

    }
}
