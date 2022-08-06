<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the email settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Email;
use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable {

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

        switch ($section) {

        case 'general':
            $template = 'pages/settings/sections/email/general';
            break;

        case 'smtp':
            $template = 'pages/settings/sections/email/smtp';
            break;
        }

        $html = view($template, compact('page', 'settings'))->render();

        $jsondata['dom_html'][] = array(
            'selector' => "#settings-wrapper",
            'action' => 'replace',
            'value' => $html);

        //left menu activate
        if (request('url_type') == 'dynamic') {
            $jsondata['dom_attributes'][] = [
                'selector' => '#settings-menu-email',
                'attr' => 'aria-expanded',
                'value' => false,
            ];
            $jsondata['dom_action'][] = [
                'selector' => '#settings-menu-email',
                'action' => 'trigger',
                'value' => 'click',
            ];

            //general menu
            if ($section == 'general') {
                $jsondata['dom_classes'][] = [
                    'selector' => '#settings-menu-email-settings',
                    'action' => 'add',
                    'value' => 'active',
                ];
            }

            //smtp menu
            if ($section == 'smtp') {
                $jsondata['dom_classes'][] = [
                    'selector' => '#settings-menu-email-smtp',
                    'action' => 'add',
                    'value' => 'active',
                ];
            }
        }

        //JAVASCRIPT
        if ($section == 'smtp') {
            // POSTRUN FUNCTIONS------
            $jsondata['postrun_functions'][] = [
                'value' => 'NXSettingsEmailSMTP',
            ];
        }

        //general menu
        if ($section == 'general') {
            // POSTRUN FUNCTIONS------
            $jsondata['postrun_functions'][] = [
                'value' => 'NXSettingsEmailGeneral',
            ];

        }

        //ajax response
        return response()->json($jsondata);
    }
}
