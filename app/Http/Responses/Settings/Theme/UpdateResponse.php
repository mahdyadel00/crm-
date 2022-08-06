<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the theme settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Theme;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for themes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //notice
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        $jsondata['redirect_url'] = url('app/settings/theme');

        //response
        return response()->json($jsondata);

    }
}
