<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class PrefillProjectResponse implements Responsable {

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


        //update the data in the text editor
        $jsondata['tinymce_new_data'][] = [
            'selector' => 'project_description', //do not put #
            'value' => $template->project_description ?? '',
        ];

        //custom fiels
        $html = view('misc.customfields', compact('fields'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#project-custom-fields-container',
            'action' => 'replace',
            'value' => $html,
        ];

        //response
        return response()->json($jsondata);
    }

}
