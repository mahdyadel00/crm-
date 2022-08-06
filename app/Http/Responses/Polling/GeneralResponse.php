<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [general] process for the polling
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Polling;
use Illuminate\Contracts\Support\Responsable;

class GeneralResponse implements Responsable {

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

        $jsondata = [];

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        /** ----------------------------------------------------------------
         * General polling
         * ---------------------------------------------------------------*/
        if ($type == 'general') {

            //update notifications icon
            if ($notifications_count > 0) {
                $jsondata['dom_visibility'][] = array(
                    'selector' => "#topnav-notification-icon",
                    'action' => 'show',
                );
            } else {
                $jsondata['dom_visibility'][] = array(
                    'selector' => "#topnav-notification-icon",
                    'action' => 'hide',
                );
            }

            //reminders
            if ($count_reminders > 0) {
                $jsondata['dom_visibility'][] = array(
                    'selector' => "#topnav-reminders-dropdown",
                    'action' => 'show',
                );
            } else {
                $jsondata['dom_visibility'][] = array(
                    'selector' => "#topnav-reminders-dropdown",
                    'action' => 'hide',
                );
            }
        }

        //skip tinymce reload
        $jsondata['skip_dom_tinymce'] = true;

        //skip dom initialization
        $jsondata['skip_dom_reset'] = true;

        //response
        return response()->json($jsondata);

    }
}
