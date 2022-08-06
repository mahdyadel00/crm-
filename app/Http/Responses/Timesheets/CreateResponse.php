<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [edit] process for the timesheets
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\timesheets;
use Illuminate\Contracts\Support\Responsable;

class CreateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for timesheets
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //render the form
        $html = view('pages/timesheets/components/modals/record-time')->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html);

        //show modal timesheetter
        $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'show');

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXRecordMyTmeModal',
        ];

        //for tasks - we want the buttons and fields enabled
        if(is_numeric(request('task_id'))){
            $jsondata['postrun_functions'][] = [
                'value' => 'NXRecordMyTmeModalExtra',
            ];
        }

        //ajax response
        return response()->json($jsondata);
    }

}
