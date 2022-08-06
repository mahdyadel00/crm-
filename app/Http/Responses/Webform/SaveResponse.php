<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the webforms settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Webform;
use Illuminate\Contracts\Support\Responsable;

class SaveResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for webforms
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //error saving form
        if ($type == 'error') {
            $jsondata['dom_visibility'][] = [
                'selector' => '#webform',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#webform-system-error',
                'action' => 'show',
            ];
        }

        //error required fields
        if ($type == 'error-required-fields') {
            $jsondata['dom_visibility'][] = [
                'selector' => '#webform-errors-wrapper',
                'action' => 'show',
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#webform-errors',
                'action' => 'replace',
                'value' => $error_message,
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#webform-buttons-container',
                'action' => 'remove',
                'value' => 'loading',
            ];
        }

        //error required fields
        
        $fallback_thankyou = '<div class="text-center p-t-40"><h4>'.__('lang.thank_you_form_submitted').'</h4></div>';
        if ($type == 'success') {
            $jsondata['dom_visibility'][] = [
                'selector' => '#webform',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#webform-submit-success',
                'action' => 'show',
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#webform-submit-success',
                'action' => 'replace',
                'value' => ($webform->webform_thankyou_message != '')? $webform->webform_thankyou_message : $fallback_thankyou,
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#webform-buttons-container',
                'action' => 'remove',
                'value' => 'loading',
            ];
        }

        //skip refresh for all
        $jsondata['skip_dom_reset'] = true;

        //ajax response
        return response()->json($jsondata);
    }
}
