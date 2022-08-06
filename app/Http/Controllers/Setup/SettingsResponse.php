<?php

/** --------------------------------------------------------------------------------
 * This controller manages the business logic for the setup wizard
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Setup;
use Illuminate\Contracts\Support\Responsable;

class SettingsResponse implements Responsable {

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

        $html = view('pages/setup/settings', compact('page', 'error'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#setup-content',
            'action' => 'replace',
            'value' => $html);

        //already passed
        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-2',
            'action' => 'add',
            'value' => 'active-passed');

        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-3',
            'action' => 'add',
            'value' => 'active-passed');

        //this step - running
        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-4',
            'action' => 'add',
            'value' => 'active-running');

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXSetupSettings',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
