<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Http\Responses\Import\Clients\CreateResponse;
use App\Http\Responses\Import\Common\StoreResponse;
use App\Imports\ClientsImport;
use App\Repositories\ImportExportRepository;
use App\Repositories\SystemRepository;
use Illuminate\Validation\Rule;
use Validator;

class Clients extends Controller {

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //Permissions on methods
        $this->middleware('importClientsMiddlewareCreate')->only([
            'create',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create(SystemRepository $systemrepo) {

        //check server requirements for excel module
        $server_check = $systemrepo->serverRequirementsExcel();

        //reponse payload
        $payload = [
            'type' => 'clients',
            'requirements' => $server_check['requirements'],
            'server_status' => $server_check['status'],
            'page' => $this->pageSettings('create'),
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function store(ImportExportRepository $importexportrepo) {

        //unique transaction ref
        $import_ref = str_unique();

        //error count default
        $error_count = 0;

        //imported
        $imported = false;

        //uploaded file path
        $file_path = BASE_DIR . "/storage/temp/" . request('importing-file-uniqueid') . "/" . request('importing-file-name');

        //initial validation
        if (!$importexportrepo->validateImport()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //unique import ID (transaction tracking)
        request()->merge([
            'import_ref' => $import_ref,
        ]);

        //import clients
        $import = new ClientsImport();
        $import->import($file_path);

        //log erors
        $importexportrepo->logImportError($import->failures(), $import_ref);

        //additional processing
        if ($import->getRowCount() > 0) {
            $this->processCollection();
        }

        //reponse payload
        $payload = [
            'type' => 'clients',
            'error_count' => count($import->failures()),
            'error_ref' => $import_ref,
            'count_passed' => $import->getRowCount(),
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * additional processing for the imported collection
     */
    private function processCollection() {

        //get the clients for this import
        $clients = \App\Models\Client::Where('client_importid', request('import_ref'))->get();

        //additional processing
        foreach ($clients as $client) {

            //create password
            $password = str_random(7);

            //create clieng user
            $user = new \App\Models\User();
            $user->clientid = $client->client_id;
            $user->first_name = $client->client_import_first_name;
            $user->last_name = $client->client_import_last_name;
            $user->email = $client->client_import_email;
            $user->position = $client->client_import_job_title;
            $user->password = bcrypt($password);
            $user->creatorid = auth()->id();
            $user->type = 'client';
            $user->account_owner = 'yes';
            //notifications
            $user->notifications_new_project = config('settings.default_notifications_client.notifications_new_project');
            $user->notifications_projects_activity = config('settings.default_notifications_client.notifications_projects_activity');
            $user->notifications_billing_activity = config('settings.default_notifications_client.notifications_billing_activity');
            $user->notifications_tasks_activity = config('settings.default_notifications_client.notifications_tasks_activity');
            $user->notifications_tickets_activity = config('settings.default_notifications_client.notifications_tickets_activity');
            $user->notifications_system = config('settings.default_notifications_client.notifications_system');
            $user->force_password_change = config('settings.force_password_change');
            //save the user
            $user->save();

            //send email (optional)
            if (request('send_email') == 'on') {
                $data = [
                    'password' => $password,
                ];
                $mail = new \App\Mail\UserWelcome($user, $data);
                $mail->build();
            }

        }
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

        ];

        //return
        return $page;
    }
}