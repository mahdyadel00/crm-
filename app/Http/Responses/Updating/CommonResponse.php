<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [common] process for the user
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Updating;
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

        /** --------------------------------------------
         * show - update currency settings form
         * --------------------------------------------*/
        if ($type == 'show-currency-settings') {
            //render the form
            $html = view('pages/updating/1-updating-currency-settings', compact('settings'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#commonModalBody',
                'action' => 'replace',
                'value' => $html);
            //show modal footer
            $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'show');
        }

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
         * just show a success notification & close modal
         * ---------------------------------------------------------------*/
        if ($type == 'success-notification-close-modal') {
            //notice
            $jsondata['notification'] = [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ];
            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');
        }

        //response
        return response()->json($jsondata);

    }
}
