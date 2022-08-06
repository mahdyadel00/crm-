<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for estimates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estimates\EstimateClone;
use App\Http\Requests\Estimates\EstimateSave;
use App\Http\Requests\Estimates\EstimateStoreUpdate;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Estimates\AcceptResponse;
use App\Http\Responses\Estimates\AttachProjectResponse;
use App\Http\Responses\Estimates\ChangeCategoryUpdateResponse;
use App\Http\Responses\Estimates\ChangeStatusResponse;
use App\Http\Responses\Estimates\ConvertToEstimate;
use App\Http\Responses\Estimates\CreateCloneResponse;
use App\Http\Responses\Estimates\CreateResponse;
use App\Http\Responses\Estimates\DeclineResponse;
use App\Http\Responses\Estimates\DestroyResponse;
use App\Http\Responses\Estimates\DocumentEditingResponse;
use App\Http\Responses\Estimates\EditResponse;
use App\Http\Responses\Estimates\IndexResponse;
use App\Http\Responses\Estimates\PDFResponse;
use App\Http\Responses\Estimates\PublishResponse;
use App\Http\Responses\Estimates\PublishRevisedResponse;
use App\Http\Responses\Estimates\ResendResponse;
use App\Http\Responses\Estimates\SaveResponse;
use App\Http\Responses\Estimates\ShowResponse;
use App\Http\Responses\Estimates\StoreCloneResponse;
use App\Http\Responses\Estimates\StoreResponse;
use App\Http\Responses\Estimates\UpdateResponse;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CloneEstimateRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EstimateGeneratorRepository;
use App\Repositories\EstimateRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\LineitemRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Estimates extends Controller {

    /**
     * The estimate repository instance.
     */
    protected $estimaterepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The tax repository instance.
     */
    protected $taxrepo;

    /**
     * The line item repository instance.
     */
    protected $lineitemrepo;

    /**
     * The unit repository instance.
     */
    protected $unitrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    /**
     * The estimate generator repository
     */
    protected $estimategenerator;

    public function __construct(
        EstimateRepository $estimaterepo,
        TagRepository $tagrepo,
        UserRepository $userrepo,
        TaxRepository $taxrepo,
        LineitemRepository $lineitemrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        EstimateGeneratorRepository $estimategenerator
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('estimatesMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
            'attachProjectUpdate',
            'changeStatusUpdate',
        ]);

        $this->middleware('estimatesMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('estimatesMiddlewareEdit')->only([
            'edit',
            'update',
            'emailClient',
            'dettachProject',
            'attachProject',
            'attachProjectUpdate',
            'convertToInvoice',
            'changeStatusUpdate',
            'changeStatus',
            'saveInvoice',
            'changeStatusUpdate',
            'convertToInvoice',
            'convertToInvoiceAction',
            'createClone',
            'storeClone',
        ]);

        $this->middleware('estimatesMiddlewareShow')->only([
            'show',
            'downloadPDF',
            'acceptEstimate',
            'declineEstimate',
        ]);

        $this->middleware('estimatesMiddlewareDestroy')->only(['destroy']);

        //only needed for the [action] methods
        $this->middleware('estimatesMiddlewareBulkEdit')->only(['changeCategoryUpdate']);

        //repos
        $this->estimaterepo = $estimaterepo;
        $this->tagrepo = $tagrepo;
        $this->userrepo = $userrepo;
        $this->lineitemrepo = $lineitemrepo;
        $this->taxrepo = $taxrepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
        $this->estimategenerator = $estimategenerator;

    }

    /**
     * Display a listing of estimates
     * @param object ProjectRepository instance of the repository
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function index(ProjectRepository $projectrepo, CategoryRepository $categoryrepo) {

        $projects = [];

        //get estimate
        $estimates = $this->estimaterepo->search();

        //get all categories (type: estimate) - for filter panel
        $categories = $categoryrepo->get('estimate');

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('estimate');

        //get clients project list
        if (config('visibility.filter_panel_clients_projects')) {
            if (is_numeric(request('estimateresource_id'))) {
                $projects = $projectrepo->search('', ['project_clientid' => request('estimateresource_id')]);
            }
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('estimates'),
            'estimates' => $estimates,
            'projects' => $projects,
            'stats' => $this->statsWidget(),
            'categories' => $categories,
            'tags' => $tags,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new estimate.
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //estimate categories
        $categories = $categoryrepo->get('estimate');

        //get tags
        $tags = $this->tagrepo->getByType('estimate');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
            'tags' => $tags,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created estimate  in storage.
     * @param object EstimateStoreUpdate
     * @return \Illuminate\Http\Response
     */
    public function store(EstimateStoreUpdate $request, ClientRepository $clientrepo) {

        //are we creating a new client
        if (request('client-selection-type') == 'new') {

            //create client
            if (!$client_id = $clientrepo->create([
                'send_email' => 'yes',
                'return' => 'id',
            ])) {
                abort(409);
            }

            //add client id to request
            request()->merge([
                'bill_clientid' => $client_id,
            ]);
        }

        //create the estimate
        if (!$bill_estimateid = $this->estimaterepo->create()) {
            abort(409);
        }

        //add tags
        $this->tagrepo->add('estimate', $bill_estimateid);

        //reponse payload
        $payload = [
            'id' => $bill_estimateid,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified estimate.
     * @param int $id estimate  id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //get invoice object payload
        if (!$payload = $this->estimategenerator->generate($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //append to payload
        $payload['page'] = $this->pageSettings('estimate', $payload['bill']);

        //mark events as read
        \App\Models\EventTracking::where('parent_id', $id)
            ->where('parent_type', 'estimate')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //if client - marked as opened
        if (auth()->user()->is_client) {
            \App\Models\Estimate::where('bill_estimateid', $id)
                ->update(['bill_viewed_by_client' => 'yes']);
        }

        //custom fields
        $payload['customfields'] = \App\Models\CustomField::Where('customfields_type', 'clients')->get();

        //pdf estimate
        if (request()->segment(3) == 'pdf') {
            return new PDFResponse($payload);
        }

        //document editing (proposal/contract)
        if (request('estimate_mode') == 'document') {
            return new DocumentEditingResponse($payload);
        }

        //process reponse
        return new ShowResponse($payload);
    }

    /**
     * save estimate changes, when an ioice is being edited
     * @param object EstimateSave
     * @return \Illuminate\Http\Response
     */
    public function saveEstimate(EstimateSave $request, $id) {

        //get the estimate
        $estimates = $this->estimaterepo->search($id);
        $estimate = $estimates->first();

        //save each line item in the database
        $this->estimaterepo->saveLineItems($id);

        //update taxes
        $this->updateEstimateTax($id);

        //update other estimate attributes
        $this->estimaterepo->updateEstimate($id);

        //reponse payload
        $payload = [
            'estimate' => $estimate,
        ];

        //saving from proposal or contract page
        if (request('estimate_mode') == 'document') {
            return response()->json(array(
                'notification' => [
                    'type' => 'success',
                    'value' => __('lang.request_has_been_completed'),
                ],
                'skip_dom_reset' => true,
            ));
        }

        //response
        return new SaveResponse($payload);

    }

    /**
     * update the tax for an estimate
     * (1) delete existing estimate taxes
     * (2) for summary taxes - save new taxes
     * @param int $bill_estimateid
     * @return \Illuminate\Http\Response
     */
    private function updateEstimateTax($bill_estimateid = '') {

        //delete current estimate taxes
        \App\Models\Tax::Where('taxresource_type', 'estimate')
            ->where('taxresource_id', $bill_estimateid)
            ->delete();

        //save taxes [summary taxes]
        if (is_array(request('bill_logic_taxes'))) {
            foreach (request('bill_logic_taxes') as $tax) {
                //get data elements
                $list = explode('|', $tax);
                $data = [
                    'tax_taxrateid' => $list[2],
                    'tax_name' => $list[1],
                    'tax_rate' => $list[0],
                    'taxresource_type' => 'estimate',
                    'taxresource_id' => $bill_estimateid,
                ];
                $this->taxrepo->create($data);
            }
        }

    }

    /**
     * publish an estimate
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function publishEstimate($id) {

        //generate the invoice
        if (!$payload = $this->estimategenerator->generate($id)) {
            abort(409, __('lang.error_loading_item'));
        }

        //estimate
        $estimate = $payload['bill'];

        //validate current status
        if ($estimate->bill_status != 'draft') {
            abort(409, __('lang.estimate_already_piblished'));
        }

        /** ----------------------------------------------
         * record event [comment]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'estimate',
            'event_item_id' => $estimate->bill_estimateid,
            'event_item_lang' => 'event_created_estimate',
            'event_item_content' => __('lang.estimate') . ' - ' . $estimate->formatted_bill_estimateid,
            'event_item_content2' => '',
            'event_parent_type' => 'estimate',
            'event_parent_id' => $estimate->bill_estimateid,
            'event_parent_title' => $estimate->project_title,
            'event_clientid' => $estimate->bill_clientid,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => (is_numeric($estimate->bill_projectid)) ? 'project' : 'client',
            'eventresource_id' => (is_numeric($estimate->bill_projectid)) ? $estimate->bill_projectid : $estimate->bill_clientid,
            'event_notification_category' => 'notifications_billing_activity',

        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users (main client)
            $users = $this->userrepo->getClientUsers($estimate->bill_clientid, 'owner', 'ids');
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [queued]
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\PublishEstimate($user, [], $estimate);
                    $mail->build();
                }
            }
        }

        //update estimate status
        \App\Models\Estimate::where('bill_estimateid', $estimate->bill_estimateid)
            ->update(['bill_status' => 'new']);

        //response
        return new PublishResponse();
    }

    /**
     * publish a revised estimate
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function publishRevisedEstimate($id) {

        //generate the invoice
        if (!$payload = $this->estimategenerator->generate($id)) {
            abort(409, __('lang.error_loading_item'));
        }

        //estimate
        $estimate = $payload['bill'];

        //validate current status
        if ($estimate->bill_status != 'declined') {
            abort(409, __('lang.action_only_available_on_declined_estimates'));
        }

        //check if estimate is not already expired
        $bill_expiry_date = \Carbon\Carbon::parse($estimate->bill_expiry_date);
        if ($bill_expiry_date->diffInDays(today(), false) > 0) {
            abort(409, __('lang.estimate_has_expired_update_date'));
        }

        /** ----------------------------------------------
         * record event [comment]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'estimate',
            'event_item_id' => $estimate->bill_estimateid,
            'event_item_lang' => 'event_revised_estimate',
            'event_item_content' => __('lang.estimate') . ' - ' . $estimate->formatted_bill_estimateid,
            'event_item_content2' => '',
            'event_parent_type' => 'estimate',
            'event_parent_id' => $estimate->bill_estimateid,
            'event_parent_title' => $estimate->project_title,
            'event_clientid' => $estimate->bill_clientid,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => (is_numeric($estimate->bill_projectid)) ? 'project' : 'client',
            'eventresource_id' => (is_numeric($estimate->bill_projectid)) ? $estimate->bill_projectid : $estimate->bill_clientid,
            'event_notification_category' => 'notifications_billing_activity',

        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users (main client)
            $users = $this->userrepo->getClientUsers($estimate->bill_clientid, 'owner', 'ids');
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [queued]
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\PublishRevisedEstimate($user, [], $estimate);
                    $mail->build();
                }
            }
        }

        //update estimate status
        \App\Models\Estimate::where('bill_estimateid', $estimate->bill_estimateid)
            ->update(['bill_status' => 'revised']);

        //response
        return new PublishRevisedResponse();
    }

    /**
     * resend an estimate via email
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function resendEstimate($id) {

        //generate the estimate
        if (!$payload = $this->estimategenerator->generate($id)) {
            abort(409, __('lang.error_loading_item'));
        }

        //estimate
        $estimate = $payload['bill'];

        //validate current status
        if ($estimate->bill_status == 'draft') {
            abort(409, __('lang.estimate_still_draft'));
        }

        /** ----------------------------------------------
         * send email [queued]
         * ----------------------------------------------*/
        $users = $this->userrepo->getClientUsers($estimate->bill_clientid, 'owner', 'collection');
        foreach ($users as $user) {
            $mail = new \App\Mail\PublishEstimate($user, [], $estimate);
            $mail->build();
        }

        //response
        return new ResendResponse();
    }

    /**
     * customer accepting estimate
     * @param int $id estimate id
     * @return \Illuminate\Http\Response
     */
    public function acceptEstimate($id) {

        //generate the estimate
        if (!$payload = $this->estimategenerator->generate($id)) {
            abort(409, __('lang.error_loading_item'));
        }

        //estimate
        $estimate = $payload['bill'];

        //validate current status
        if (!in_array($estimate->bill_status, ['new', 'revised'])) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //update estimate status
        \App\Models\Estimate::where('bill_estimateid', $estimate->bill_estimateid)
            ->update(['bill_status' => 'accepted']);

        /** ----------------------------------------------
         * record event [comment]
         * see database table to details of each key
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'estimate',
            'event_item_id' => $estimate->bill_estimateid,
            'event_item_lang' => 'event_accepted_estimate',
            'event_item_content' => __('lang.estimate') . ' - ' . $estimate->formatted_bill_estimateid,
            'event_item_content2' => '',
            'event_clientid' => $estimate->bill_clientid,
            'event_parent_type' => 'estimate',
            'event_parent_id' => $estimate->bill_estimateid,
            'event_parent_title' => $estimate->project_title,
            'event_clientid' => $estimate->bill_clientid,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => (is_numeric($estimate->bill_projectid)) ? 'project' : 'client',
            'eventresource_id' => (is_numeric($estimate->bill_projectid)) ? $estimate->bill_projectid : $estimate->bill_clientid,
            'event_notification_category' => 'notifications_billing_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get estimate team users, with billing app notifications enabled
            $users = $this->userrepo->mailingListTeamEstimates('app');
            //record notification
            $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** --------------------------------------------------------
         * send email [queued]
         * - estimate users, with biling email preference enabled
         * --------------------------------------------------------*/
        $users = $this->userrepo->mailingListTeamEstimates('email');
        foreach ($users as $user) {
            $mail = new \App\Mail\AcceptEstimate($user, [], $estimate);
            $mail->build();
        }

        //response
        return new AcceptResponse();
    }

    /**
     * customer declining an estimate
     * @param int $id estimate id
     * @return \Illuminate\Http\Response
     */
    public function declineEstimate($id) {

        //generate the estimate
        if (!$payload = $this->estimategenerator->generate($id)) {
            abort(409, __('lang.error_loading_item'));
        }

        //estimate
        $estimate = $payload['bill'];

        //validate current status
        if (!in_array($estimate->bill_status, ['new', 'revised'])) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //update estimate status
        \App\Models\Estimate::where('bill_estimateid', $estimate->bill_estimateid)
            ->update(['bill_status' => 'declined']);

        /** ----------------------------------------------
         * record event [comment]
         * see database table to details of each key
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'estimate',
            'event_item_id' => $estimate->bill_estimateid,
            'event_item_lang' => 'event_declined_estimate',
            'event_item_content' => __('lang.estimate') . ' - ' . $estimate->formatted_bill_estimateid,
            'event_item_content2' => '',
            'event_clientid' => $estimate->bill_clientid,
            'event_parent_type' => 'estimate',
            'event_parent_id' => $estimate->bill_estimateid,
            'event_parent_title' => $estimate->project_title,
            'event_clientid' => $estimate->bill_clientid,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => (is_numeric($estimate->bill_projectid)) ? 'project' : 'client',
            'eventresource_id' => (is_numeric($estimate->bill_projectid)) ? $estimate->bill_projectid : $estimate->bill_clientid,
            'event_notification_category' => 'notifications_billing_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get estimate team users, with billing app notifications enabled
            $users = $this->userrepo->mailingListTeamEstimates('app');
            //record notification
            $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** --------------------------------------------------------
         * send email [queued]
         * - estimate users, with biling email preference enabled
         * --------------------------------------------------------*/
        $users = $this->userrepo->mailingListTeamEstimates('email');
        foreach ($users as $user) {
            $mail = new \App\Mail\DeclineEstimate($user, [], $estimate);
            $mail->build();
        }

        //response
        return new DeclineResponse();
    }

    /**
     * Show the form for editing the specified estimate.
     * @param object CategoryRepository instance of the repository
     * @param int $id estimate  id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //get the project
        $estimate = $this->estimaterepo->search($id);

        //client categories
        $categories = $categoryrepo->get('estimate');

        //get tags
        $tags_resource = $this->tagrepo->getByResource('estimate', $id);
        $tags_user = $this->tagrepo->getByType('estimate');
        $tags = $tags_resource->merge($tags_user);
        $tags = $tags->unique('tag_title');

        //not found
        if (!$estimate = $estimate->first()) {
            abort(409, __('lang.estimate_not_found'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'estimate' => $estimate,
            'categories' => $categories,
            'tags' => $tags,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified estimate in storage.
     * @param int $id estimate  id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {
        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'bill_date' => 'required|date',
            'bill_expiry_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value != '' && request('bill_date') != '' && (strtotime($value) < strtotime(request('bill_date')))) {
                        return $fail(__('lang.expiry_date_must_be_after_estimate_date'));
                    }
                }],
            'bill_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
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
        if (!$this->estimaterepo->update($id)) {
            abort(409);
        }

        //delete & update tags
        $this->tagrepo->delete('estimate', $id);
        $this->tagrepo->add('estimate', $id);

        //get project
        $estimates = $this->estimaterepo->search($id);

        //reponse payload
        $payload = [
            'estimates' => $estimates,
            'stats' => $this->statsWidget(),
        ];

        //generate a response
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified estimate from storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo) {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked items
            if ($value == 'on') {
                //destroy estimate
                $destroyrepo->destroyEstimate($id);
                //add to array
                $allrows[] = $id;
            }
        }
        //reponse payload
        $payload = [
            'allrows' => $allrows,
            'stats' => $this->statsWidget(),
        ];

        //generate a response
        return new DestroyResponse($payload);
    }

    /**
     * Show the form for changing estimate category
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all estimate categories
        $categories = $categoryrepo->get('estimate');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * update the estimate category
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'estimate')
            ->first()) {
            abort(409, __('lang.item_not_found'));
        }

        //update each estimate
        $allrows = array();
        foreach (request('ids') as $bill_estimateid => $value) {
            if ($value == 'on') {
                $estimate = \App\Models\Estimate::Where('bill_estimateid', $bill_estimateid)->first();
                //update the category
                $estimate->bill_categoryid = request('category');
                $estimate->save();
                //get the estimate in rendering friendly format
                $estimates = $this->estimaterepo->search($bill_estimateid);
                //add to array
                $allrows[] = $estimates;
            }
        }

        //reponse payload
        $payload = [
            'allrows' => $allrows,
        ];

        //show the form
        return new ChangeCategoryUpdateResponse($payload);
    }

    /**
     * Show the form for changing estimate category
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function convertToInvoice($id) {

        //reponse payload
        $payload = [
            'estimate_id' => $id,
        ];

        //show the form
        return new ConvertToEstimate($payload);
    }

    /**
     * convert the estimate into an invoice
     * @return \Illuminate\Http\Response
     */
    public function convertToInvoiceAction(DestroyRepository $destroyrepo, $id) {

        //get the  estimate
        $estimate = \App\Models\Estimate::Where('bill_estimateid', $id)->first();

        //get the line items
        $lines = \App\Models\Lineitem::Where('lineitemresource_type', 'estimate')->where('lineitemresource_id', $id)->get();

        //get the line items
        $taxes = \App\Models\Tax::Where('taxresource_type', 'estimate')->where('taxresource_id', $id)->get();

        //create an invocie
        $invoice = new \App\Models\Invoice();
        $invoice->bill_clientid = $estimate->bill_clientid;
        $invoice->bill_projectid = $estimate->bill_projectid;
        $invoice->bill_creatorid = auth()->id();
        $invoice->bill_date = request('bill_date');
        $invoice->bill_due_date = request('bill_date');
        $invoice->bill_subtotal = $estimate->bill_subtotal;
        $invoice->bill_discount_type = $estimate->bill_discount_type;
        $invoice->bill_discount_percentage = $estimate->bill_discount_percentage;
        $invoice->bill_discount_amount = $estimate->bill_discount_amount;
        $invoice->bill_amount_before_tax = $estimate->bill_amount_before_tax;
        $invoice->bill_tax_type = $estimate->bill_tax_type;
        $invoice->bill_tax_total_percentage = $estimate->bill_tax_total_percentage;
        $invoice->bill_tax_total_amount = $estimate->bill_tax_total_amount;
        $invoice->bill_final_amount = $estimate->bill_final_amount;
        $invoice->bill_adjustment_description = $estimate->bill_adjustment_description;
        $invoice->bill_adjustment_amount = $estimate->bill_adjustment_amount;
        $invoice->bill_notes = '';
        $invoice->bill_terms = config('system.settings_invoices_default_terms_conditions');
        $invoice->bill_status = $estimate->bill_status;
        $invoice->bill_invoice_type = 'onetime';
        $invoice->bill_type = 'invoice';
        $invoice->bill_visibility = 'visible';

        //estimate notes
        if (request('copy_estimate_notes') == 'on') {
            $invoice->bill_notes = $estimate->bill_notes;
        }

        //estimate terms
        if (request('copy_estimate_terms') == 'on') {
            $invoice->bill_terms = $estimate->bill_terms;
        }

        $invoice->save();

        //clone line items
        foreach ($lines as $line) {
            $lineitem = $line->replicate();
            $lineitem->lineitem_created = now();
            $lineitem->lineitem_updated = now();
            $lineitem->lineitemresource_type = 'invoice';
            $lineitem->lineitemresource_id = $invoice->bill_invoiceid;
            $lineitem->save();
        }

        //clone taxes
        foreach ($taxes as $tax) {
            $newtax = $tax->replicate();
            $newtax->tax_created = now();
            $newtax->tax_updated = now();
            $newtax->taxresource_type = 'invoice';
            $newtax->taxresource_id = $invoice->bill_invoiceid;
            $newtax->save();
        }

        //delete original estimate
        if (request('delete_original_estimate') == 'on') {
            $destroyrepo->destroyEstimate($id);
        }

        //redirect to new invoice
        $jsondata = [];
        $jsondata['redirect_url'] = url("/invoices/" . $invoice->bill_invoiceid);
        return response()->json($jsondata);
    }

    /**
     * Show the form for changing an estimate status
     * @return \Illuminate\Http\Response
     */
    public function changeStatus() {

        //get the estimate
        $estimate = \App\Models\Estimate::Where('bill_estimateid', request()->route('estimate'))->first();

        //reponse payload
        $payload = [
            'estimate' => $estimate,
        ];

        //show the form
        return new ChangeStatusResponse($payload);
    }

    /**
     * change estimate status
     * @return \Illuminate\Http\Response
     */
    public function changeStatusUpdate() {

        //validate the estimate exists
        $estimate = \App\Models\Estimate::Where('bill_estimateid', request()->route('estimate'))->first();

        //if revsed - mark as unread
        if (request('bill_status') == 'revised' || request('bill_status') == 'new') {
            \App\Models\Estimate::where('bill_estimateid', request()->route('estimate'))
                ->update(['bill_viewed_by_client' => 'no']);
        }

        //update the estimate
        $estimate->bill_status = request('bill_status');
        $estimate->save();

        //get refreshed estimate
        $estimates = $this->estimaterepo->search(request()->route('estimate'));

        //reponse payload
        $payload = [
            'estimates' => $estimates,
            'bill_estimateid' => request()->route('estimate'),
            'stats' => $this->statsWidget(),
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * Show the form for attaching a project to an estimate
     * @return \Illuminate\Http\Response
     */
    public function attachProject() {

        //get client id
        $client_id = request('client_id');

        //reponse payload
        $payload = [
            'projects_feed_url' => url("/feed/projects?ref=clients_projects&client_id=$client_id"),
        ];

        //show the form
        return new AttachProjectResponse($payload);
    }

    /**
     * attach a project to an estimate
     * @return \Illuminate\Http\Response
     */
    public function attachProjectUpdate() {

        //validate the estimate exists
        $estimate = \App\Models\estimate::Where('bill_estimateid', request()->route('estimate'))->first();

        //validate the project exists
        if (!$project = \App\Models\Project::Where('project_id', request('attach_project_id'))->first()) {
            abort(409, __('lang.item_not_found'));
        }

        //update the estimate
        $estimate->bill_projectid = request('attach_project_id');
        $estimate->bill_clientid = $project->project_clientid;
        $estimate->save();

        //get refreshed estimate
        $estimates = $this->estimaterepo->search(request()->route('estimate'));
        $estimate = $estimates->first();

        //refresh estimate
        $this->estimaterepo->refreshestimate($estimate);

        //reponse payload
        $payload = [
            'estimates' => $estimates,
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * dettach estimate from a project
     * @return \Illuminate\Http\Response
     */
    public function dettachProject() {

        //validate the estimate exists
        $estimate = \App\Models\estimate::Where('bill_estimateid', request()->route('estimate'))->first();

        //update the estimate
        $estimate->bill_projectid = null;
        $estimate->save();

        //get refreshed estimate
        $estimates = $this->estimaterepo->search(request()->route('estimate'));

        //reponse payload
        $payload = [
            'estimates' => $estimates,
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * show the form for cloning an estimate
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function createClone(CategoryRepository $categoryrepo, $id) {

        //get the estimate
        $estimate = \App\Models\estimate::Where('bill_estimateid', $id)->first();

        //get tags
        $tags = $this->tagrepo->getByType('estimate');

        //estimate categories
        $categories = $categoryrepo->get('estimate');

        //reponse payload
        $payload = [
            'estimate' => $estimate,
            'tags' => $tags,
            'categories' => $categories,
        ];

        //show the form
        return new CreateCloneResponse($payload);
    }

    /**
     * show the form for cloning an estimate
     * @param object EstimateClone instance of the request validation
     * @param object CloneEstimateRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function storeClone(EstimateClone $request, CloneEstimateRepository $cloneestimaterepo, $id) {

        //get the estimate
        $estimate = \App\Models\Estimate::Where('bill_estimateid', $id)->first();

        //clone data
        $data = [
            'estimate_id' => $id,
            'client_id' => request('bill_clientid'),
            'project_id' => request('bill_projectid'),
            'estimate_date' => request('bill_date'),
            'return' => 'id',
        ];

        //clone estimate
        if (!$estimate_id = $cloneestimaterepo->clone($data)) {
            abort(409, __('lang.cloning_failed'));
        }

        //reponse payload
        $payload = [
            'id' => $estimate_id,
        ];

        //show the form
        return new StoreCloneResponse($payload);
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
                __('lang.sales'),
                __('lang.estimates'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'estimates',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_estimates' => 'active',
            'mainmenu_sales' => 'active',
            'mainmenu_client_billing' => 'active',
            'submenu_estimates' => 'active',
            'sidepanel_id' => 'sidepanel-filter-estimates',
            'dynamic_search_url' => url('estimates/search?action=search&estimateresource_id=' . request('estimateresource_id') . '&estimateresource_type=' . request('estimateresource_type')),
            'add_button_classes' => 'add-edit-estimate-button',
            'load_more_button_route' => 'estimates',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_estimate'),
            'add_modal_create_url' => url('estimates/create?estimateresource_id=' . request('estimateresource_id') . '&estimateresource_type=' . request('estimateresource_type')),
            'add_modal_action_url' => url('estimates?estimateresource_id=' . request('estimateresource_id') . '&estimateresource_type=' . request('estimateresource_type')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //estimates list page
        if ($section == 'estimates') {
            $page += [
                'meta_title' => __('lang.estimates'),
                'heading' => __('lang.estimates'),
                'sidepanel_id' => 'sidepanel-filter-estimates',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //estimate page
        if ($section == 'estimate') {
            //adjust
            $page['page'] = 'estimate';
            //add
            $page += [
                'crumbs' => [
                    __('lang.estimates'),
                ],
                'meta_title' => __('lang.estimate') . ' #' . $data->formatted_bill_estimateid,
                'heading' => __('lang.project') . ' - ' . $data->project_title,
                'bill_estimateid' => request()->segment(2),
                'source_for_filter_panels' => 'ext',
                'section' => 'overview',
            ];

            $page['crumbs'] = [
                __('lang.sales'),
                __('lang.estimates'),
                $data['formatted_bill_estimateid'],
            ];

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
            $page['mode'] = 'editing';
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

        //stats
        $count_new = $this->estimaterepo->search('', ['stats' => 'count-new']);
        $count_accepted = $this->estimaterepo->search('', ['stats' => 'count-accepted']);
        $count_declined = $this->estimaterepo->search('', ['stats' => 'count-declined']);
        $count_expired = $this->estimaterepo->search('', ['stats' => 'count-expired']);

        $sum_new = $this->estimaterepo->search('', ['stats' => 'sum-new']);
        $sum_accepted = $this->estimaterepo->search('', ['stats' => 'sum-accepted']);
        $sum_declined = $this->estimaterepo->search('', ['stats' => 'sum-declined']);
        $sum_expired = $this->estimaterepo->search('', ['stats' => 'sum-expired']);

        //default values
        $stats = [
            [
                'value' => runtimeMoneyFormat($sum_new),
                'title' => __('lang.pending') . " ($count_new)",
                'percentage' => '100%',
                'color' => 'bg-info',
            ],
            [
                'value' => runtimeMoneyFormat($sum_accepted),
                'title' => __('lang.accepted') . " ($count_accepted)",
                'percentage' => '100%',
                'color' => 'bg-success',
            ],
            [
                'value' => runtimeMoneyFormat($sum_expired),
                'title' => __('lang.expired') . " ($count_expired)",
                'percentage' => '100%',
                'color' => 'bg-warning',
            ],
            [
                'value' => runtimeMoneyFormat($sum_declined),
                'title' => __('lang.declined') . " ($count_declined)",
                'percentage' => '100%',
                'color' => 'bg-danger',
            ],
        ];
        //return
        return $stats;
    }
}