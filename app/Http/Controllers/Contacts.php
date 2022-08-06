<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for contacts
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Contacts\CreateResponse;
use App\Http\Responses\Contacts\DestroyResponse;
use App\Http\Responses\Contacts\EditResponse;
use App\Http\Responses\Contacts\IndexResponse;
use App\Http\Responses\Contacts\StoreResponse;
use App\Http\Responses\Contacts\UpdateResponse;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Validator;

class Contacts extends Controller {

    /**
     * The users repository instance.
     */
    protected $userrepo;

    /**
     * The category repository instance.
     */
    protected $categoryrepo;

    /**
     * The client repository instance.
     */
    protected $clientrepo;

    public function __construct(UserRepository $userrepo, CategoryRepository $categoryrepo, ClientRepository $clientrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('contactsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);

        $this->middleware('contactsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('contactsMiddlewareEdit')->only([
            'edit',
            'update',
        ]);

        $this->middleware('contactsMiddlewareDestroy')->only([
            'destroy',
        ]);

        //dependencies
        $this->userrepo = $userrepo;
        $this->categoryrepo = $categoryrepo;
        $this->clientrepo = $clientrepo;
    }

    /**
     * Display a listing of contacts
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //get contacts
        request()->merge([
            'type' => 'client',
            'status' => 'active',
        ]);
        $contacts = $this->userrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('contacts'),
            'contacts' => $contacts,
        ];

        //show views
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new contact.
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //page settings
        $page = $this->pageSettings('create');

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created contact in storage.
     * @param object ClientRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRepository $clientrepo) {

        //custom error messages
        $messages = [
            'clientid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'clientid' => [
                'required',
                Rule::exists('clients', 'client_id'),
            ],
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

        //set other data (client role = 3)
        request()->merge([
            'role_id' => 2,
            'type' => 'client',
        ]);

        //password
        $password = str_random(9);

        //save contact
        if (!$userid = $this->userrepo->create(bcrypt($password))) {
            abort(409);
        }

        //get the contact
        $contacts = $this->userrepo->search($userid);
        $contact = $contacts->first();

        //update client user specific - default notification settings
        $contact->notifications_new_project = config('settings.default_notifications_client.notifications_new_project');
        $contact->notifications_projects_activity = config('settings.default_notifications_client.notifications_projects_activity');
        $contact->notifications_billing_activity = config('settings.default_notifications_client.notifications_billing_activity');
        $contact->notifications_tasks_activity = config('settings.default_notifications_client.notifications_tasks_activity');
        $contact->notifications_tickets_activity = config('settings.default_notifications_client.notifications_tickets_activity');
        $contact->notifications_system = config('settings.default_notifications_client.notifications_system');
        $contact->force_password_change = config('settings.force_password_change');
        $contact->pref_language = config('system.settings_system_language_default');
        $contact->save();

        /** ----------------------------------------------
         * send email to user
         * ----------------------------------------------*/
        $data = [
            'password' => $password,
        ];
        $mail = new \App\Mail\UserWelcome($contact, $data);
        $mail->build();

        //counting rows
        $rows = $this->userrepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'contacts' => $contacts,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id contact id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //the user
        $user = \App\Models\User::Where('id', $id)->first();

        //reponse payload
        $payload = [
            'page' => $page,
            'user' => $user,
        ];

        //process reponse
        return new EditResponse($payload);

    }

    /**
     * Update the specified contact in storage.
     * @param int $id contact id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //vars
        $original_owner = '';

        //validate the form
        $validator = Validator::make(request()->all(), [
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id, 'id'),
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //get the user
        $user = \App\Models\User::Where('id', $id)->first();

        //update the user
        if (!$this->userrepo->update($id)) {
            abort(409);
        }

        //update accout owner
        if (request('account_owner') == 'on') {
            //get the current account owner
            $owner = \App\Models\User::Where('clientid', $user->clientid)->where('account_owner', 'yes')->first();
            //update owner
            $this->userrepo->updateAccountOwner($user->clientid, $id);
            //get original owner in friendly format
            $original_owner = $this->userrepo->search($owner->id);
        }

        //get the user
        $contacts = $this->userrepo->search($id);
        $contact = $contacts->first();

        //reponse payload
        $payload = [
            'contacts' => $contacts,
            'clientid' => $user->clientid,
            'original_owner' => $original_owner,
            'user' => $contact,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified contact from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy() {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked items
            if ($value == 'on') {
                //get the item
                $user = \App\Models\User::Where('id', $id)->first();
                //make account as deleted
                $user->status = 'deleted';
                //remove avater
                $user->avatar_filename = '';
                //delete email
                $user->email = '';
                //delete password
                $user->password = '';
                //update delete date
                $user->deleted = now();
                //save user
                $user->save();
                //add to array
                $allrows[] = $id;
            }
        }
        //reponse payload
        $payload = [
            'allrows' => $allrows,
        ];

        //generate a response
        return new DestroyResponse($payload);
    }

    /**
     * Update preferences of logged in user
     * @return null silent
     */
    public function updatePreferences() {

        $this->userrepo->updatePreferences(auth()->id());

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
                __('lang.clients'),
                __('lang.users'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'contacts',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_contacts' => 'active',
            'mainmenu_customers' => 'active',
            'submenu_contacts' => 'active',
            'sidepanel_id' => 'sidepanel-filter-contacts',
            'dynamic_search_url' => url('contacts/search?action=search&contactresource_id=' . request('contactresource_id') . '&contactresource_type=' . request('contactresource_type')),
            'add_button_classes' => '',
            'load_more_button_route' => 'contacts',
            'source' => 'list',
        ];

        //client user settings
        if (auth()->user()->is_client) {
            $page['visibility_list_page_actions_filter_button'] = '';
            $page['visibility_list_page_actions_search'] = '';
        }

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_user'),
            'add_modal_create_url' => url('contacts/create?contactresource_id=' . request('contactresource_id') . '&contactresource_type=' . request('contactresource_type')),
            'add_modal_action_url' => url('contacts?contactresource_id=' . request('contactresource_id') . '&contactresource_type=' . request('contactresource_type')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //contracts list page
        if ($section == 'contacts') {
            $page += [
                'meta_title' => __('lang.users'),
                'heading' => __('lang.users'),

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
                'source' => 'list',
            ];
            return $page;
        }

        //return
        return $page;
    }
}