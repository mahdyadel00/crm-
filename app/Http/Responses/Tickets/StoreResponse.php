<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the tickets
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tickets;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for tickets
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //success
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
        
        //redirect to ticket
        $jsondata['redirect_url'] = url('tickets');

        //response
        return response()->json($jsondata);

    }

}
