<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Templates\Projects;
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

        //render the form
        $html = view('pages/templates/project/components/tabs/details', compact('page', 'project', 'tags'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#embed-content-container',
            'action' => 'replace',
            'value' => $html,
        ];

        
        // POSTRUN FUNCTIONS------
        if (config('visibility.edit_project_button')) {
            $jsondata['postrun_functions'][] = [
                'value' => 'NXProjectDetails',
            ];
        }

        //ajax response
        return response()->json($jsondata);
    }

}
