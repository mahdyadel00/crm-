<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the temp settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Customfields;
use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for bars
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $payload = $this->payload;

        //load more button - change the page number and determine buttons visibility
        if ($fields->currentPage() < $fields->lastPage()) {
            $url = loadMoreButtonUrl($fields->currentPage() + 1, request('source'));
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

        //load ajax
        if (request('action') == 'load') {
            $template = 'pages/settings/sections/customfields/ajax';
            $html = view($template, compact('page', 'fields', 'payload'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#customfields-td-container",
                'action' => 'append',
                'value' => $html);
        } else {
            $template = 'pages/settings/sections/customfields/table';
            $html = view($template, compact('page', 'fields', 'payload'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#settings-wrapper",
                'action' => 'replace',
                'value' => $html);
        }

        //ajax response
        return response()->json($jsondata);
    }
}
