<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [common] process for the user
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\User;
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

        /** -------------------------------------------------------
         * Update users avatar
         * -------------------------------------------------------*/
        if ($type == 'update-avatar') {
            //update profile avatart source image
            $jsondata['dom_attributes'][] = [
                'selector' => '#topnav_avatar',
                'attr' => 'src',
                'value' => $img_source,
            ];
            $jsondata['dom_attributes'][] = [
                'selector' => '#topnav_dropdown_avatar',
                'attr' => 'src',
                'value' => $img_source,
            ];
            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');
            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
        }

        /** --------------------------------------------
         * show update password form
         * --------------------------------------------*/
        if ($type == 'update-password-form') {
            //render the form
            $html = view('pages/user/modals/update-password')->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#commonModalBody',
                'action' => 'replace',
                'value' => $html);

            // POSTRUN FUNCTIONS------
            $jsondata['postrun_functions'][] = [
                'value' => 'NXUserUpdatePassword',
            ];

            //show modal footer
            $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'show');
        }

        /** -------------------------------------------------------
         * Update users password
         * -------------------------------------------------------*/
        if ($type == 'update-password-action') {
            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');
            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
        }

        /** --------------------------------------------
         * show update notifications form
         * --------------------------------------------*/
        if ($type == 'update-notifications-form') {
            //render the form
            $html = view('pages/user/modals/update-notifications')->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#commonModalBody',
                'action' => 'replace',
                'value' => $html);

            //show modal footer
            $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'show');
        }

        /** -------------------------------------------------------
         * Update users notifcation
         * -------------------------------------------------------*/
        if ($type == 'update-notifications-action') {
            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');
            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
        }

        /** -------------------------------------------------------
         * Update users language
         * -------------------------------------------------------*/
        if ($type == 'update-language') {
            request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
            $jsondata['redirect_url'] = request('current_url');
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
