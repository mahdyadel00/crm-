<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
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

        //prepend content on top of list or show full table
        if (auth()->user()->pref_view_leads_layout == 'list') {
            if ($count == 1) {
                $html = view('pages/leads/components/table/table', compact('leads'))->render();
                $jsondata['dom_html'][] = array(
                    'selector' => '#leads-view-wrapper',
                    'action' => 'replace',
                    'value' => $html);
            } else {
                //prepend use on top of list
                $html = view('pages/leads/components/table/ajax', compact('leads'))->render();
                $jsondata['dom_html'][] = array(
                    'selector' => '#leads-td-container',
                    'action' => 'prepend',
                    'value' => $html);
            }
        }

        if (auth()->user()->pref_view_leads_layout == 'kanban') {
            //prepend use on top of list
            $html = view('pages/leads/components/kanban/card', compact('board'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#kanban-board-wrapper-' . request('lead_status'),
                'action' => 'prepend',
                'value' => $html);
        }

        //refresh stats
        if (isset($stats)) {
            $html = view('misc/list-pages-stats-content', compact('stats'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#list-pages-stats-widget',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        //show task after adding
        if (request('ref') == 'quickadd' && request('show_after_adding') == 'on') {
            $jsondata['redirect_url'] = url("/leads/v/" . $lead->lead_id . "/" . str_slug($lead->lead_title));
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
