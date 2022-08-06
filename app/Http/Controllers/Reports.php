<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for reports
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class Reports extends Controller {


    public function __construct() {
        
        //parent
        parent::__construct();
                
       //authenticated
        $this->middleware('auth');
    }
    

    /**
     * show main page
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings('reports');
        $stats = $this->statsWidget(array());

        //render page
        return view('reports', compact('page', 'stats'));
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = array();

        $page = [
            'crumbs' => [
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'reports',
        ];
        return $page;
    }

    /**
     * data for the stats widget
     * $section string required
     * $data array optional payload data
     * @return array
     */
    private function statsWidget($data = array()) {

        $stats = array();

        //return
        return $stats;
    }
}