<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class UpdateErrorResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for tasks
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //full payload array
        $payload = $this->payload;

        //replace any text or html in the card
        if ($reset_target != '' && isset($reset_value)) {
            $jsondata['dom_html'][] = array(
                'selector' => "$reset_target",
                'action' => 'replace',
                'value' => $reset_value);
            $jsondata['dom_classes'][] = array(
                'selector' => "$reset_target",
                'action' => 'remove',
                'value' => 'loading');
        }

        //replace any text or html in the card
        if ($reset_target2 != '' && isset($reset_value2)) {
            $jsondata['dom_html'][] = array(
                'selector' => "$reset_target2",
                'action' => 'replace',
                'value' => $reset_value2);
            $jsondata['dom_classes'][] = array(
                'selector' => "$reset_target2",
                'action' => 'remove',
                'value' => 'loading');
        }

        //[other] update value
        if (isset($type) && $type == 'update-value') {
            $jsondata['dom_attributes'][] = array(
                'selector' => '#card-lead-value',
                'attr' => 'data-value',
                'value' => $value);
        }

        //[other] update nname
        if (isset($type) && $type == 'update-name') {
            $jsondata['dom_classes'][] = array(
                'selector' => '#card-lead-element-container-name',
                'action' => 'remove',
                'value' => 'loading');
        }

        //[other] update phone
        if (isset($type) && $type == 'update-phone') {
            $jsondata['dom_classes'][] = array(
                'selector' => '#card-lead-phone',
                'action' => 'remove',
                'value' => 'loading');
        }

        //[other] update email
        if (isset($type) && $type == 'update-email') {
            $jsondata['dom_classes'][] = array(
                'selector' => '#card-lead-email',
                'action' => 'remove',
                'value' => 'loading');
        }

        //[other] update email
        if (isset($type) && $type == 'update-source') {
            $jsondata['dom_classes'][] = array(
                'selector' => '#card-lead-source-text',
                'action' => 'remove',
                'value' => 'loading');
        }


        //[other] store checklist
        if (isset($type) && $type == 'store-checklist') {
            $jsondata['dom_classes'][] = array(
                'selector' => '#checklist-submit-button',
                'action' => 'remove',
                'value' => 'button-loading-annimation');
            $jsondata['dom_property'][] = array(
                'selector' => '#checklist-submit-button',
                'prop' => 'disabled',
                'value' => false);
        }

        //show and error message
        if ($error_message != '') {
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => $error_message,
            ];
        }

        //response
        return response()->json($jsondata);

    }
}
