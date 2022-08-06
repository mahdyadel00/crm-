<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [common] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class UpdateProgressResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * various common responses. Add more as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //success notification
        if ($type == 'show') {
            $html = view('pages/projects/components/actions/change-progress', compact('project'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#actionsModalBody',
                'action' => 'replace',
                'value' => $html);
            $jsondata['dom_visibility'][] = array('selector' => '#actionsModalFooter', 'action' => 'show');
        }

        //error notification
        if ($type == 'update') {

            if (request('ref') == 'page') {
                $jsondata['redirect_url'] = url('projects/' . $project->project_id);
            } else {

                //update list view
                if (auth()->user()->pref_view_projects_layout == 'list') {
                    $html = view('pages/projects/views/list/table/ajax', compact('projects'))->render();
                    $jsondata['dom_html'][] = array(
                        'selector' => "#project_" . $project->project_id,
                        'action' => 'replace-with',
                        'value' => $html);
                }

                //update cards view
                if (auth()->user()->pref_view_projects_layout == 'card') {
                    $html = view('pages/projects/views/cards/layout/ajax', compact('projects'))->render();
                    $jsondata['dom_html'][] = array(
                        'selector' => "#project_" . $project->project_id,
                        'action' => 'replace-with',
                        'value' => $html);
                }
            }

            //close modal
            $jsondata['dom_visibility'][] = array('selector' => '#actionsModal', 'action' => 'close-modal');
        }

        //response
        return response()->json($jsondata);

    }
}
