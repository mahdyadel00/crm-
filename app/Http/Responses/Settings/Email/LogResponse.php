<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the email settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Email;
use Illuminate\Contracts\Support\Responsable;

class LogResponse implements Responsable {

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

        //template and dom - for additional ajax loading
        switch (request('action')) {

        //typically from the loadmore button
        case 'load':
            $template = 'pages/settings/sections/email/log/ajax';
            $dom_container = '#emails-td-container';
            $dom_action = 'append';
            break;

        //from the sorting links
        case 'sort':
            $template = 'pages/settings/sections/email/log/ajax';
            $dom_container = '#emails-td-container';
            $dom_action = 'replace';
            break;

        //from search box or filter panel
        case 'search':
            $template = 'pages/settings/sections/email/log/table';
            $dom_container = '#emails-table-wrapper';
            $dom_action = 'replace-with';
            break;

        //template and dom - for ajax initial loading
        default:
            $template = 'pages/settings/sections/email/log/table';
            $dom_container = '#embed-content-container';
            $dom_action = 'replace';
            break;
        }

        //load more button - change the page number and determine buttons visibility
        if ($emails->currentPage() < $emails->lastPage()) {
            $url = loadMoreButtonUrl($emails->currentPage() + 1, request('source'));
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

        //page
        $html = view($template, compact('emails', 'page'))->render();
        $jsondata['dom_html'][] = [
            'selector' => $dom_container,
            'action' => $dom_action,
            'value' => $html,
        ];

        //for embedded - change breadcrumb title
        $jsondata['dom_html'][] = [
            'selector' => '.active-bread-crumb',
            'action' => 'replace',
            'value' => strtoupper(__('lang.email_log')),
        ];

        //left menu activate
        if (request('url_type') == 'dynamic') {
            $jsondata['dom_attributes'][] = [
                'selector' => '#settings-menu-email',
                'attr' => 'aria-expanded',
                'value' => false,
            ];
            $jsondata['dom_action'][] = [
                'selector' => '#settings-menu-email',
                'action' => 'trigger',
                'value' => 'click',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#settings-menu-email-log',
                'action' => 'add',
                'value' => 'active',
            ];
        }

        //ajax response
        return response()->json($jsondata);
    }
}
