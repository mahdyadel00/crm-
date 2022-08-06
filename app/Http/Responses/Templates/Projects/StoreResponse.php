<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Templates\Projects;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

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

        //redirect to project page
        if (request('show_after_adding') == 'on') {

            $jsondata['redirect_url'] = url("templates/projects/$id");

        } else {

            //prepend content on top of list or show full table
            if ($count == 1) {
                $html = view('pages/templates/projects/components/table/table', compact('projects'))->render();
                $jsondata['dom_html'][] = array(
                    'selector' => '#projects-view-wrapper',
                    'action' => 'replace',
                    'value' => $html);
            } else {
                //prepend content on top of list
                if (auth()->user()->pref_view_projects_layout == 'list') {
                    $html = view('pages/projects/views/list/table/ajax', compact('projects'))->render();
                    $jsondata['dom_html'][] = array(
                        'selector' => '#projects-td-container',
                        'action' => 'prepend',
                        'value' => $html);
                }

                //prepend content on top of list
                if (auth()->user()->pref_view_projects_layout == 'card') {
                    $html = view('pages/projects/views/cards/layout/ajax', compact('projects'))->render();
                    $jsondata['dom_html'][] = array(
                        'selector' => '#projects-cards-container',
                        'action' => 'prepend',
                        'value' => $html);
                }
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
