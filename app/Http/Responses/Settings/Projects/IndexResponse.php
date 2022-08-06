<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the projects settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Projects;
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
            $template = 'pages/settings/sections/projects/general';
            break;

        case 'client-permissions':
            $template = 'pages/settings/sections/projects/client-permissions';
            break;

        case 'staff-permissions':
            $template = 'pages/settings/sections/projects/staff-permissions';
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
                'selector' => '#settings-menu-projects',
                'attr' => 'aria-expanded',
                'value' => false,
            ];
            $jsondata['dom_action'][] = [
                'selector' => '#settings-menu-projects',
                'action' => 'trigger',
                'value' => 'click',
            ];

            if ($section == 'client-permissions') {
                $jsondata['dom_classes'][] = [
                    'selector' => '#settings-menu-client-permissions',
                    'action' => 'add',
                    'value' => 'active',
                ];

                // POSTRUN FUNCTIONS------
                $jsondata['postrun_functions'][] = [
                    'value' => 'NXSettingsProjectsClients',
                ];

            }

            if ($section == 'staff-permissions') {
                $jsondata['dom_classes'][] = [
                    'selector' => '#settings-menu-projects-staff-permissions',
                    'action' => 'add',
                    'value' => 'active',
                ];
            }
        }


        
        if ($section == 'general') {
            $jsondata['postrun_functions'][] = [
                'value' => 'NXSettingsProjectsGeneral',
            ];
        }


        //ajax response
        return response()->json($jsondata);
    }
}
