<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for contracts settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Contracts\IndexResponse;
use App\Http\Responses\Settings\Contracts\UpdateResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use DB;
use Validator;

class Contracts extends Controller {

    /**
     * The settings repository instance.
     */
    protected $settingsrepo;

    public function __construct(SettingsRepository $settingsrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->settingsrepo = $settingsrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $settings = \App\Models\Settings::find(1);

        $query = DB::select("SHOW TABLE STATUS LIKE 'contracts'");
        $next_id = $query[0]->Auto_increment;

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'next_id' => $next_id,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //update
        if (!$this->settingsrepo->updateContractSettings()) {
            abort(409);
        }

        //are we updating next ID
        if (request()->filled('next_id')) {
            if (request('next_id') != request('next_id_current')) {
                DB::select("ALTER TABLE contracts AUTO_INCREMENT = " . request('next_id'));
            }
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
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
                __('lang.sales'),
                __('lang.contracts'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];
        return $page;
    }

}
