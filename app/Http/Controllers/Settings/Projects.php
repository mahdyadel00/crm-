<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for projects settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Projects\IndexResponse;
use App\Http\Responses\Settings\Projects\UpdateResponse;
use App\Repositories\ProjectRepository;
use App\Repositories\SettingsRepository;
use App\Rules\CheckBox;
use Illuminate\Http\Request;
use Validator;

class Projects extends Controller {

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
    public function general() {

        //crumbs, page data & stats
        $page = $this->pageSettings('general');

        $settings = \App\Models\Settings::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'section' => 'general',
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function clientPermissions() {

        //crumbs, page data & stats
        $page = $this->pageSettings('client-permissions');

        $settings = \App\Models\Settings::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'section' => 'client-permissions',
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function staffPermissions() {

        //crumbs, page data & stats
        $page = $this->pageSettings('staff-permissions');

        $settings = \App\Models\Settings::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'section' => 'staff-permissions',
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
    public function updateGeneral() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_projects_default_hourly_rate' => [
                'numeric',
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
        if (!$this->settingsrepo->updateProjectGeneral()) {
            abort(409);
        }

        $jsondata['skip_dom_reset'] = true;

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateClientPermissions() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_projects_clientperm_tasks_view' => [
                new CheckBox,
            ],
            'settings_projects_assignedperm_tasks_collaborate' => [
                new CheckBox,
            ],
            'settings_projects_clientperm_tasks_create' => [
                new CheckBox,
            ],
            'settings_projects_clientperm_timesheets_view' => [
                new CheckBox,
            ],
            'settings_projects_clientperm_expenses_view' => [
                new CheckBox,
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
        if (!$this->settingsrepo->updateProjectClientPermissions()) {
            abort(409);
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStaffPermissions(ProjectRepository $projectrepo) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_projects_assignedperm_tasks_collaborate' => [
                new CheckBox,
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

        if (config('system.settings_projects_permissions_basis') == request('settings_projects_permissions_basis')) {
            $changed = false;
        } else {
            $changed = true;
        }

        //update
        if (!$this->settingsrepo->updateProjectStaffPermissions()) {
            abort(409);
        }

        /** -------------------------------------------------------------------------
         * [CATEGORY USERS] 
         * we are changing to category_based permission. All projects will now be
         * reassigned to the users currently in each category.
         * We do not need to do this is we are changing to user_roles because we will
         * just keep the currently assigned users as they are
         * -------------------------------------------------------------------------*/
        if ($changed && request('settings_projects_permissions_basis') == 'category_based') {
            $projectrepo->switchPermissionsProtocol(request('settings_projects_permissions_basis'));
        }

        //reponse payload
        $payload = [];

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        //skip dom
        $jsondata['skip_dom_reset'] = true;

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        //general settings
        if ($section == 'general') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.projects'),
                __('lang.general_settings'),
            ];
        }

        //client permissions
        if ($section == 'client-permissions') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.projects'),
                __('lang.client_permissions'),
            ];
        }

        //team permissions
        if ($section == 'staff-permissions') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.projects'),
                __('lang.team_permissions'),
            ];
        }

        return $page;
    }

}
