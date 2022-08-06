<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for company settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Company\IndexResponse;
use App\Http\Responses\Settings\Company\UpdateResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\NoTags;
use Validator;

class Company extends Controller {

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
     * Display company settings
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
            'settings_company_name' => [
                'required',
                new NoTags,
            ],
            'settings_company_address_line_1' => [
                'nullable',
                new NoTags,
            ],
            'settings_company_city' => [
                'nullable',
                new NoTags,
            ],
            'settings_company_state' => [
                'nullable',
                new NoTags,
            ],
            'settings_company_zipcode' => [
                'nullable',
                new NoTags,
            ],
            'settings_company_country' => [
                'nullable',
                new NoTags,
            ],
            'settings_company_telephone' => [
                'nullable',
                new NoTags,
            ],
            
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
        if (!$this->settingsrepo->updateCompany()) {
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
                __('lang.company'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.company'),
        ];
        return $page;
    }

}
