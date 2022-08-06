<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for logos settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Logos\EditResponse;
use App\Http\Responses\Settings\Logos\IndexResponse;
use App\Http\Responses\Settings\Logos\UpdateResponse;
use App\Repositories\AttachmentRepository;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;

class Logos extends Controller {

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

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function logo() {

        //reponse payload
        $payload = [];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(AttachmentRepository $attachmentrepo) {

        //validate input
        $data = [
            'directory' => request('logo_directory'),
            'filename' => request('logo_filename'),
            'logo_size' => request('logo_size'),
        ];

        //process and save to db
        if (!$attachmentrepo->processAppLogo($data)) {
            abort(409);
        }

        //update settings
        $settings = \App\Models\Settings::find(1);
        if (request('logo_size') == 'large') {
            $settings->settings_system_logo_large_name = request('logo_filename');
        } else {
            $settings->settings_system_logo_small_name = request('logo_filename');
        }

        //add new version to avoid caching
        $settings->settings_system_logo_versioning = time();
        $settings->save();

        //payload
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
                __('lang.general_settings'),
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
