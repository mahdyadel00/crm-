<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the events
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Events;
use Illuminate\Contracts\Support\Responsable;

class TopNavResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //full payload array
        $payload = $this->payload;

        //load more button - change the page number and determine buttons visibility
        if ($events->currentPage() < $events->lastPage()) {
            //loadmore url
            $next_page = $events->currentPage() + 1;
            $url = loadMoreButtonUrl($events->currentPage() + 1, request('source'));
            $jsondata['dom_attributes'][] = array(
                'selector' => '#events-panel-load-more-button',
                'attr' => 'data-url',
                'value' => $url);
            //load more - visible
            $jsondata['dom_visibility'][] = array('selector' => '#events-panel-loadmore-button-container', 'action' => 'show');
            //load more: (intial load - sanity)
            $page['visibility_show_load_more'] = true;
            $page['url'] = $url;
        } else {
            $jsondata['dom_visibility'][] = array('selector' => '#events-panel-loadmore-button-container', 'action' => 'hide');
        }

        //render the form
        $html = view('pages/events/topnav', compact('page', 'events'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#sidepanel-notifications-events',
            'action' => (request('action') == 'load') ? 'append' : 'replace',
            'value' => $html);

        if ($count > 0) {
            //show footer
            $jsondata['dom_visibility'][] = [
                'selector' => '#my-events-actions-footer',
                'action' => 'show',
            ];
            //show flashing icon on bell
            $jsondata['dom_visibility'][] = [
                'selector' => '#topnav-notification-icon',
                'action' => 'show',
            ];
            //show mark all read
            if (request('eventtracking_status') == 'unread') {
                $jsondata['dom_visibility'][] = [
                    'selector' => '#sidepanel-notifications-mark-all-read',
                    'action' => 'show',
                ];
            }else{
                $jsondata['dom_visibility'][] = [
                    'selector' => '#sidepanel-notifications-mark-all-read',
                    'action' => 'hide',
                ];
            }
        } else {
            //show flashing icon on bell
            $jsondata['dom_visibility'][] = [
                'selector' => '#topnav-notification-icon',
                'action' => 'hide',
            ];
            //show mark all read
            $jsondata['dom_visibility'][] = [
                'selector' => '#sidepanel-notifications-mark-all-read',
                'action' => 'hide',
            ];
        }

        //ajax response
        return response()->json($jsondata);

    }
}