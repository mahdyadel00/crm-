<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the comments
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Comments;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for comments
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

        //prepend content on top of list
        $html = view('pages/comments/components/ajax', compact('comments'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#comments-container',
            'action' => 'prepend',
            'value' => $html);

        //reset text box
        if (request()->filled('editor')) {
            $jsondata['tinymce_reset'][] = [
                'selector' => request('editor'),
            ];
        }
        $jsondata['dom_visibility'][] = [
            'selector' => '#' . request('editor-container'),
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#' . request('placeholder-container'),
            'action' => 'show',
        ];

        //hide notification
        $jsondata['dom_visibility'][] = [
            'selector' => '.page-notification',
            'action' => 'hide-remove',
        ];

        // POSTRUN FUNCTIONS------
        /*
        $jsondata['postrun_functions'][] = [
            'value' => 'NXPostGeneralComment',
        ];
        */

        //response
        return response()->json($jsondata);
    }
}