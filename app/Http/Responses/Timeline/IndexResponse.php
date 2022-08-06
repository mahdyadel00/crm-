<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the timeline
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Timeline;
use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable {

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

        //has this call been made from an embedded page/ajax or directly on timeline page
        if (request('source') == 'ext' || request()->ajax()) {

            //template and dom - for additional ajax loading
            if (request('action') == 'load') {
                $template = 'pages/timeline/components/misc/ajax';
                $dom_container = '#timeline-container';
                $dom_action = 'append';
            } else {
                //template and dom - for ajax initial loading
                $template = 'pages/timeline/timeline';
                $dom_container = '#embed-content-container';
                $dom_action = 'replace';
            }

            //load more button - change the page number and determine buttons visibility
            if ($events->currentPage() < $events->lastPage()) {
                $url = loadMoreButtonUrl($events->currentPage() + 1, request('source'));
                $jsondata['dom_attributes'][] = array(
                    'selector' => '#load-more-button',
                    'attr' => 'data-url',
                    'value' => $url);
                //load more - visible
                $jsondata['dom_visibility'][] = array('selector' => '.loadmore-button-container', 'action' => 'show');
                //load more: (intial load - sanity)
                $page['visibility_show_load_more'] = true;
                $page['url'] = $url;
            } else {
                $jsondata['dom_visibility'][] = array('selector' => '.loadmore-button-container', 'action' => 'hide');
            }

            //for embedded request -change active tabs menu
            $jsondata['dom_classes'][] = [
                'selector' => '.tabs-menu-item',
                'action' => 'remove',
                'value' => 'active',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#tabs-menu-overview',
                'action' => 'add',
                'value' => 'active',
            ];

            //change actions right panel
            if ($replace_actions_nav == 'client') {
                $html = view('pages/client/components/misc/actions', compact('page', 'client'))->render();
                $jsondata['dom_html'][] = [
                    'selector' => '#list-page-actions-container',
                    'action' => 'replace-with',
                    'value' => $html,
                ];
            }

            //render the view and save to json
            $html = view($template, compact('page', 'events'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => $dom_container,
                'action' => $dom_action,
                'value' => $html);

            return response()->json($jsondata);

        }
    }
}