<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
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

        //show and error message
        if ($error_message != '') {
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => $error_message,
            ];
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

        //response
        return response()->json($jsondata);

    }
}
