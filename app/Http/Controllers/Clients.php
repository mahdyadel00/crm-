<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for clients
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clients\ClientStoreValidation;
use App\Http\Responses\Clients\CommonResponse;
use App\Http\Responses\Clients\CreateResponse;
use App\Http\Responses\Clients\DestroyResponse;
use App\Http\Responses\Clients\DetailsResponse;
use App\Http\Responses\Clients\EditLogoResponse;
use App\Http\Responses\Clients\EditResponse;
use App\Http\Responses\Clients\IndexResponse;
use App\Http\Responses\Clients\ShowDynamicResponse;
use App\Http\Responses\Clients\ShowResponse;
use App\Http\Responses\Clients\StoreResponse;
use App\Http\Responses\Clients\UpdateDetailsResponse;
use App\Http\Responses\Clients\UpdateResponse;
use App\Repositories\AttachmentRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CustomFieldsRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Validator;

class Clients extends Controller {

    /**
     * The users repository instance.
     */
    protected $userrepo;

    /**
     * The clients repository instance.
     */
    protected $clientrepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The customrepo repository instance.
     */
    protected $customrepo;

    public function __construct(UserRepository $userrepo, ClientRepository $clientrepo, TagRepository $tagrepo, CustomFieldsRepository $customrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('clientsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);

        $this->middleware('clientsMiddlewareEdit')->only([
            'edit',
            'update',
            'updateDescription',
        ]);

        $this->middleware('clientsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('clientsMiddlewareDestroy')->only(['destroy']);

        $this->middleware('clientsMiddlewareShow')->only([
            'show',
            'details',
            'updateDescription',
            'emailCompose',
            'emailSend',
        ]);

        //dependencies
        $this->userrepo = $userrepo;
        $this->clientrepo = $clientrepo;
        $this->tagrepo = $tagrepo;
        $this->customrepo = $customrepo;

    }

    /**
     * Display a listing of clients
     * @param object CategoryRepository category repository
     * @return blade view | ajax view
     */
    public function index(CategoryRepository $categoryrepo) {

        //basic page settings
        $page = $this->pageSettings('clients');

        //get clients
        $clients = $this->clientrepo->search();

        //client categories
        $categories = $categoryrepo->get('client');

        //get tags
        $tags = $this->tagrepo->getByType('client');

        //reponse payload
        $payload = [
            'page' => $page,
            'stats' => $this->statsWidget(),
            'clients' => $clients,
            'categories' => $categories,
            'tags' => $tags,
        ];

        //show views
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new client
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {
        //page settings
        $page = $this->pageSettings('create');

        //get tags
        $tags = $this->tagrepo->getByType('client');

        //get custom fields

        //reponse payload
        $payload = [
            'page' => $page,
            'tags' => $tags,
            'categories' => $categoryrepo->get('client'),
            'fields' => $this->getCustomFields(),
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created client in storage.
     * @return \Illuminate\Http\Response
     */
    public function store(ClientStoreValidation $request) {

        //custom field validation
        if ($messages = $this->customFieldValidationFailed()) {
            abort(409, $messages);
        }

        //save the client first [API]
        if (!$client_id = $this->clientrepo->create([
            'send_email' => 'yes',
            'return' => 'id',
        ])) {
            abort(409);
        }

        //get the client object (friendly for rendering in blade template)
        $clients = $this->clientrepo->search($client_id);

        //counting rows
        $rows = $this->clientrepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'clients' => $clients,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified client
     * @param int $id client id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //get the client
        $clients = $this->clientrepo->search($id);

        //client
        $client = $clients->first();

        //owner - primary contact
        $owner = \App\Models\User::Where('clientid', $id)
            ->Where('account_owner', 'yes')
            ->first();

        //page settings
        $page = $this->pageSettings('client', $client);

        //get tags
        $tags_resource = $this->tagrepo->getByResource('client', $id);
        $tags_user = $this->tagrepo->getByType('client');
        $tags = $tags_resource->merge($tags_user);
        $tags = $tags->unique('tag_title');

        //set dynamic url for use in template
        switch (request()->segment(3)) {
        case 'files':
        case 'invoices':
        case 'expenses':
        case 'payments':
        case 'timesheets':
        case 'notes':
        case 'tickets':
        case 'contacts':
        case 'projects':
            $sections = request()->segment(3);
            $section = rtrim($sections, 's');
            $page['dynamic_url'] = url($sections . '?source=ext&' . $section . 'resource_type=client&' . $section . 'resource_id=' . $client->client_id);
            break;
        default:
            $page['dynamic_url'] = url("timeline/client?request_source=client&source=ext&timelineclient_id=$id&page=1");
            break;
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'client' => $client,
            'tags' => $tags,
            'owner' => $owner,
            'fields' => $this->getCustomFields(),
        ];

        //response
        return new ShowResponse($payload);
    }

    /**
     * Display the specified client.
     * @param int $id id of the client
     * @return \Illuminate\Http\Response
     */
    public function showDynamic($id) {

        //get the client
        $clients = $this->clientrepo->search($id);

        //client
        $client = $clients->first();

        //owner - primary contact
        $owner = \App\Models\User::Where('clientid', $id)
            ->Where('account_owner', 'yes')
            ->first();

        //page settings
        $page = $this->pageSettings('client', $client);

        //set dynamic url for use in template
        switch (request()->segment(3)) {
        case 'invoices':
        case 'expenses':
        case 'estimates':
        case 'payments':
        case 'timesheets':
        case 'notes':
        case 'tickets':
        case 'contacts':
        case 'projects':
            $sections = request()->segment(3);
            $section = rtrim($sections, 's');
            $page['dynamic_url'] = url($sections . '?source=ext&' . $section . 'resource_type=client&' . $section . 'resource_id=' . $client->client_id);
            break;
        case 'project-files':
            $sections = request()->segment(3);
            $page['dynamic_url'] = url($sections . '?source=ext&' . $section . 'fileresource_type=project&' . $section . 'filter_file_clientid=' . $client->client_id);
            break;
        case 'client-files':
            $sections = request()->segment(3);
            $page['dynamic_url'] = url($sections . '?source=ext&' . $section . 'fileresource_type=client&' . $section . 'fileresource_id=' . $client->client_id);
            break;
        case 'details':
            $page['dynamic_url'] = url('clients/' . $client->client_id . '/client-details');
            break;
        default:
            $page['dynamic_url'] = url("timeline/client?request_source=client&source=ext&timelineclient_id=$id&page=1");
            break;
        }

        //get tags
        $tags_resource = $this->tagrepo->getByResource('client', $id);
        $tags_user = $this->tagrepo->getByType('client');
        $tags = $tags_resource->merge($tags_user);
        $tags = $tags->unique('tag_title');

        //reponse payload
        $payload = [
            'page' => $page,
            'client' => $client,
            'owner' => $owner,
            'tags' => $tags,
        ];

        //response
        return new ShowDynamicResponse($payload);
    }

    /**
     * Show the form for editing the specified client.
     * @param int $id client id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //page settings
        $page = $this->pageSettings('edit');

        //get the client
        if (!$client = $this->clientrepo->search($id)) {
            abort(409);
        }

        //get client tags and users tags
        $tags_resource = $this->tagrepo->getByResource('client', $id);
        $tags_user = $this->tagrepo->getByType('client');
        $tags = $tags_resource->merge($tags_user);
        $tags = $tags->unique('tag_title');

        //clients
        $client = $client->first();

        //reponse payload
        $payload = [
            'page' => $page,
            'client' => $client,
            'categories' => $categoryrepo->get('client'),
            'tags' => $tags,
            'fields' => $this->getCustomFields($client),
        ];

        //response
        return new EditResponse($payload);

    }

    /**
     * Update the specified client in storage.
     * @param int $id client id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //validate the form
        $validator = Validator::make(request()->all(), [
            'client_company_name' => 'required',
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

        //custom field validation
        if ($messages = $this->customFieldValidationFailed()) {
            abort(409, $messages);
        }

        //update client
        if (!$this->clientrepo->update($id)) {
            abort(409);
        }

        //delete & update tags
        if (auth()->user()->is_team) {
            $this->tagrepo->delete('client', $id);
            $this->tagrepo->add('client', $id);
        }

        //if we are suspending the client - logout all users
        if (request('client_status') == 'suspended') {
            if ($users = \App\Models\User::Where('clientid', $id)->get()) {
                //each user - logout
                foreach ($users as $user) {
                    \App\Models\Session::Where('user_id', $user->id)->delete();
                }
            }
        }

        //client
        $clients = $this->clientrepo->search($id);

        //reponse payload
        $payload = [
            'clients' => $clients,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified client from storage.
     * @param object DestroyRepository instance of the repository
     * @param int $id client id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo, $id) {

        //delete client
        $destroyrepo->destroyClient($id);

        //reponse payload
        $payload = [
            'client_id' => $id,
        ];

        //generate a response
        return new DestroyResponse($payload);

    }

    /**
     * get all custom fields for clients
     *   - if they are being used in the 'edit' modal form, also get the current data
     *     from the cliet record. Store this temporarily in '$field->customfields_name'
     *     this will then be used to prefill data in the custom fields
     * @param model client model - only when showing the edit modal form
     * @return collection
     */
    public function getCustomFields($obj = '') {

        //set typs
        request()->merge([
            'customfields_type' => 'clients',
            'filter_show_standard_form_status' => 'enabled',
            'filter_field_status' => 'enabled',
            'sort_by' => 'customfields_position',
        ]);

        //show all fields
        config(['settings.custom_fields_display_limit' => 1000]);

        //get fields
        $fields = $this->customrepo->search();

        //when in editing view - get current value that is stored for this custom field
        if ($obj instanceof \App\Models\Client) {
            foreach ($fields as $field) {
                $field->current_value = $obj[$field->customfields_name];
            }
        }

        return $fields;
    }

    /**
     * Returns false when all is ok
     * @return \Illuminate\Http\Response
     */
    public function customFieldValidationFailed() {

        //custom field validation
        $fields = \App\Models\CustomField::Where('customfields_type', 'clients')->get();
        $errors = '';
        foreach ($fields as $field) {
            if ($field->customfields_status == 'enabled' && $field->customfields_standard_form_status == 'enabled' && $field->customfields_required == 'yes') {
                if (request($field->customfields_name) == '') {
                    $errors .= '<li>' . $field->customfields_title . ' - ' . __('lang.is_required') . '</li>';
                }
            }
        }
        //return
        if ($errors != '') {
            return $errors;
        } else {
            return false;
        }
    }

    /**
     * Return ajax details for project
     * @return \Illuminate\Http\Response
     */
    public function profile() {

        //get client details
        $client = [
            'client_id' => random_int(1, 999),
            'description' => 'hello world',
        ];

        //set the view
        $html = view('pages/client/components/tabs/profile', compact('client'))->render();

        //[action options] replace|append|prepend
        $ajax['dom_html'][] = [
            'selector' => '#embed-content-container',
            'action' => 'replace',
            'value' => $html,
        ];

        //ajax response & view
        return response()->json($ajax);
    }

    /**
     * Show the form for editing the specified clients logo
     * @return \Illuminate\Http\Response
     */
    public function logo() {

        //is this client or admin
        if (auth()->user()->type == 'client') {
            $client_id = auth()->user()->clientid;
        } else {
            $client_id = request('client_id');
        }

        //reponse payload
        $payload = [
            'client_id' => $client_id,
        ];

        //response
        return new EditLogoResponse($payload);
    }

    /**
     * Update the specified client logo in storage.
     * @param int $id client id
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(AttachmentRepository $attachmentrepo) {

        //validate input
        $data = [
            'directory' => request('logo_directory'),
            'filename' => request('logo_filename'),
        ];

        //process and save to db
        if (!$attachmentrepo->processClientLogo($data)) {
            abort(409);
        }

        //sanity check
        if (auth()->user()->type == 'client') {
            $clientid = auth()->user()->clientid;
        } else {
            $clientid = request('client_id');
        }

        //update avatar
        if (!$this->clientrepo->updateLogo(request('client_id'))) {
            abort(409);
        }

        //reponse payload
        $payload = [
            'type' => 'upload-logo',
            'client_id' => $clientid,
        ];

        //generate a response
        return new CommonResponse($payload);
    }

    /**
     * Return ajax details for client
     * @param int $id client id
     * @return \Illuminate\Http\Response
     */
    public function details($id) {

        //get the client
        $clients = $this->clientrepo->search($id);

        //get tags
        $tags = $this->tagrepo->getByResource('client', $id);

        //not found
        if (!$client = $clients->first()) {
            abort(409, __('lang.client_not_found'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('client', $client),
            'client' => $client,
            'tags' => $tags,
        ];

        //response
        return new DetailsResponse($payload);
    }

    /**
     * update client description and also the tags
     * @return \Illuminate\Http\Response
     */
    public function updateDescription() {

        //get the client
        $client = \App\Models\client::Where('client_id', request()->route('client'))->first();

        //update description
        $client->client_description = request('description');

        //save
        $client->save();

        //delete & update tags
        $this->tagrepo->delete('client', $client->client_id);
        $this->tagrepo->add('client', $client->client_id);

        //get tags
        $tags = $this->tagrepo->getByResource('client', $client->client_id);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('client', $client),
            'client' => $client,
            'tags' => $tags,
        ];

        //response
        return new UpdateDetailsResponse($payload);

    }


    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //

        //common settings
        $page = [
            'crumbs' => [
                __('lang.clients'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'clients',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_customers' => 'active',
            'mainmenu_clients' => 'active',
            'submenu_customers' => 'active',
            'tabmenu_timeline' => 'active',
            'sidepanel_id' => 'sidepanel-filter-clients',
            'dynamic_search_url' => url('clients/search?action=search&clientresource_id=' . request('clientresource_id') . '&clientresource_type=' . request('clientresource_type')),
            'add_button_classes' => '',
            'load_more_button_route' => 'clients',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_client'),
            'add_modal_create_url' => url('clients/create?clientresource_id=' . request('clientresource_id') . '&clientresource_type=' . request('clientresource_type')),
            'add_modal_action_url' => url('clients?clientresource_id=' . request('clientresource_id') . '&clientresource_type=' . request('clientresource_type')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //projects list page
        if ($section == 'clients') {
            $page += [
                'meta_title' => __('lang.clients'),
                'heading' => __('lang.clients'),
                'mainmenu_customers' => 'active',

            ];
            return $page;
        }

        //client page
        if ($section == 'client') {
            //adjust
            $page['page'] = 'client';
            //add
            $page += [
                'crumbs' => [
                    __('lang.clients'),
                ],
                'meta_title' => __('lang.client') . ' - ' . $data->client_company_name,
                'heading' => __('lang.client') . ' - ' . $data->client_company_name,
                'project_id' => request()->segment(2),
                'source_for_filter_panels' => 'ext',
            ];

            //ajax loading and tabs
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

        //return
        return $page;
    }

    /**
     * data for the stats widget
     * @return array
     */
    private function statsWidget($data = array()) {

        //default values
        $stats = [
            [
                'value' => '0',
                'title' => __('lang.projects'),
                'percentage' => '0%',
                'color' => 'bg-success',
            ],
            [
                'value' => '$0.00',
                'title' => __('lang.invoices'),
                'percentage' => '0%',
                'color' => 'bg-info',
            ],
            [
                'value' => '0',
                'title' => __('lang.users'),
                'percentage' => '0%',
                'color' => 'bg-primary',
            ],
            [
                'value' => '0',
                'title' => __('lang.active'),
                'percentage' => '0%',
                'color' => 'bg-inverse',
            ],
        ];
        //calculations - set real values
        if (!empty($data)) {
            $stats[0]['value'] = '1';
            $stats[0]['percentage'] = '10%';
            $stats[1]['value'] = '2';
            $stats[1]['percentage'] = '20%';
            $stats[2]['value'] = '3';
            $stats[2]['percentage'] = '30%';
            $stats[3]['value'] = '4';
            $stats[3]['percentage'] = '40%';
        }
        //return
        return $stats;
    }
}