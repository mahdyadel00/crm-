<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the project
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Templates\Projects;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for project members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //editing from main page
        if (request('ref') == 'page') {

            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '.modal', 'action' => 'close-modal');

            //redirect to project page
            $jsondata['redirect_url'] = url("templates/projects/$id");
            
        } else {

            //replace the row of this record
            $html = view('pages/templates/projects/components/table/ajax', compact('projects'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#projects_" . $id,
                'action' => 'replace-with',
                'value' => $html);

            //for own profile, replace user name in top nav
            if ($request->input('id') == auth()->id()) {
                $jsondata['dom_html'][] = array(
                    'selector' => "#topnav_username",
                    'action' => 'replace',
                    'value' => safestr($request->input('first_name')));
            }

            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

            //notice
            $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));
        }

        //response
        return response()->json($jsondata);

    }

}
