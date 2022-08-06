<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for update settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Updates\CheckResponse;
use App\Http\Responses\Settings\Updates\IndexResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class Updates extends Controller {

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

        //[MT]
        if (config('system.settings_type') != 'standalone') {
            abort(404);
        }

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
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function checkUpdates() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        try {
            $response = Http::asForm()->post(config('app.updates_server'), [
                'licence_key' => request('licence_key'),
                'ip_address' => request('ip_address'),
                'url' => request('url'),
                'current_version' => request('current_version'),
                'email' => request('email'),
                'name' => request('name'),
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $message = substr($e->getMessage(), 0, 150);
            //log
            Log::error("unable to connect to updates server", ['process' => '[updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'server_url' => config('app.updates_server'), 'error' => $message]);
            return new CheckResponse([
                'type' => 'server-error',
            ]);
        }

        //check if we got an error
        if ($response->failed() || $response->clientError() || $response->serverError() || !$response->successful()) {
            //log
            Log::error("unable to connect to updates server", ['process' => '[updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'server_url' => config('app.updates_server'), 'error' => $response]);
            return new CheckResponse([
                'type' => 'server-error',
            ]);
        }

        if (!request()->filled('licence_key')) {
            //log
            Log::error("purchase license key not provided", ['process' => '[updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return new CheckResponse([
                'type' => 'license-error',
            ]);
        }

        //get the result
        $result = $response->json();

        //we have received an error from the update server
        if ($result['status'] == 'failed') {
            return new CheckResponse([
                'type' => 'generic-error',
                'dom' => $result['dom'],
            ]);
        }

        //we have received an error from the update server
        if ($result['status'] == 'success') {
            return new CheckResponse([
                'type' => 'success',
                'update_version' => $result['update_version'],
                'url' => $result['url'],
            ]);
        }
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
                __('lang.updates'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];
        return $page;
    }

}
