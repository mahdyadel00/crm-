<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [change category] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class ChangeCategoryUpdateResponse implements Responsable {

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

        //generate a new for for each record
        foreach ($allrows as $projects) {
            //project id
            $id = $projects->first()->project_id;

            //update list view
            if (auth()->user()->pref_view_projects_layout == 'list') {
                $html = view('pages/projects/views/list/table/ajax', compact('projects'))->render();
                $jsondata['dom_html'][] = array(
                    'selector' => "#project_$id",
                    'action' => 'replace-with',
                    'value' => $html);
            }

            //update cards view
            if (auth()->user()->pref_view_projects_layout == 'card') {
                $html = view('pages/projects/views/cards/layout/ajax', compact('projects'))->render();
                $jsondata['dom_html'][] = array(
                    'selector' => "#project_$id",
                    'action' => 'replace-with',
                    'value' => $html);
            }

            //remove the item if its not in same category
            if (request()->filled('filter_category')) {
                if ($projects->first()->project_categoryid != request('filter_category')) {
                    $jsondata['dom_visibility'][] = [
                        'selector' => "#project_$id",
                        'action' => 'hide',
                    ];
                }
            }

            //check the box again (only for bulk actions)
            if (request('type') == 'bulk') {
                $jsondata['dom_property'][] = [
                    'selector' => '#listcheckbox-projects-' . $id,
                    'prop' => 'checked',
                    'value' => true,
                ];
            }
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#actionsModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
