<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Http\Responses\Import\Common\StoreResponse;
use App\Http\Responses\Import\Leads\CreateResponse;
use App\Imports\LeadsImport;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\ImportExportRepository;
use App\Repositories\LeadAssignedRepository;
use App\Repositories\SystemRepository;
use Illuminate\Validation\Rule;
use Validator;

class Leads extends Controller {

    /**
     * The assign leads repository instance.
     */
    protected $assignedrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    public function __construct(
        LeadAssignedRepository $assignedrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->assignedrepo = $assignedrepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;

        //Permissions on methods
        $this->middleware('importLeadsMiddlewareCreate')->only([
            'create',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create(SystemRepository $systemrepo) {

        //all available lead statuses
        $statuses = \App\Models\LeadStatus::all();

        //check server requirements for excel module
        $server_check = $systemrepo->serverRequirementsExcel();

        //reponse payload
        $payload = [
            'type' => 'leads',
            'statuses' => $statuses,
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

        //step 1 form validation errors
        $validator = Validator::make(request()->all(), [
            'lead_status' => [
                'required',
                Rule::exists('leads_status', 'leadstatus_id'),
            ],
        ]);

        //step 1 form validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //unique import ID (transaction tracking)
        request()->merge([
            'import_ref' => $import_ref,
        ]);

        //import leads
        $import = new LeadsImport();
        $import->import($file_path);

        //log erors
        $importexportrepo->logImportError($import->failures(), $import_ref);

        //additional processing
        if ($import->getRowCount() > 0) {
            $this->processCollection();
        }

        //reponse payload
        $payload = [
            'type' => 'leads',
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

        //get the leads for this import
        $leads = \App\Models\Lead::Where('lead_importid', request('import_ref'))->get();

        //additional processing
        foreach ($leads as $lead) {

            /** ----------------------------------------------
             * record assignment events and send emails
             * ----------------------------------------------*/
            $assigned_users = $this->assignedrepo->add($lead->lead_id);

            //notify each user (apart from the user doing the imports)
            foreach ($assigned_users as $assigned_user_id) {
                if ($assigned_user_id != auth()->id()) {
                    if ($assigned_user = \App\Models\User::Where('id', $assigned_user_id)->first()) {

                        //event
                        $data = [
                            'event_creatorid' => auth()->id(),
                            'event_item' => 'assigned',
                            'event_item_id' => '',
                            'event_item_lang' => 'event_assigned_user_to_a_lead',
                            'event_item_lang_alt' => 'event_assigned_user_to_a_lead_alt',
                            'event_item_content' => __('lang.assigned'),
                            'event_item_content2' => $assigned_user_id,
                            'event_item_content3' => $assigned_user->first_name,
                            'event_parent_type' => 'lead',
                            'event_parent_id' => $lead->lead_id,
                            'event_parent_title' => $lead->lead_title,
                            'event_show_item' => 'yes',
                            'event_show_in_timeline' => 'no',
                            'event_clientid' => '',
                            'eventresource_type' => 'lead',
                            'eventresource_id' => $lead->lead_id,
                            'event_notification_category' => 'notifications_new_assignement',
                        ];
                        //record event
                        if ($event_id = $this->eventrepo->create($data)) {
                            //record notification (skip the user creating this event)
                            if ($assigned_user_id != auth()->id()) {
                                $emailusers = $this->trackingrepo->recordEvent($data, [$assigned_user_id], $event_id);
                            }
                        }

                        /** ----------------------------------------------
                         * send email [assignment]
                         * ----------------------------------------------*/
                        if ($assigned_user_id != auth()->id()) {
                            if ($assigned_user->notifications_new_assignement == 'yes_email') {
                                $mail = new \App\Mail\LeadAssignment($assigned_user, $data, $lead);
                                $mail->build();
                            }
                        }
                    }
                }
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