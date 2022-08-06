<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for team
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Common\CommonResponse;
use App\Http\Responses\Team\CreateResponse;
use App\Http\Responses\Team\EditResponse;
use App\Http\Responses\Team\IndexResponse;
use App\Http\Responses\Team\StoreResponse;
use App\Http\Responses\Team\UpdateResponse;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Validator;

class Team extends Controller {

    /**
     * The roles repository instance.
     */
    protected $roles;

    /**
     * The users repository instance.
     */
    protected $userrepo;

    public function __construct(RoleRepository $roles, UserRepository $userrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('teamMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);

        $this->middleware('teamMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('teamMiddlewareEdit')->only([
            'edit',
            'update',
            'destroy',
        ]);

        //dependencies
        $this->roles = $roles;
        $this->userrepo = $userrepo;
    }

    /**
     * Display a listing of team
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //get team members
        request()->merge([
            'type' => 'team',
            'status' => 'active',
        ]);
        $users = $this->userrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('team'),
            'users' => $users,
        ];

        //show views
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new team member
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //get all team level roles
        $roles = $this->roles->allTeamRoles();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'roles' => $roles,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created team member in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,role_id',
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

        //if this is creating an admin user - check permissions
        if (!runtimeTeamCreateAdminPermissions(request('role_id'))) {
            abort(403);
        }

        //set other data
        request()->merge(['type' => 'team']);

        //save
        $password = str_random(9);
        if (!$userid = $this->userrepo->create(bcrypt($password))) {
            abort(409);
        }

        //get the user
        $users = $this->userrepo->search($userid);
        $user = $users->first();

        //update team user specific - default notification settings (defaults are set in config/settings.php)
        $user->notifications_projects_activity = 'yes_email';
        $user->notifications_billing_activity = 'yes_email';
        $user->notifications_new_assignement = 'yes_email';
        $user->notifications_leads_activity = 'yes_email';
        $user->notifications_tasks_activity = 'yes_email';
        $user->notifications_tickets_activity = 'yes_email';
        $user->notifications_system = 'yes_email';
        $user->force_password_change = config('settings.force_password_change');
        $user->pref_language = config('system.settings_system_language_default');
        $user->save();

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        //send to users
        $data = [
            'password' => $password,
        ];
        $mail = new \App\Mail\UserWelcome($user, $data);
        $mail->build();

        //reponse payload
        $payload = [
            'users' => $users,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified team member
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get all team level roles
        $roles = $this->roles->allTeamRoles();

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionEdit($user)) {
            abort(403);
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'roles' => $roles,
            'user' => $user,
        ];

        //process reponse
        return new EditResponse($payload);

    }

    /**
     * Update profile
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionEdit($user)) {
            abort(403);
        }

        //custom error messages
        $messages = [
            'role_id.exists' => __('lang.user_role_not_found'),
        ];

        //validate the form
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore($id, 'id'),
            ],
            'role_id' => 'nullable|exists:roles,role_id',
            'password' => 'nullable|confirmed|min:5',
        ], $messages);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update the user
        if (!$this->userrepo->update($id)) {
            abort(409);
        }

        //get user
        $users = $this->userrepo->search($id);

        //reponse payload
        $payload = [
            'users' => $users,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Update preferences of logged in user
     * @return null silent
     */
    public function updatePreferences() {

        $this->userrepo->updatePreferences(auth()->id());

    }

    /**
     * Remove the specified team member from storage.
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionDelete($user)) {
            abort(403);
        }

        //delete project assignments
        \App\Models\ProjectAssigned::Where('projectsassigned_userid', $id)->delete();

        //delete task assignments
        \App\Models\TaskAssigned::Where('tasksassigned_userid', $id)->delete();

        //delete lead assignments
        \App\Models\LeadAssigned::Where('leadsassigned_userid', $id)->delete();

        //delete project manager
        \App\Models\ProjectManager::Where('projectsmanager_userid', $id)->delete();

        //make account as deleted
        $user->status = 'deleted';

        //delete email
        $user->email = '';

        //delete password
        $user->password = '';

        //remove avater
        $user->avatar_filename = '';

        //update delete date
        $user->deleted = now();

        //save user
        $user->save();

        //reponse payload
        $payload = [
            'type' => 'remove-basic',
            'element' => "#team_$id",
        ];

        //generate a response
        return new CommonResponse($payload);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.team_members'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'team',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_settings' => 'active',
            'mainmenu_team' => 'active',
            'submenu_team' => 'active',
            'sidepanel_id' => 'sidepanel-filter-team',
            'dynamic_search_url' => 'team/search?source=' . request('source') . '&action=search',
            'add_button_classes' => '',
            'load_more_button_route' => 'team',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_user'),
            'add_modal_create_url' => url('team/create'),
            'add_modal_action_url' => url('team'),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //contracts list page
        if ($section == 'team') {
            $page += [
                'meta_title' => __('lang.team_members'),
                'heading' => __('lang.team_members'),
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
                'create_type' => 'team',
            ];
            return $page;
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
            return $page;
        }

        //ext page settings
        if ($section == 'ext') {

            $page += [
                'list_page_actions_size' => 'col-lg-12',

            ];

            return $page;
        }

        //return
        return $page;
    }
}