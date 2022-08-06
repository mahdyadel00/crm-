<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Errorlogs\IndexResponse;
use File;
use Illuminate\Http\Request;
use Storage;
use Validator;

class Errorlogs extends Controller {

    /**
     * The settings repository instance.
     */
    protected $settingsrepo;

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $logs = File::allFiles('application/storage/logs');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'logs' => $logs,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * some notes
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download() {

        $file = 'logs/' . request('filename');

        //validation
        if (!request()->filled('filename')) {
            abort(404);
        }

        //check if file exists
        if (!Storage::disk('app-storage')->exists($file)) {
            abort(404);
        }

        //download file
        return Storage::disk('app-storage')->download($file);
    }

    /**
     * delete a log file
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete() {

        $file = 'logs/' . request('filename');

        //validation
        if (!request()->filled('filename')) {
            abort(404);
        }

        //check if file exists
        if (!Storage::disk('app-storage')->exists($file)) {
            abort(404);
        }

        //delete the file
        Storage::disk('app-storage')->delete([$file]);

        //remove file from list
        $jsondata['dom_visibility'][] = [
            'selector' => '#logfile_'. request('key'),
            'action' => 'fadeout',
        ];

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_company_name' => 'required',
            'settings_system_date_format' => 'required',
            'settings_system_datepicker_format' => 'required',
            'settings_system_default_leftmenu' => 'required',
            'settings_system_default_statspanel' => 'required',
            'settings_system_pagination_limits' => 'required',
            'settings_system_currency_symbol' => 'required',
            'settings_system_currency_position' => 'required',
            'settings_system_close_modals_body_click' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update
        if (!$this->settingsrepo->updateGeneral()) {
            abort(409);
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
                __('lang.error_logs'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => ' - ' . __('lang.settings'),
            'heading' => __('lang.settings'),
            'settingsmenu_general' => 'active',
        ];
        return $page;
    }

}
