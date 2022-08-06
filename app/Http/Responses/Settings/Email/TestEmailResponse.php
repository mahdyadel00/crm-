<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the email settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Email;
use Illuminate\Contracts\Support\Responsable;

class TestEmailResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for projects
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        /** --------------------------------------------
         * show email testing form
         * --------------------------------------------*/
        if ($section == 'form') {
            //render the form
            $html = view('pages/settings/sections/email/modals/test-email', compact('show'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#commonModalBody',
                'action' => 'replace',
                'value' => $html);

            // POSTRUN FUNCTIONS------
            $jsondata['postrun_functions'][] = [
                'value' => 'NXEmailSettingTest',
            ];

            //show modal footer
            if ($show == 'form') {
                $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'show');
            } else {
                $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'hide');
            }
        }

        /** --------------------------------------------
         * success
         * --------------------------------------------*/
        if ($section == 'success') {

            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

            //success
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
        }

        //response
        return response()->json($jsondata);

    }

}
