<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the logos settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Logos;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $jsondata['redirect_url'] = url('app/settings/logos');

        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        //ajax response
        return response()->json($jsondata);

    }

}
