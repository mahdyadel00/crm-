<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update status] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class UpdatePositionResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for tasks
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

        //reload stats widget
        $html = view('misc/list-pages-stats', compact('stats'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#list-pages-stats-widget',
            'action' => 'replace-with',
            'value' => $html);
            
        //stats visibility of reload
        if (auth()->user()->stats_panel_position == 'open') {
            $jsondata['dom_visibility'][] = [
                'selector' => '#list-pages-stats-widget',
                'action' => 'show-flex',
            ];
        }

        //response
        return response()->json($jsondata);

    }

}
