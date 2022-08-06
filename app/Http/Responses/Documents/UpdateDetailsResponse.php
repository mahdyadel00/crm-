<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the proposals
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Documents;
use Illuminate\Contracts\Support\Responsable;

class UpdateDetailsResponse implements Responsable {

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

        $payload = $this->payload;

        //update details section
        $html = view('pages/documents/elements/doc-details', compact('document', 'payload'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#doc-details',
            'action' => 'replace-with',
            'value' => $html,
        ];

        //also update the hero section
        $html = view('pages/documents/elements/hero', compact('document', 'payload'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#hero-header-cover',
            'action' => 'replace-with',
            'value' => $html,
        ];

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        //skip dom update
        $jsondata['skip_dom_reset'] = true;

        //ajax response
        return response()->json($jsondata);
    }

}
