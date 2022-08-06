<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for dynamic settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Dynamic\ShowDynamicResponse;
use Illuminate\Http\Request;

class Dynamic extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');
    }

    /**
     * load the settings section that corresponds to the provided url
     * [exmaple]
     *  http://domain.com/app/settings/email/smpt
     *  will load
     *  http://domain.com/settings/email/smpt
     * @return \Illuminate\Http\Response
     */
    public function showDynamic() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        //get url sections
        $section = str_replace('app/', '', request()->path());

        $dynamic_url = str_replace('app/', '', url()->full());
        $page['dynamic'] = 'yes';

        //add param to let us know this is a dynamic url call
        if (strpos($dynamic_url, '?') !== false) {
            $page['dynamic_url'] = $dynamic_url . '&url_type=dynamic';
        } else {
            $page['dynamic_url'] = $dynamic_url . '?url_type=dynamic';
        }

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        //response
        return new ShowDynamicResponse($payload);
    }
    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'settingsmenu_home' => 'active',
        ];

        //enabled form builder js
        config([
            //'visibility.web_form_builder' => true,
        ]);

        return $page;
    }
}