<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for [various] processes for the clients
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Clients;
use Illuminate\Contracts\Support\Responsable;

class CommonResponse implements Responsable {

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

        //default
        $jsondata = [];

        /** ----------------------------------------------------------------
         * just show a success notification
         * ---------------------------------------------------------------*/
        if ($type == 'success-notification') {
            $jsondata['notification'] = [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ];
        }

        /** ----------------------------------------------------------------
         * just show a success notification
         * ---------------------------------------------------------------*/
        if ($type == 'upload-logo') {
            $jsondata['notification'] = [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ];
            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

            if(request('source') == 'page'){
                $jsondata['redirect_url'] = url("clients/$client_id");;
            }
        }

        //response
        return response()->json($jsondata);

    }

}
