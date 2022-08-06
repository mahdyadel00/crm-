<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for system settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Common\CommonResponse;
use App\Repositories\SettingsRepository;

class System extends Controller {

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
     * Clear system cache
     *
     * @return \Illuminate\Http\Response
     */
    public function clearLaravelCache() {

        $settings = \App\Models\Settings::find(1);

        //clear cache
        \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');

        //reponse payload
        $payload = [
            'type' => 'success-notification',
        ];

        //show the view
        return new CommonResponse($payload);
    }

}
