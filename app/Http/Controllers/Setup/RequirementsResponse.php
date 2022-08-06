<?php

/** --------------------------------------------------------------------------------
 * This controller manages the business logic for the setup wizard
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Setup;
use Illuminate\Contracts\Support\Responsable;

class RequirementsResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for setup
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $html = view('pages/setup/requirements', compact('page', 'requirements', 'error'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#setup-content',
            'action' => 'replace',
            'value' => $html);

        //this step
        if ($error['count'] == 0) {
            $jsondata['dom_classes'][] = array(
                'selector' => '#steps-2',
                'action' => 'add',
                'value' => 'active-passed');
        } else {
            $jsondata['dom_classes'][] = array(
                'selector' => '#steps-2',
                'action' => 'add',
                'value' => 'active-failed');
        }

        //ajax response
        return response()->json($jsondata);

    }

}
