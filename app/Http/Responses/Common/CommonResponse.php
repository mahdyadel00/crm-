<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for various controllers
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Common;
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

        $jsondata = [];

        /** ----------------------------------------------------------------
         * generic, basic removal or element from a list. Slide up and
         * hide
         * ---------------------------------------------------------------*/
        if ($type == 'remove-basic') {
            $jsondata['dom_visibility'][] = array(
                'selector' => $element,
                'action' => 'slideup-remove',
            );
        }

        /** ----------------------------------------------------------------
         * user has marked an event notification as 'read'
         *      - remove the element
         *      - if no more notifications, remove the flashing icon on bell
         * ---------------------------------------------------------------*/
        if ($type == 'remove-my-event') {
            if (isset($count) && $count == 0) {
                //hide footer
                $jsondata['dom_visibility'][] = [
                    'selector' => '#sidepanel-notifications-mark-all-read',
                    'action' => 'hide',
                ];
                //hide notification is applicable
                $jsondata['dom_visibility'][] = array(
                    'selector' => "#topnav-notification-icon",
                    'action' => 'hide',
                );
            }
        }

        /** ----------------------------------------------------------------
         * user is marking all notifications as 'read'
         *      - remove the element
         *      - if no more notifications, remove the flashing icon on bell
         * ---------------------------------------------------------------*/
        if ($type == 'remove-all-my-events') {
            //clear whole notificatons panel
            $jsondata['dom_html'][] = [
                'selector' => '#topnav-events-container',
                'action' => 'replace',
                'value' => '',
            ];
            //hide notification is applicable
            if (isset($count) && $count > 0) {
                $jsondata['dom_visibility'][] = array(
                    'selector' => "#topnav-notification-icon",
                    'action' => 'hide',
                );
            }
            //hide footer
            $jsondata['dom_visibility'][] = [
                'selector' => '#topnav-events-container-footer',
                'action' => 'hide',
            ];
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

        //response
        return response()->json($jsondata);

    }
}
