<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the emailing a client
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Webmail;
use Illuminate\Contracts\Support\Responsable;

class ComposeResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team members
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
        if ($view == 'modal') {
            $html = view('pages/webmail/compose/modal', compact('recipients', 'templates'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#commonModalBody',
                'action' => 'replace',
                'value' => $html);
            //show modal footer
            $jsondata['dom_visibility'][] = array('selector' => '#commonModalFooter', 'action' => 'show');
            //$jsondata['skip_dom_reset'] = true;
        }

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXWebmailComposeEmail',
        ];

        //ajax response
        return response()->json($jsondata);
    }

}
