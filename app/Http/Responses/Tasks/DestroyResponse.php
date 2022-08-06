<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [destroy] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class DestroyResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * remove the item from the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //hide and remove all deleted rows
        foreach ($allrows as $id) {
            $jsondata['dom_visibility'][] = array(
                'selector' => '#task_' . $id,
                'action' => 'slideup-slow-remove',
            );
        }

        //hide and remove all deleted cards (if kanbanview)
        foreach ($allrows as $id) {
            $jsondata['dom_visibility'][] = array(
                'selector' => '#card_task_' . $id,
                'action' => 'slideup-slow-remove',
            );
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

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //close task modal(if applicable)
        $jsondata['dom_visibility'][] = array('selector' => '#cardModal', 'action' => 'close-modal');

        //response
        return response()->json($jsondata);

    }

}
