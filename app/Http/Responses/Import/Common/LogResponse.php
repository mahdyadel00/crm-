<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the log
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Import\Common;
use Illuminate\Contracts\Support\Responsable;

class LogResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for logs
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //render the form
        $html = view('pages/import/common/errorlog', compact('log'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#importing-modal-container',
            'action' => 'replace',
            'value' => $html);

        //show modal logter
        $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'hide');

        //ajax response
        return response()->json($jsondata);

    }

}
