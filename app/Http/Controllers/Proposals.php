<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for proposal proposals
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Proposals\StoreUpdate;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Documents\ShowEditResponse;
use App\Http\Responses\Documents\ShowPreviewResponse;
use App\Http\Responses\Proposals\ChangeCategoryUpdateResponse;
use App\Http\Responses\Proposals\ChangeStatusResponse;
use App\Http\Responses\Proposals\CreateResponse;
use App\Http\Responses\Proposals\DestroyResponse;
use App\Http\Responses\Proposals\EmailResponse;
use App\Http\Responses\Proposals\IndexResponse;
use App\Http\Responses\Proposals\PublishResponse;
use App\Http\Responses\Proposals\StoreResponse;
use App\Models\Category;
use App\Models\Proposal;
use App\Repositories\CategoryRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EstimateGeneratorRepository;
use App\Repositories\EstimateRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\ProposalRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Intervention\Image\Exception\NotReadableException;
use Validator;

class Proposals extends Controller {

    /**
     * The repository instances.
     */
    protected $proposalrepo;
    protected $userrepo;
    protected $estimaterepo;
    protected $eventrepo;
    protected $trackingrepo;
    protected $emailerrepo;

    public function __construct(
        ProposalRepository $proposalrepo,
        UserRepository $userrepo,
        EstimateRepository $estimaterepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth')->except(
            'showPublic',
            'sign',
            'accepted',
            'declined'
        );

        $this->middleware('proposalsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
            'changeStatus',
        ]);

        $this->middleware('proposalsMiddlewareEdit')->only([
            'editingProposal',
            'update',
            'resendEmail',
            'publish',
            'changeStatus',
        ]);

        $this->middleware('proposalsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('proposalsMiddlewareShow')->only([
            'show',
        ]);

        $this->middleware('proposalsMiddlewareDestroy')->only([
            'destroy',
        ]);

        //only needed for the [action] methods
        $this->middleware('proposalsMiddlewareBulkEdit')->only([
            'changeCategoryUpdate',
        ]);

        $this->middleware('proposalsMiddlewareShowPublic')->only([
            'showPublic',
            'sign',
        ]);

        //repos
        $this->proposalrepo = $proposalrepo;
        $this->userrepo = $userrepo;
        $this->estimaterepo = $estimaterepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
    }

    /**
     * Display a listing of proposals
     * @param object CategoryRepository instance of the repository
     * @param object Category instance of the repository
     * @return blade view | ajax view
     */
    public function index(CategoryRepository $categoryrepo, Category $categorymodel) {

        //get proposals
        $proposals = $this->proposalrepo->search();

        //get all categories (type: proposal) - for filter panel
        $categories = $categoryrepo->get('proposal');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('proposals'),
            'proposals' => $proposals,
            'count' => $proposals->count(),
            'stats' => $this->statsWidget(),
            'categories' => $categories,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new proposal
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //client categories
        $categories = $categoryrepo->get('proposal');

        //modal options
        config(['modal.action' => 'create']);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created proposalin storage.
     * @param object StoreUpdate instance of the repository
     * @param object UnitRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdate $request) {

        //get client
        if (request('customer-selection-type') == 'client') {
            if (!$client = \App\Models\Client::Where('client_id', request('doc_client_id'))->first()) {
                abort(409, __('lang.client_not_found'));
            }
            //set the fall back details
            $user = $this->userrepo->getClientAccountOwner(request('doc_client_id'));
            $doc_fallback_client_first_name = $user->first_name;
            $doc_fallback_client_last_name = $user->last_name;
            $doc_fallback_client_email = $user->email;
        }

        //get lead
        if (request('customer-selection-type') == 'lead') {
            if (!$lead = \App\Models\Lead::Where('lead_id', request('doc_lead_id'))->first()) {
                abort(409, __('lang.lead_not_found'));
            }
            //set the fall back details
            $doc_fallback_client_first_name = $lead->lead_firstname;
            $doc_fallback_client_last_name = $lead->lead_lastname;
            $doc_fallback_client_email = $lead->lead_email;
        }

        //create the proposal
        $proposal = new \App\Models\Proposal();
        $proposal->doc_unique_id = str_unique();
        $proposal->doc_creatorid = auth()->id();
        $proposal->doc_type = 'proposal';
        $proposal->doc_categoryid = request('doc_categoryid');
        $proposal->doc_client_id = request('doc_client_id');
        $proposal->doc_lead_id = request('doc_lead_id');
        $proposal->docresource_type = request('customer-selection-type');
        $proposal->docresource_id = (request('customer-selection-type') == 'client') ? request('doc_client_id') : request('doc_lead_id');
        $proposal->doc_heading = __('lang.proposal');
        $proposal->doc_heading_color = '#FFFFFF';
        $proposal->doc_title_color = '#FFFFFF';
        $proposal->doc_title = request('doc_title');
        $proposal->doc_date_start = request('doc_date_start');
        $proposal->doc_date_end = request('doc_date_end');
        $proposal->doc_fallback_client_first_name = $doc_fallback_client_first_name;
        $proposal->doc_fallback_client_last_name = $doc_fallback_client_last_name;
        $proposal->doc_fallback_client_email = $doc_fallback_client_email;
        $proposal->save();

        //options
        if (is_numeric(request('proposal_template'))) {
            if ($template = \App\Models\ProposalTemplate::Where('proposal_template_id', request('proposal_template'))->first()) {
                $proposal->doc_heading_color = $template->proposal_template_heading_color;
                $proposal->doc_title_color = $template->proposal_template_title_color;
                $proposal->doc_body = $template->proposal_template_body;
                $proposal->save();
            }
        }

        //create an estimate record
        $estimate_id = $this->estimaterepo->createProposalEstimate($proposal->doc_id);

        //get the proposal object (friendly for rendering in blade template)
        $proposals = $this->proposalrepo->search($proposal->doc_id);

        //counting rows
        $rows = $this->proposalrepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'proposals' => $proposals,
            'id' => $proposal->doc_id,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the resource
     * @return blade view | ajax view
     */
    public function show(EstimateGeneratorRepository $estimategenerator, $id) {

        //defaults
        $has_estimate = false;

        $payload = [];

        //get the project
        $documents = $this->proposalrepo->search($id);
        $document = $documents->first();

        //get the estimate
        if ($estimate = \App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->first()) {
            request()->merge([
                'generate_estimate_mode' => 'document',
            ]);
            if ($payload = $estimategenerator->generate($estimate->bill_estimateid)) {
                $has_estimate = true;
            }
        }

        //mark events as read
        \App\Models\EventTracking::where('parent_id', $id)
            ->where('parent_type', 'proposal')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //custom fields
        $customfields = \App\Models\CustomField::Where('customfields_type', 'clients')->get();

        //set page
        $page = $this->pageSettings('proposal', $document);

        //payload
        $payload += [
            'document' => $document,
            'page' => $page,
            'customfields' => $customfields,
            'estimate' => $estimate,
            'has_estimate' => $has_estimate,
        ];

        //show the view
        return new ShowPreviewResponse($payload);
    }

    /**
     * Show the resource on a public url
     * @return blade view | ajax view
     */
    public function showPublic(EstimateGeneratorRepository $estimategenerator, $id) {

        //for sanity - do not apply any filters
        request()->merge([
            'do_not_apply_filters' => true,
        ]);

        $payload = [];

        //get the proposal
        $doc = \App\Models\Proposal::Where('doc_unique_id', $id)->first();

        //set numeric id
        $id = $doc->doc_id;

        //for logged in clients - redirect to view inside dashboard
        if (auth()->check() && auth()->user()->is_client) {
            if ($doc->doc_client_id == auth()->user()->clientid) {
                return redirect("/proposals/$id");
            }
        }

        //for logged in admin - redirect to view inside dashboard
        if (auth()->check() && auth()->user()->is_team) {
            //if we are not previewing from admin dashboard
            if (request('action') != 'preview') {
                return redirect("/proposals/$id");
            }
        }

        //defaults
        $has_estimate = false;

        //get the project
        $documents = $this->proposalrepo->search($id);
        $document = $documents->first();

        //get the estimate
        if ($estimate = \App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->first()) {
            request()->merge([
                'generate_estimate_mode' => 'document',
            ]);
            if ($payload = $estimategenerator->generate($estimate->bill_estimateid)) {
                $has_estimate = true;
            }
        }

        //mark events as read
        if (auth()->check()) {
            \App\Models\EventTracking::where('parent_id', $id)
                ->where('parent_type', 'proposal')
                ->where('eventtracking_userid', auth()->id())
                ->update(['eventtracking_status' => 'read']);
        }

        //custom fields
        $customfields = \App\Models\CustomField::Where('customfields_type', 'clients')->get();

        //set page
        $page = $this->pageSettings('proposal', $document);

        //iformation needed for merging variables
        if ($document->docresource_type == 'client') {

        }

        //public url template settings
        config(['visibility.viewing' => 'public']);

        //payload
        $payload += [
            'document' => $document,
            'page' => $page,
            'customfields' => $customfields,
            'estimate' => $estimate,
            'has_estimate' => $has_estimate,
        ];

        //show the view
        return new ShowPreviewResponse($payload);
    }

    /**
     * edit the cover
     * @return blade view | ajax view
     */
    public function editingProposal(CategoryRepository $categoryrepo, $id) {

        //get the project
        $documents = $this->proposalrepo->search($id);
        $document = $documents->first();

        //make sure we have an estimate record
        $estimate_id = $this->estimaterepo->createProposalEstimate($id);

        //get the estimate (or create if does not exist)
        if (!$estimate = \App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->first()) {
            //create an estimate record
            $this->estimaterepo->createProposalEstimate($document->doc_id);
            $estimate = \App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->first();
        }

        //client categories
        $categories = $categoryrepo->get('proposal');

        //custom fields
        $customfields = \App\Models\CustomField::Where('customfields_type', 'clients')->get();

        //set page
        $page = $this->pageSettings('proposal', $document);

        //payload
        $payload = [
            'document' => $document,
            'page' => $page,
            'categories' => $categories,
            'customfields' => $customfields,
            'estimate' => $estimate,
            'mode' => 'editing',
        ];

        //show the view
        return new ShowEditResponse($payload);
    }

    /**
     * Remove the specified proposal from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy() {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked proposals
            if ($value == 'on') {
                //get the proposal
                $proposal = \App\Models\Proposal::Where('doc_id', $id)->first();
                //delete client
                $proposal->delete();
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
     * Bulk change category for proposals
     * @url baseusr/proposals/bulkdelete
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete() {

        //validation - post
        if (!is_array(request('proposal'))) {
            abort(409);
        }

        //loop through and delete each one
        $deleted = 0;
        foreach (request('proposal') as $proposal_id => $value) {
            if ($value == 'on') {
                //get the proposal
                if ($proposals = $this->proposalrepo->search($proposal_id)) {
                    //remove the proposal
                    $proposals->first()->delete();
                    //hide and remove row
                    $jsondata['dom_visibility'][] = array(
                        'selector' => '#proposal_' . $proposal_id,
                        'action' => 'slideup-remove',
                    );
                }
                $deleted++;
            }
        }

        //something went wrong
        if ($deleted == 0) {
            abort(409);
        }

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => 'Request has been completed');

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * Show the form for updating the proposal
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all proposal categories
        $categories = $categoryrepo->get('proposal');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * Show the form for updating the proposal
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'proposal')
            ->first()) {
            abort(409, __('lang.category_not_found'));
        }

        //update each proposal
        $allrows = array();
        foreach (request('ids') as $proposal_id => $value) {
            if ($value == 'on') {
                $proposal = \App\Models\Proposal::Where('doc_id', $proposal_id)->first();
                //update the category
                $proposal->doc_categoryid = request('category');
                $proposal->save();
                //get the proposal in rendering friendly format
                $proposals = $this->proposalrepo->search($proposal_id);
                //add to array
                $allrows[] = $proposals;
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
     * publish the resource
     * @return blade view | ajax view
     */
    public function publish($id) {

        //get the project
        $documents = $this->proposalrepo->search($id);
        $document = $documents->first();

        //get the estimate
        if ($estimate = \App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->first()) {
            $value = $estimate->bill_final_amount;
        } else {
            $value = 0;
        }

        //mark as published
        $document->doc_status = 'new';
        $document->doc_date_published = now();
        $document->doc_date_last_emailed = now();
        $document->save();

        /** ----------------------------------------------
         * record event [comment]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'proposal',
            'event_item_id' => $document->doc_id,
            'event_item_lang' => 'event_created_proposal',
            'event_item_content' => __('lang.proposal') . ' - ' . runtimeProposalIdFormat($document->doc_id),
            'event_item_content2' => '',
            'event_parent_type' => 'proposal',
            'event_parent_id' => $document->doc_id,
            'event_parent_title' => $document->doc_title,
            'event_clientid' => $document->doc_client_id,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => (is_numeric($document->doc_project_id)) ? 'project' : 'client',
            'eventresource_id' => (is_numeric($document->doc_project_id)) ? $document->doc_project_id : $document->doc_client_id,
            'event_notification_category' => 'notifications_billing_activity',
        ];
        $event_id = $this->eventrepo->create($data);

        /** ----------------------------------------------
         * send email - client users - [queued]
         * ----------------------------------------------*/
        if ($document->docresource_type == 'client') {
            if ($event_id = $this->eventrepo->create($data)) {
                //get users (main client)
                $users = $this->userrepo->getClientUsers($document->doc_client_id, 'owner', 'ids');
                //record notification
                $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
            }
            if (isset($emailusers) && is_array($emailusers)) {
                $data = [
                    'user_type' => 'client',
                    'proposal_value' => $value,
                ];
                //send to users
                if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                    foreach ($users as $user) {
                        $mail = new \App\Mail\ProposalPublish($user, $data, $document);
                        $mail->build();
                    }
                }
            }
        }

        /** ----------------------------------------------
         * send email - lead users - [queued]
         * ----------------------------------------------*/
        if ($document->docresource_type == 'lead') {
            if ($lead = \App\Models\Lead::Where('lead_id', $document->doc_lead_id)->first()) {
                $data = [
                    'user_type' => 'lead',
                    'proposal_value' => $value,
                ];
                $mail = new \App\Mail\ProposalPublish($lead, $data, $document);
                $mail->build();
            }
        }

        //payload
        $payload = [

        ];

        //return the reposnse
        return new PublishResponse($payload);
    }

    /**
     * email the resource
     * @return blade view | ajax view
     */
    public function resendEmail($id) {

        //get the project
        $documents = $this->proposalrepo->search($id);
        $document = $documents->first();

        //get the estimate
        if ($estimate = \App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->first()) {
            $value = $estimate->bill_final_amount;
        } else {
            $value = 0;
        }

        //mark as published (fro draft status)
        if ($document->doc_status == 'draft') {
            $document->doc_status = 'new';
            $document->doc_date_published = now();
        }
        $document->doc_date_last_emailed = now();
        $document->save();

        /** ----------------------------------------------
         * send email - client users - [queued]
         * ----------------------------------------------*/
        if ($document->docresource_type == 'client') {
            $data = [
                'user_type' => 'client',
                'proposal_value' => $value,
            ];
            if ($users = $this->userrepo->getClientUsers($document->doc_client_id, 'owner', 'collection')) {
                foreach ($users as $user) {
                    if ($document->doc_status == 'revised') {
                        $mail = new \App\Mail\ProposalRevised($user, $data, $document);
                    } else {
                        $mail = new \App\Mail\ProposalPublish($user, $data, $document);
                    }
                    $mail->build();
                }
            }
        }

        /** ----------------------------------------------
         * send email - lead users - [queued]
         * ----------------------------------------------*/
        if ($document->docresource_type == 'lead') {
            if ($lead = \App\Models\Lead::Where('lead_id', $document->doc_lead_id)->first()) {
                $data = [
                    'user_type' => 'lead',
                    'proposal_value' => $value,
                ];
                if ($document->doc_status == 'revised') {
                    $mail = new \App\Mail\ProposalRevised($lead, $data, $document);
                } else {
                    $mail = new \App\Mail\ProposalPublish($lead, $data, $document);
                }
                $mail->build();
            }
        }

        //payload
        $payload = [

        ];

        //return the reposnse
        return new EmailResponse($payload);
    }

    /**
     * change the resource status
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id) {

        //valid statuses
        $valid_statuses = [
            'accepted',
            'declined',
            'revised',
            'draft',
            'new',
        ];

        //validate
        if (!in_array(request('status'), $valid_statuses)) {
            abort(409, __('lang.invalid_status'));
        }

        //get proposal
        $proposal = \App\Models\Proposal::Where('doc_id', $id)->first();

        //update
        $proposal->doc_status = request('status');
        $proposal->doc_signed_date = null;
        $proposal->doc_signed_first_name = null;
        $proposal->doc_signed_last_name = null;
        $proposal->doc_signed_signature_directory = null;
        $proposal->doc_signed_signature_filename = null;
        $proposal->doc_signed_ip_address = null;
        $proposal->save();

        //get the refreshed proposal
        $proposals = $this->proposalrepo->search($id);
        $proposal = $proposals->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'id' => $id,
            'proposals' => $proposals,
            'proposal' => $proposal,
            'stats' => $this->statsWidget(),
        ];

        //return the reposnse
        return new ChangeStatusResponse($payload);

    }

    /**
     * show the form to sign a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function sign($id) {

        //for sanity - do not apply any filters
        request()->merge([
            'do_not_apply_filters' => true,
        ]);

        //get the proposal
        $proposal = \App\Models\Proposal::Where('doc_unique_id', $id)->first();

        //public or private
        if (auth()->check() && auth()->user()->is_client && (auth()->user()->clientid == $proposal->doc_client_id)) {
            config(['visibility.signing_authenticated' => true]);
        } else {
            config(['visibility.signing_public' => true]);
        }

        //page
        $html = view('pages/documents/components/proposal/sign', compact('proposal'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXSignDocument',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * accept the proposal
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accepted($id) {

        //for sanity - do not apply any filters
        request()->merge([
            'do_not_apply_filters' => true,
        ]);

        //get the proposal
        $proposal = \App\Models\Proposal::Where('doc_unique_id', $id)->first();

        //authenticated user
        if (auth()->check()) {
            if (auth()->user()->is_client) {
                request()->merge([
                    'doc_signed_first_name' => auth()->user()->first_name,
                    'doc_signed_last_name' => auth()->user()->last_name,
                ]);
            } else {
                abort(409, __('lang.incorrect_user_for_action'));
            }
        }

        //custom error messages
        $messages = [
            'doc_signed_first_name.required' => __('lang.first_name') . ' - ' . __('lang.is_required'),
            'doc_signed_last_name.required' => __('lang.last_name') . ' - ' . __('lang.is_required'),
            'signature_code.required' => __('lang.signature') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'doc_signed_first_name' => [
                'required',
            ],
            'doc_signed_last_name' => [
                'required',
            ],
            'signature_code' => [
                'required',
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

        //generate the signature image
        $signature = $this->saveSignature();

        //update proposal
        $proposal->doc_signed_date = now();
        $proposal->doc_signed_first_name = request('doc_signed_first_name');
        $proposal->doc_signed_last_name = request('doc_signed_last_name');
        $proposal->doc_signed_signature_directory = $signature['directory'];
        $proposal->doc_signed_signature_filename = $signature['file_name'];
        $proposal->doc_signed_ip_address = request()->ip();
        $proposal->doc_status = 'accepted';
        $proposal->save();

        /** ----------------------------------------------
         * record event [comment]
         * see database table to details of each key
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => -1,
            'event_creator_name' => $proposal->doc_signed_first_name, //(optional) non-registered users
            'event_item' => 'proposal',
            'event_item_id' => $proposal->doc_id,
            'event_item_lang' => 'event_accepted_proposal',
            'event_item_content' => $proposal->doc_title,
            'event_item_content2' => '',
            'event_clientid' => $proposal->doc_client_id,
            'event_parent_type' => 'proposal',
            'event_parent_id' => $proposal->doc_id,
            'event_parent_title' => $proposal->doc_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => 'proposal',
            'eventresource_id' => $proposal->doc_id,
            'event_notification_category' => 'notifications_billing_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users
            $users = $this->userrepo->mailingListProposals();
            //dd($users);
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\ProposalAccepted($user, [], $proposal);
                    $mail->build();
                }
            }
        }

        //redirect
        $jsondata['redirect_url'] = url("proposals/view/$id");

        //thank you message
        request()->session()->flash('success-notification-long', __('lang.request_has_been_completed'));

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * decline the proposal
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function declined($id) {

        //for sanity - do not apply any filters
        request()->merge([
            'do_not_apply_filters' => true,
        ]);

        //get the proposal
        $proposal = \App\Models\Proposal::Where('doc_unique_id', $id)->first();

        //update proposal
        $proposal->doc_signed_date = null;
        $proposal->doc_signed_first_name = null;
        $proposal->doc_signed_last_name = null;
        $proposal->doc_signed_signature_directory = null;
        $proposal->doc_signed_signature_filename = null;
        $proposal->doc_signed_ip_address = null;
        $proposal->doc_status = 'declined';
        $proposal->save();

        /** ----------------------------------------------
         * record event [comment]
         * see database table to details of each key
         * ----------------------------------------------*/
        $creator_name = (auth()->check()) ? auth()->user()->first_name : $proposal->doc_fallback_client_first_name;
        $data = [
            'event_creatorid' => -1,
            'event_creator_name' => $creator_name,
            'event_item' => 'proposal',
            'event_item_id' => $proposal->doc_id,
            'event_item_lang' => 'event_declined_proposal',
            'event_item_content' => $proposal->doc_title,
            'event_item_content2' => '',
            'event_clientid' => $proposal->doc_client_id,
            'event_parent_type' => 'proposal',
            'event_parent_id' => $proposal->doc_id,
            'event_parent_title' => $proposal->doc_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => 'proposal',
            'eventresource_id' => $proposal->doc_id,
            'event_notification_category' => 'notifications_billing_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users
            $users = $this->userrepo->mailingListProposals();
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\ProposalDeclined($user, [], $proposal);
                    $mail->build();
                }
            }
        }

        //redirect
        $jsondata['redirect_url'] = url("proposals/view/$id");

        //thank you message
        request()->session()->flash('success-notification-long', __('lang.request_has_been_completed'));

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * save signature as an image
     * @return array
     */
    public function saveSignature() {

        //unique file id & directory name
        $directory = Str::random(40);
        $file_name = 'signature.png';
        $file_path = "files/$directory/$file_name";
        $file_full_path = path_storage() . '/' . $file_path;

        //create signature image
        $signature_data = request('signature_code');
        $encoded_image = explode(",", $signature_data)[1];
        $decoded_image = base64_decode($encoded_image);

        //save file to directory
        Storage::put($file_path, $decoded_image);

        //trim white spaces from the image: https://image.intervention.io/v2/api/trim
        try {
            Image::make($file_full_path)->trim('top-left', null, 60)->save();
        } catch (NotReadableException $e) {
            Log::error("Unable to crop signature image", ['process' => '[accept-proposal]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        return [
            'directory' => $directory,
            'file_name' => $file_name,
            'file_path' => $file_path,
        ];

    }

    /**
     * data for the stats widget
     * @return array
     */
    private function statsWidget($data = array()) {

        //stats
        $count_accpeted = $this->proposalrepo->search('', ['stats' => 'count-accepted']);
        $count_pending = $this->proposalrepo->search('', ['stats' => 'count-new']);
        $count_declined = $this->proposalrepo->search('', ['stats' => 'count-declined']);
        $sum_accepted = $this->proposalrepo->search('', ['stats' => 'sum-accepted']);

        //default values
        $stats = [
            [
                'value' => runtimeMoneyFormat($sum_accepted),
                'title' => __('lang.accepted'),
                'percentage' => '100%',
                'color' => 'bg-info',
            ],
            [
                'value' => $count_accpeted,
                'title' => __('lang.accepted'),
                'percentage' => '100%',
                'color' => 'bg-success',
            ],
            [
                'value' => $count_pending,
                'title' => __('lang.pending'),
                'percentage' => '100%',
                'color' => 'bg-warning',
            ],
            [
                'value' => $count_declined,
                'title' => __('lang.declined'),
                'percentage' => '100%',
                'color' => 'bg-danger',
            ],
        ];
        //return
        return $stats;
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
                __('lang.proposals'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'proposals',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_sales' => 'active',
            'mainmenu_client_proposals' => 'active',
            'submenu_proposals' => 'active',
            'sidepanel_id' => 'sidepanel-filter-proposals',
            'dynamic_search_url' => url('proposals/search?action=search&proposalresource_id=' . request('proposalresource_id') . '&proposalresource_type=' . request('proposalresource_type')),
            'load_more_button_route' => 'proposals',
            'source' => 'list',
        ];

        //proposals list page
        if ($section == 'proposals') {

            //adjust
            $page['page'] = 'proposal';

            $page += [
                'meta_title' => __('lang.proposals'),
                'heading' => __('lang.proposals'),
            ];

            return $page;
        }

        //proposals list page
        if ($section == 'proposal') {

            //crumbs
            $page['crumbs'] = [
                __('lang.proposal'),
                '#' . $data->formatted_id,
            ];

            $page += [
                'meta_title' => __('lang.proposal'),
                'heading' => __('lang.proposal'),
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

        //return
        return $page;
    }
}