<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\LeadConvert;
use App\Http\Requests\Leads\LeadStoreUpdate;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Leads\ActivateResponse;
use App\Http\Responses\Leads\ArchiveResponse;
use App\Http\Responses\Leads\AttachFilesResponse;
use App\Http\Responses\Leads\ChangeCategoryUpdateResponse;
use App\Http\Responses\Leads\ChangeStatusResponse;
use App\Http\Responses\Leads\ChecklistResponse;
use App\Http\Responses\Leads\CloneResponse;
use App\Http\Responses\Leads\CloneStoreResponse;
use App\Http\Responses\Leads\contentResponse;
use App\Http\Responses\Leads\ConvertLeadResponse;
use App\Http\Responses\Leads\CreateResponse;
use App\Http\Responses\Leads\DestroyResponse;
use App\Http\Responses\Leads\IndexKanbanResponse;
use App\Http\Responses\Leads\IndexListResponse;
use App\Http\Responses\Leads\ShowResponse;
use App\Http\Responses\Leads\StoreChecklistResponse;
use App\Http\Responses\Leads\StoreCommentResponse;
use App\Http\Responses\Leads\StoreResponse;
use App\Http\Responses\Leads\UpdateChecklistResponse;
use App\Http\Responses\Leads\UpdateErrorResponse;
use App\Http\Responses\Leads\UpdateResponse;
use App\Http\Responses\Leads\UpdateStatusResponse;
use App\Http\Responses\Leads\UpdateTagsResponse;
use App\Models\Checklist;
use App\Models\Comment;
use App\Models\Lead;
use App\Permissions\AttachmentPermissions;
use App\Permissions\ChecklistPermissions;
use App\Permissions\CommentPermissions;
use App\Permissions\LeadPermissions;
use App\Repositories\AttachmentRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ChecklistRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CommentRepository;
use App\Repositories\CustomFieldsRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\LeadAssignedRepository;
use App\Repositories\LeadRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use App\Rules\NoTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Intervention\Image\Exception\NotReadableException;
use Validator;

class Leads extends Controller {

    /**
     * The lead repository instance.
     */
    protected $leadrepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The lead permission instance.
     */
    protected $leadpermissions;

    /**
     * The attachment repository instance.
     */
    protected $attachmentrepo;

    /**
     * The comment permission instance.
     */
    protected $commentpermissions;

    /**
     * The attachment permission instance.
     */
    protected $attachmentpermissions;

    /**
     * The file repository instance.
     */
    protected $filerepo;

    /**
     * The category repository instance.
     */
    protected $categoryrepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The checklist permission instance.
     */
    protected $checklistpermissions;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    /**
     * The customrepo repository instance.
     */
    protected $customrepo;

    public function __construct(
        LeadRepository $leadrepo,
        TagRepository $tagrepo,
        UserRepository $userrepo,
        AttachmentRepository $attachmentrepo,
        AttachmentPermissions $attachmentpermissions,
        CommentPermissions $commentpermissions,
        LeadPermissions $leadpermissions,
        ChecklistPermissions $checklistpermissions,
        CategoryRepository $categoryrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        Lead $leadmodel,
        CustomFieldsRepository $customrepo) {

        //parent
        parent::__construct();

        //vars
        $this->leadrepo = $leadrepo;
        $this->tagrepo = $tagrepo;
        $this->userrepo = $userrepo;
        $this->attachmentrepo = $attachmentrepo;
        $this->leadpermissions = $leadpermissions;
        $this->leadmodel = $leadmodel;
        $this->attachmentpermissions = $attachmentpermissions;
        $this->commentpermissions = $commentpermissions;
        $this->checklistpermissions = $checklistpermissions;
        $this->categoryrepo = $categoryrepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
        $this->customrepo = $customrepo;

        //authenticated
        $this->middleware('auth');

        //Permissions on methods
        $this->middleware('leadsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
            'changeStatusUpdate',
            'updateName',
            'updateValue',
            'updateStatus',
            'updateCategory',
            'updateContacted',
            'updatePhone',
            'updateEmail',
            'updateSource',
            'updateOrganisation',
            'updateAssigned',
            'archive',
            'activate',
            'cloneStore',
        ]);

        $this->middleware('leadsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('leadsMiddlewareShow')->only([
            'show',
            'showOrganisation',
            'updateOrganisation',
            'showCustomFields',
            'updateCustomFields',
            'showMyNotes',
            'createMyNotes',
            'editMyNotes',
            'deleteMyNotes',
        ]);

        $this->middleware('leadsMiddlewareEdit')->only([
            'edit',
            'update',
            'changeStatus',
            'changeStatusUpdate',
            'updateDescription',
            'updateTitle',
            'updateDateAdded',
            'updateName',
            'updateValue',
            'updateStatus',
            'updateCategory',
            'updateContacted',
            'updatePhone',
            'updateEmail',
            'updateSource',
            'updateOrganisation',
            'convertLead',
            'updateCustomFields',
            'archive',
            'activate',
            'editOrganisation',
            'editCustomFields',
            'updateCustomFields',
            'updateTags',
        ]);

        $this->middleware('leadsMiddlewareParticipate')->only([
            'storeComment',
            'storeChecklist',
            'attachFiles',
        ]);

        $this->middleware('leadsMiddlewareDeleteAttachment')->only([
            'deleteAttachment',
        ]);

        $this->middleware('leadsMiddlewareDownloadAttachment')->only([
            'downloadAttachment',
        ]);

        $this->middleware('leadsMiddlewareDeleteComment')->only([
            'deleteComment',
        ]);

        $this->middleware('leadsMiddlewareEditDeleteChecklist')->only([
            'updateChecklist',
            'deleteChecklist',
            'toggleChecklistStatus',
        ]);

        $this->middleware('leadsMiddlewareDestroy')->only([
            'destroy',
        ]);

        //only needed for the [action] methods
        $this->middleware('leadsMiddlewareBulkEdit')->only([
            'changeCategoryUpdate',
        ]);

        $this->middleware('leadsMiddlewareAssign')->only([
            'updateAssigned',
        ]);

        $this->middleware('leadsMiddlewareCloning')->only([
            'cloneTask',
            'cloneStore',
        ]);
    }

    /**
     * Display a listing of leads
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (auth()->user()->pref_view_leads_layout == 'list') {
            $payload = $this->indexList();
            return new IndexListResponse($payload);
        } else {
            $payload = $this->indexKanban();
            return new IndexKanbanResponse($payload);
        }
    }

    /**
     * Prepare the listing of leads (list view)
     * @return array
     */
    public function indexList() {

        //get leads
        $leads = $this->leadrepo->search();

        //apply some permissions
        if ($leads) {
            foreach ($leads as $lead) {
                $this->applyPermissions($lead);
            }
        }

        //process leads
        $this->processLeads($leads);

        //get all categories (type: lead) - for filter panel
        $categories = $this->categoryrepo->get('lead');

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('lead');

        //all available lead statuses
        $statuses = \App\Models\LeadStatus::all();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('leads'),
            'leads' => $leads,
            'stats' => $this->statsWidget(),
            'categories' => $categories,
            'tags' => $tags,
            'statuses' => $statuses,
        ];

        //show the view
        return $payload;
    }

    /**
     * Prepare the listing of leads (kanban view)
     * @return blade view | ajax view
     */
    public function indexKanban() {

        $boards = $this->leadBoards();

        //basic page settings
        $page = $this->pageSettings('leads', []);

        //page setting for embedded view
        if (request('source') == 'ext') {

            $page = $this->pageSettings('ext', []);
        }
        //get all categories (type: lead) - for filter panel
        $categories = $this->categoryrepo->get('lead');

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('lead');

        //reponse payload
        $payload = [
            'page' => $page,
            'boards' => $boards,
            'categories' => $categories,
            'stats' => $this->statsWidget(),
            'statuses' => \App\Models\LeadStatus::all(),
            'tags' => $tags,
        ];

        //show the view
        return $payload;
    }

    /**
     * process/group leads into boards
     * @return object
     */
    private function leadBoards() {

        $statuses = \App\Models\LeadStatus::orderBy('leadstatus_position', 'asc')->get();

        foreach ($statuses as $status) {

            request()->merge([
                'filter_single_lead_status' => $status->leadstatus_id,
                'query_type' => 'kanban',
            ]);

            //get leads
            $leads = $this->leadrepo->search();

            //process lead
            $this->processLeads($leads);

            //count rows
            $count = $leads->total();

            //apply some permissions
            if ($leads) {
                foreach ($leads as $lead) {
                    $this->applyPermissions($lead);
                }
            }

            //apply custom fields
            if ($leads) {
                foreach ($leads as $lead) {
                    $lead->fields = $this->getCustomFields($lead);
                }
            }

            //initial loadmore button
            if ($leads->currentPage() < $leads->lastPage()) {
                $boards[$status->leadstatus_id]['load_more'] = '';
                $boards[$status->leadstatus_id]['load_more_url'] = loadMoreButtonUrl($leads->currentPage() + 1, $status->leadstatus_id);
            } else {

                $boards[$status->leadstatus_id]['load_more'] = 'hidden';
                $boards[$status->leadstatus_id]['load_more_url'] = '';
            }

            $boards[$status->leadstatus_id]['name'] = $status->leadstatus_title;
            $boards[$status->leadstatus_id]['id'] = $status->leadstatus_id;
            $boards[$status->leadstatus_id]['leads'] = $leads;
            $boards[$status->leadstatus_id]['color'] = $status->leadstatus_color;

        }

        return $boards;
    }

    /**
     * Show the form for creating a new lead
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //lead categories
        $categories = $categoryrepo->get('lead');

        //get tags
        $tags = $this->tagrepo->getByType('lead');

        //all available lead statuses
        $statuses = \App\Models\LeadStatus::all();

        //all available lead sources
        $sources = \App\Models\LeadSources::all();

        //get customfields
        request()->merge([
            'filter_show_standard_form_status' => 'enabled',
            'filter_field_status' => 'enabled',
            'sort_by' => 'customfields_position',
        ]);
        $fields = $this->getCustomFields();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
            'tags' => $tags,
            'statuses' => $statuses,
            'sources' => $sources,
            'stats' => $this->statsWidget(),
            'fields' => $fields,
        ];

        //show the form
        return new CreateResponse($payload);
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
            'customfields_type' => 'leads',
        ]);

        //show all fields
        config(['settings.custom_fields_display_limit' => 1000]);

        //get fields
        $fields = $this->customrepo->search();

        //when in editing view - get current value that is stored for this custom field
        if ($obj instanceof \App\Models\Lead) {
            foreach ($fields as $field) {
                $field->current_value = $obj[$field->customfields_name];
            }
        }

        return $fields;
    }

    /**
     * Store a newly created lead in storage.
     * @param object LeadStoreUpdate instance of the repository
     * @param object LeadAssignedRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(LeadStoreUpdate $request, LeadAssignedRepository $assignedrepo) {

        //custom field validation
        if ($messages = $this->customFieldValidationFailed()) {
            abort(409, $messages);
        }

        //get the last row (order by position - desc)
        if ($last = $this->leadmodel::orderBy('lead_position', 'desc')->first()) {
            $position = $last->lead_position + config('settings.db_position_increment');
        } else {
            //default position increment
            $position = config('settings.db_position_increment');
        }

        //create the lead
        if (!$lead_id = $this->leadrepo->create($position)) {
            abort(409);
        }

        //add tags
        $this->tagrepo->add('lead', $lead_id);

        //assign project
        $assigned_users = $assignedrepo->add($lead_id);

        //get the leads object (friendly for rendering in blade template)
        $leads = $this->leadrepo->search($lead_id);

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'attachment_clientid' => 0,
                    'attachmentresource_type' => 'lead',
                    'attachmentresource_id' => $lead_id,
                    'attachment_directory' => $uniqueid,
                    'attachment_uniqiueid' => $uniqueid,
                    'attachment_filename' => $file_name,
                ];
                //process and save to db
                $this->attachmentrepo->process($data);
            }
        }

        //get the lead
        $leads = $this->leadrepo->search($lead_id);
        $lead = $leads->first();

        //apply permissions
        $this->applyPermissions($lead);

        //apply custom fields
        $lead->fields = $this->getCustomFields($lead);

        /** ----------------------------------------------
         * record assignment events and send emails
         * ----------------------------------------------*/
        foreach ($assigned_users as $assigned_user_id) {
            if ($assigned_user = \App\Models\User::Where('id', $assigned_user_id)->first()) {

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

        //counting rows
        $rows = $this->leadrepo->search();

        //reponse payload
        $payload = [
            'leads' => $leads,
            'lead' => $leads->first(),
            'count' => $rows->total(),
            'stats' => $this->statsWidget(),
        ];

        //card view response
        if (auth()->user()->pref_view_leads_layout == 'kanban') {
            request()->merge([
                'filter_lead_status' => request('lead_status'),
            ]);
            //counting rows
            $rows = $this->leadrepo->search();
            //payload
            $board['leads'] = $leads;
            $payload['board'] = $board;
            $payload['count'] = $rows->total();
        }

        //process reponse
        return new StoreResponse($payload);
    }

    /**
     * Returns false when all is ok
     * @return \Illuminate\Http\Response
     */
    public function customFieldValidationFailed() {

        //custom field validation
        $fields = \App\Models\CustomField::Where('customfields_type', 'leads')->get();
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
     * Display the specified lead
     * @param object CategoryRepository instance of the repository
     * @param object LeadAssignedRepository instance of the repository
     * @param object CommentRepository instance of the repository
     * @param object ChecklistRepository instance of the repository
     * @param object AttachmentRepository instance of the repository
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function show(
        CategoryRepository $categoryrepo,
        LeadAssignedRepository $assignedrepo,
        CommentRepository $commentrepo,
        ChecklistRepository $checklistrepo,
        AttachmentRepository $attachmentrepo, $id) {

        //get the lead
        $leads = $this->leadrepo->search($id);

        //lead
        $lead = $leads->first();

        //process lead
        $this->processLead($lead);

        //apply permissions
        $this->applyPermissions($lead);

        //get tags
        $tags_resource = $this->tagrepo->getByResource('lead', $id);
        $tags_system = $this->tagrepo->getByType('lead');
        $tags = $tags_resource->merge($tags_system);
        $tags = $tags->unique('tag_title');

        //client categories
        $categories = $categoryrepo->get('lead');

        //get assigned users
        $assigned = $assignedrepo->getAssigned($id);

        //all available lead sources
        $sources = \App\Models\LeadSources::all();

        //all available lead statuses
        $statuses = \App\Models\LeadStatus::all();

        //comments
        request()->merge([
            'commentresource_type' => 'lead',
            'commentresource_id' => $id,
        ]);
        $comments = $commentrepo->search();
        foreach ($comments as $comment) {
            $this->applyCommentPermissions($comment);
        }

        //attachments
        request()->merge([
            'attachmentresource_type' => 'lead',
            'attachmentresource_id' => $id,
        ]);
        $attachments = $attachmentrepo->search();
        foreach ($attachments as $attachment) {
            $this->applyAttachmentPermissions($attachment);
        }

        //checklists
        request()->merge([
            'checklistresource_type' => 'lead',
            'checklistresource_id' => $id,
        ]);
        $checklists = $checklistrepo->search();
        foreach ($checklists as $checklist) {
            $this->applyChecklistPermissions($checklist);
        }

        //mark events as read
        \App\Models\EventTracking::where('parent_id', $id)
            ->where('parent_type', 'lead')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //get users reminders
        if ($reminder = \App\Models\Reminder::Where('reminderresource_type', 'lead')
            ->Where('reminderresource_id', $id)
            ->Where('reminder_userid', auth()->id())->first()) {
            $has_reminder = true;
        } else {
            $reminder = [];
            $has_reminder = false;
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('lead', $lead),
            'lead' => $lead,
            'id' => $id,
            'tags' => $tags,
            'current_tags' => $lead->tags,
            'assigned' => $assigned,
            'sources' => $sources,
            'statuses' => $statuses,
            'comments' => $comments,
            'attachments' => $attachments,
            'categories' => $categories,
            'checklists' => $checklists,
            'reminder' => $reminder,
            'resource_type' => 'lead',
            'resource_id' => $id,
            'has_reminder' => $has_reminder,
            'progress' => $this->checklistProgress($checklists),
        ];

        //showing just the tab
        if (request('show') == 'tab') {
            $payload['type'] = 'show-main';
            return new contentResponse($payload);
        }

        //response
        return new ShowResponse($payload);
    }

    /**
     * Show the form for editing the specified lead
     * @param object CategoryRepository instance of the repository
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //nothing here
    }
    /**
     * update a lead in storage.
     * @return \Illuminate\Http\Response
     */
    public function update(LeadStoreUpdate $request, LeadAssignedRepository $assignedrepo, $id) {

        //update
        if (!$this->leadrepo->update($id)) {
            abort(409);
        }

        //delete & update tags
        $this->tagrepo->delete('lead', $id);
        $this->tagrepo->add('lead', $id);

        //if available
        if (request('edit_assigned')) {
            //update assigned
            $assignedrepo->delete($id);
            $assigned_users = $assignedrepo->add($id);
        }

        //get the lead
        $leads = $this->leadrepo->search($id);

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'attachment_clientid' => 0,
                    'attachmentresource_type' => 'lead',
                    'attachmentresource_id' => $id,
                    'attachment_directory' => $uniqueid,
                    'attachment_uniqiueid' => $uniqueid,
                    'attachment_filename' => $file_name,
                ];
                //process and save to db
                $this->attachmentrepo->process($data);
            }
        }

        //apply permissions
        $this->applyPermissions($leads->first());

        //process leads
        $this->processLeads($leads);

        //reponse payload
        $payload = [
            'leads' => $leads,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified lead from storage
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo) {

        //delete each record in the array
        $allrows = array();

        foreach (request('ids') as $id => $value) {

            //only checked items
            if ($value == 'on') {
                //delete lead
                $destroyrepo->destroyLead($id);
                //add to array
                $allrows[] = $id;
            }
        }

        //reponse payload
        $payload = [
            'lead_id' => $id,
            'allrows' => $allrows,
            'stats' => $this->statsWidget(),
        ];

        //generate a response
        return new DestroyResponse($payload);

    }

    /**
     * send each lead for processing
     * @param object leads collection of the lead model
     * @return object
     */
    private function processLeads($leads = '') {
        //sanity - make sure this is a valid leads object
        if ($leads instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            foreach ($leads as $lead) {
                $this->processLead($lead);
            }
        }
    }

    /**
     * check the lead for the following:
     *    1. Check if lead is assigned to me - add 'assigned_to_me' (yes/no) attribute
     *    2. check if there are any running timers on the leads - add 'running_timer' (yes/no)
     * @param object lead instance of the lead model
     * @return object
     */
    private function processLead($lead = '') {

        //sanity - make sure this is a valid lead object
        if ($lead instanceof \App\Models\Lead) {

            //default values
            $lead->assigned_to_me = false;
            $lead->has_attachments = false;
            $lead->has_comments = false;
            $lead->has_checklist = false;

            //check if the lead is assigned to me
            foreach ($lead->assigned as $user) {
                if ($user->id == auth()->id()) {
                    //its assigned to me
                    $lead->assigned_to_me = true;
                }
            }

            $lead->has_attachments = ($lead->attachments_count > 0) ? true : false;
            $lead->has_comments = ($lead->comments_count > 0) ? true : false;
            $lead->has_checklist = ($lead->checklists_count > 0) ? true : false;

            //custom fields
            $lead->fields = $this->getCustomFields($lead);
        }
    }

    /**
     * Show the form for updating the lead
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all lead categories
        $categories = $categoryrepo->get('lead');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * Show the form for updating the lead
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'lead')
            ->first()) {
            abort(409, __('lang.category_not_found'));
        }

        //update each lead
        $allrows = array();
        foreach (request('ids') as $lead_id => $value) {
            if ($value == 'on') {
                $lead = \App\Models\Lead::Where('lead_id', $lead_id)->first();
                //update the category
                $lead->lead_categoryid = request('category');
                $lead->save();
                //get the lead in rendering friendly format
                $leads = $this->leadrepo->search($lead_id);
                //apply permissions
                $this->applyPermissions($leads->first());
                //update custom fields
                $lead->fields = $this->getCustomFields($leads->first());
                //add to array
                $allrows[] = $leads;
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
     * Show the form for changing a leads status
     * @return \Illuminate\Http\Response
     */
    public function changeStatus() {

        //get the lead
        $lead = \App\Models\Lead::Where('lead_id', request()->route('lead'))->first();

        //all available lead statuses
        $statuses = \App\Models\LeadStatus::all();

        //reponse payload
        $payload = [
            'lead' => $lead,
            'statuses' => $statuses,
        ];

        //show the form
        return new ChangeStatusResponse($payload);
    }

    /**
     * change status lead status
     * @return \Illuminate\Http\Response
     */
    public function changeStatusUpdate() {

        //validate the lead exists
        $lead = \App\Models\Lead::Where('lead_id', request()->route('lead'))->first();

        //update the lead
        $lead->lead_status = request('lead_status');
        $lead->save();

        //get refreshed lead
        $leads = $this->leadrepo->search(request()->route('lead'));

        //clients contacts (needed for left panel - on update)
        $contacts = \App\Models\User::where('clientid', $lead['lead_clientid'])->where('type', 'client')->get();

        //apply permissions
        $this->applyPermissions($leads->first());

        //process leads
        $this->processLeads($leads);

        //reponse payload
        $payload = [
            'leads' => $leads,
            'lead_id' => request()->route('lead'),
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * pass the lead through the LeadPermissions class and apply user permissions.
     * @param object lead instance of the lead model
     * @return \Illuminate\Http\Response
     */
    private function applyPermissions($lead = '') {

        //sanity - make sure this is a valid lead object
        if ($lead instanceof \App\Models\Lead) {
            //edit permissions
            $lead->permission_edit_lead = $this->leadpermissions->check('edit', $lead);
            //delete permissions
            $lead->permission_delete_lead = $this->leadpermissions->check('delete', $lead);
            //edit participate
            $lead->permission_participate = $this->leadpermissions->check('participate', $lead);
        }
    }

    /**
     * update lead description
     * @param int $id lead id
     * @return object
     */
    public function updateDescription($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //update
        $lead->lead_description = request('lead_description');
        $lead->save();

        //update card description
        $jsondata['dom_html'][] = [
            'selector' => '#card-description-container',
            'action' => 'replace',
            'value' => clean($lead->lead_description),
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#card-description-container',
            'action' => 'show',
        ];

        return response()->json($jsondata);

    }

    /**
     * save uploaded files
     * @param object DestroyRepository instance of the repository
     * @param object DestroyRepository instance of the repository
     * @param object Request instance of the request object
     * @param int $id client id
     * @return
     */
    public function attachFiles(Request $request, AttachmentRepository $attachmentrepo, $id) {

        //validate the lead exists
        $lead = $this->leadmodel::find($id);

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //defaults
            $file_type = 'file';

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file name
            $filename = $file->getClientOriginalName();

            //filepath
            $file_path = BASE_DIR . "/storage/files/$directory/$filename";

            //extension
            $extension = pathinfo($file_path, PATHINFO_EXTENSION);

            //thumb path
            $thumb_name = generateThumbnailName($filename);
            $thumb_path = BASE_DIR . "/storage/files/$directory/$thumb_name";

            //create directory
            Storage::makeDirectory("files/$directory");

            //save file to directory
            Storage::putFileAs("files/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            if (is_array(@getimagesize($file_path))) {
                $file_type = 'image';
                try {
                    $img = Image::make($file_path)->resize(null, 90, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($thumb_path);
                } catch (NotReadableException $e) {
                    $message = $e->getMessage();
                    Log::error("[Image Library] failed to create uplaoded image thumbnail. Image type is not supported on this server", ['process' => '[permissions]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $message]);
                    abort(409, __('lang.image_file_type_not_supported'));
                }
            }

            //save files
            $data = [
                'attachment_clientid' => $lead->lead_clientid,
                'attachment_uniqiueid' => $uniqueid,
                'attachment_directory' => $directory,
                'attachment_filename' => $filename,
                'attachment_extension' => $extension,
                'attachment_type' => $file_type,
                'attachment_size' => humanFileSize(filesize($file_path)),
                'attachment_thumbname' => $thumb_name,
                'attachmentresource_type' => 'lead',
                'attachmentresource_id' => $id,
            ];
            $attachment_id = $attachmentrepo->create($data);

            //get refreshed attachment
            $attachments = $attachmentrepo->search($attachment_id);
            $attachment = $attachments->first();
            //apply permissions
            $this->applyAttachmentPermissions($attachments->first());

            //get lead
            $leads = $this->leadrepo->search($id);
            $lead = $leads->first();
            $this->processLead($lead);

            /** ----------------------------------------------
             * record event [attachment]
             * ----------------------------------------------*/
            $data = [
                'event_creatorid' => auth()->id(),
                'event_item' => 'attachment',
                'event_item_id' => $attachment_id,
                'event_item_lang' => 'event_attached_a_file',
                'event_item_content' => $filename,
                'event_item_content2' => "leads/download-attachment/$uniqueid",
                'event_parent_type' => 'lead',
                'event_parent_id' => $lead->lead_id,
                'event_parent_title' => $lead->lead_title,
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'yes',
                'event_clientid' => '',
                'eventresource_type' => 'lead',
                'eventresource_id' => $lead->lead_id,
                'event_notification_category' => 'notifications_leads_activity',
            ];
            //record event
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $this->leadpermissions->check('users', $lead);
                //record notification
                $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
            }

            /** ----------------------------------------------
             * send email [attachment]
             * ----------------------------------------------*/
            if (isset($emailusers) && is_array($emailusers)) {
                $data = $attachment->toArray();
                //send to users
                if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                    foreach ($users as $user) {
                        $mail = new \App\Mail\LeadFileUploaded($user, $data, $lead);
                        $mail->build();
                    }
                }
            }

            //reponse payload
            $payload = [
                'attachments' => $attachments,
                'leads' => $leads,
            ];

            //show the form
            return new AttachFilesResponse($payload);
        }
    }

    /**
     * apply permissions to each attachment
     * @param object $attachment instance of the attachment model object
     * @return object
     */
    private function applyAttachmentPermissions($attachment = '') {

        //sanity - make sure this is a valid object
        if ($attachment instanceof \App\Models\Attachment) {
            //delete permissions
            $attachment->permission_delete_attachment = $this->attachmentpermissions->check('delete', $attachment);
        }
    }

    /**
     * delete an attachment
     * @return \Illuminate\Http\Response
     */
    public function deleteAttachment() {

        //check if file exists in the database
        $attachment = \App\Models\Attachment::Where('attachment_uniqiueid', request()->route('uniqueid'))->first();

        //confirm thumb exists
        if ($attachment->attachment_directory != '') {
            if (Storage::exists("files/$attachment->attachment_directory")) {
                Storage::deleteDirectory("files/$attachment->attachment_directory");
            }
        }

        $attachment->delete();

        //hide and remove row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#card_attachment_' . $attachment->attachment_uniqiueid,
            'action' => 'slideup-slow-remove',
        );

        //response
        return response()->json($jsondata);
    }

    /**
     * download an attachment
     * @return \Illuminate\Http\Response
     */
    public function downloadAttachment() {

        //check if file exists in the database
        $attachment = \App\Models\Attachment::Where('attachment_uniqiueid', request()->route('uniqueid'))->first();

        //confirm thumb exists
        if ($attachment->attachment_filename != '') {
            $file_path = "files/$attachment->attachment_directory/$attachment->attachment_filename";
            if (Storage::exists($file_path)) {
                return Storage::download($file_path);
            }
        }
        abort(404);
    }

    /**
     * update lead title
     * @param int $id lead id
     * @return object
     */
    public function updateTitle($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validation
        if (hasHTML(request('lead_title'))) {
            //[type options] error|success
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => __('lang.title') . ' ' . __('lang.must_not_contain_any_html'),
            ];

            //update back the title
            $jsondata['dom_html'][] = [
                'selector' => '#card-title-editable',
                'action' => 'replace',
                'value' => safestr($lead->lead_title),
            ];
            return response()->json($jsondata);
        }

        //validation
        if (!request()->filled('lead_title')) {

            //[type options] error|success
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => __('lang.title_is_required'),
            ];

            //update back the title
            $jsondata['dom_html'][] = [
                'selector' => '#card-title-editable',
                'action' => 'replace',
                'value' => safestr($lead->lead_title),
            ];

            return response()->json($jsondata);

        } else {
            $lead->lead_title = request('lead_title');
            $lead->save();

            //get refreshed & reprocess
            $leads = $this->leadrepo->search($id);
            $this->processLead($leads->first());

            //update table row
            $jsondata['dom_html'][] = [
                'selector' => "#table_lead_title_$id",
                'action' => 'replace',
                'value' => str_limit(safestr($lead->lead_title), 25),
            ];

            //update kanban card title
            $jsondata['dom_html'][] = [
                'selector' => "#kanban_lead_title_$id",
                'action' => 'replace',
                'value' => str_limit(safestr($lead->lead_title), 45),
            ];

            //update card
            $jsondata['dom_html'][] = [
                'selector' => '#card-title-editable',
                'action' => 'replace',
                'value' => safestr($lead->lead_title),
            ];

            return response()->json($jsondata);
        }
    }

    /**
     * update lead priority
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateTags($id) {

        //delete & update tags
        $this->tagrepo->delete('lead', $id);
        $this->tagrepo->add('lead', $id);

        //get tags
        $tags_resource = $this->tagrepo->getByResource('lead', $id);
        $tags_system = $this->tagrepo->getByType('lead');
        $tags = $tags_resource->merge($tags_system);
        $tags = $tags->unique('tag_title');

        //get refreshed lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //reponse payload
        $payload = [
            'lead' => $lead,
            'tags' => $tags,
            'current_tags' => $lead->tags,
        ];

        //process reponse
        return new UpdateTagsResponse($payload);
    }

    /**
     * post a lead comment
     * @param object CommentRepository instance of the repository
     * @param int $id lead id
     * @return object
     */
    public function storeComment(CommentRepository $commentrepo, $id) {

        //validate
        $validator = Validator::make(request()->all(), [
            'comment_text' => [
                'required',
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

        request()->merge([
            'commentresource_type' => 'lead',
            'commentresource_id' => $id,
            'comment_text' => request('comment_text'),
        ]);
        $comment_id = $commentrepo->create();

        //get complete comment
        $comments = $commentrepo->search($comment_id);
        $comment = $comments->first();
        $this->applyCommentPermissions($comments->first());

        //get lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();
        $this->processLead($lead);

        /** ----------------------------------------------
         * record event [coment]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'comment',
            'event_item_id' => $comment->comment_id,
            'event_item_lang' => 'event_posted_a_comment',
            'event_item_content' => $comment->comment_text,
            'event_item_content2' => '',
            'event_parent_type' => 'lead',
            'event_parent_id' => $lead->lead_id,
            'event_parent_title' => $lead->lead_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'event_clientid' => '',
            'eventresource_type' => 'lead',
            'eventresource_id' => $lead->lead_id,
            'event_notification_category' => 'notifications_leads_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users
            $users = $this->leadpermissions->check('users', $lead);
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [comment]
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //the comment
            $data = $comment->toArray();
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\LeadComment($user, $data, $lead);
                    $mail->build();
                }
            }
        }

        //reponse payload
        $payload = [
            'comments' => $comments,
            'leads' => $leads,
        ];

        //show the form
        return new StoreCommentResponse($payload);
    }

    /**
     * download an attachment
     * @param object DestroyRepository instance of the repository
     * @param object Comment instance of the comment model object
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function deleteComment(DestroyRepository $destroyrepo, Comment $comment, $id) {

        //delete comment
        $destroyrepo->destroyComment($id);

        //hide and remove row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#card_comment_' . $comment->comment_id,
            'action' => 'slideup-slow-remove',
        );

        //response
        return response()->json($jsondata);
    }

    /**
     * post a lead checklist
     * @param object ChecklistRepository instance of the repository
     * @return object
     */
    public function StoreChecklist(ChecklistRepository $checklistrepo, $id) {

        //validate
        $validator = Validator::make(request()->all(), [
            'checklist_text' => [
                'required',
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'type' => 'store-checklist',
                'error_message' => $messages,
            ]);
        }

        //we are creating a new list
        request()->merge([
            'checklistresource_type' => 'lead',
            'checklistresource_id' => $id,
            'checklist_text' => request('checklist_text'),
        ]);

        //get next position
        if ($last = \App\Models\Checklist::Where('checklistresource_type', 'lead')
            ->Where('checklistresource_id', $id)
            ->orderBy('checklist_position', 'desc')
            ->first()) {
            $position = $last->checklist_position + 1;
        } else {
            //default position
            $position = 1;
        }
        //save checklist
        $checklist_id = $checklistrepo->create($position);

        //get complete checklist
        $checklists = $checklistrepo->search($checklist_id);
        $this->applyChecklistPermissions($checklists->first());

        //get lead
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'checklists' => $checklists,
            'progress' => $this->checklistProgress($checklistrepo->search()),
            'leads' => $leads,
        ];

        //show the form
        return new StoreChecklistResponse($payload);
    }

    /**
     * update a lead checklist
     * @param object ChecklistRepository instance of the repository
     * @return object
     */
    public function UpdateChecklist(ChecklistRepository $checklistrepo, $id) {

        //validate
        $validator = Validator::make(request()->all(), [
            'checklist_text' => [
                'required',
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'type' => 'store-checklist',
                'error_message' => $messages,
            ]);
        }

        //update checklist
        $checklist = \App\Models\Checklist::Where('checklist_id', $id)->first();
        $checklist->checklist_text = request('checklist_text');
        $checklist->save();

        //get refreshed
        $checklists = $checklistrepo->search($id);
        $this->applyChecklistPermissions($checklists->first());

        //reponse payload
        $payload = [
            'checklist' => $checklist,
            'checklists' => $checklists,
        ];

        //show the form
        return new UpdateChecklistResponse($payload);
    }

    /**
     * delete checklist
     * @param object ChecklistRepository instance of the repository
     * @param object Checklist instance of the Checklist model object
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function deleteChecklist(Checklist $checklist, ChecklistRepository $checklistrepo) {

        //check if file exists in the database
        $checklist = $checklist::find(request()->route('checklistid'));

        //some data
        $resource_id = $checklist->checklistresource_id;
        $checklist_id = $checklist->checklist_id;

        //delete
        $checklist->delete();

        //checklists
        request()->merge([
            'checklistresource_type' => 'lead',
            'checklistresource_id' => $resource_id,
        ]);
        $checklists = $checklistrepo->search();

        //reponse payload
        $payload = [
            'progress' => $this->checklistProgress($checklists),
            'action' => 'delete',
            'checklistid' => $checklist_id,
        ];

        //show the form
        return new ChecklistResponse($payload);
    }

    /**
     * delete checklist
     * @param object Checklist instance of the Checklist model object
     * @param object ChecklistRepository instance of the repository
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function toggleChecklistStatus(Checklist $checklist, ChecklistRepository $checklistrepo) {

        //check if file exists in the database
        $checklist = $checklist::find(request()->route('checklistid'));

        if (request('card_checklist') == 'on') {
            $checklist->checklist_status = 'completed';
        } else {
            $checklist->checklist_status = 'pending';
        }

        //save
        $checklist->save();

        //checklists
        request()->merge([
            'checklistresource_type' => 'lead',
            'checklistresource_id' => $checklist->checklistresource_id,
        ]);
        $checklists = $checklistrepo->search();

        //reponse payload
        $payload = [
            'progress' => $this->checklistProgress($checklists),
        ];

        //show the form
        return new ChecklistResponse($payload);
    }

    /**
     * create the checklists progress bar data
     * @param object checklistProgress instance of the checlkist collection object
     * @return object
     */
    private function checklistProgress($checklists) {

        $progress['bar'] = 'w-0'; //css width %
        $progress['completed'] = '---';

        //sanity - make sure this is a valid leads object
        if ($checklists instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $count = 0;
            $completed = 0;
            foreach ($checklists as $checklist) {
                if ($checklist->checklist_status == 'completed') {
                    $completed++;
                }
                $count++;
            }
            //finial
            $progress['completed'] = "$completed/$count";
            if ($count > 0) {
                $percentage = round(($completed / $count) * 100);
                $progress['bar'] = "w-$percentage";
            }
        }

        return $progress;
    }

    /**
     * apply permissions to each comment
     * @param object comment instance of the comment model object
     * @return \Illuminate\Http\Response
     */
    private function applyCommentPermissions($comment = '') {

        //sanity - make sure this is a valid object
        if ($comment instanceof \App\Models\Comment) {
            //delete permissions
            $comment->permission_delete_comment = $this->commentpermissions->check('delete', $comment);
        }
    }

    /**
     * apply permissions to each comment
     * @param object checklist instance of the resource model object
     * @return object
     */
    private function applyChecklistPermissions($checklist = '') {

        //sanity - make sure this is a valid object
        if ($checklist instanceof \App\Models\Checklist) {
            //delete permissions
            $checklist->permission_edit_delete_checklist = $this->checklistpermissions->check('edit-delete', $checklist);
        }
    }

    /**
     * update lead date added
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateDateAdded($id) {

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_created' => [
                'required',
                'date',
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'reset_target' => '#lead-date-added-container',
                'reset_value' => runtimeDate($lead->lead_created),
                'error_message' => $messages,
            ]);
        }

        $lead->lead_created = request('lead_created');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'leads' => $leads,
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead name
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateName($id) {

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_firstname' => [
                'required',
                new NoTags,
            ],
            'lead_lastname' => [
                'required',
                new NoTags,
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'type' => 'update-name',
                'reset_target' => '#card-lead-firstname-containter',
                'reset_value' => $lead->lead_firstname,
                'reset_target2' => '#card-lead-element-container-name',
                'reset_value2' => $lead->lead_lastname,
                'error_message' => $messages,
            ]);
        }

        //validate
        $lead->lead_firstname = request('lead_firstname');
        $lead->lead_lastname = request('lead_lastname');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'type' => 'update-name',
            'firstname' => $lead->lead_firstname,
            'firstlast' => $lead->lead_lastname,
            'leads' => $leads,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead status
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //old status
        $old_status = $lead->lead_status;

        //validate
        if (!\App\Models\LeadStatus::Where('leadstatus_id', request('lead_status'))->exists()) {
            //show error and reset values
            return new UpdateErrorResponse([
                'type' => 'update-status',
                'reset_target' => '#card-lead-status-text',
                'reset_value' => safestr(request('current_lead_status_text')),
                'error_message' => __('lang.invalid_status'),
            ]);
            //process reponse
            return new UpdateErrorResponse($payload);
        }

        $statuses = \App\Models\LeadStatus::Where('leadstatus_id', request('lead_status'))->first();
        $new_lead_status = $statuses->leadstatus_title;

        //validate
        $lead->lead_status = request('lead_status');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();
        $this->processLead($lead);

        /** ----------------------------------------------
         * record event [status]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'status',
            'event_item_id' => '',
            'event_item_lang' => 'event_changed_lead_status',
            'event_item_content' => $new_lead_status,
            'event_item_content2' => '',
            'event_parent_type' => 'lead',
            'event_parent_id' => $lead->lead_id,
            'event_parent_title' => $lead->lead_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'event_clientid' => '',
            'eventresource_type' => 'lead',
            'eventresource_id' => $lead->lead_id,
            'event_notification_category' => 'notifications_leads_activity',
        ];
        //record event
        if ($old_status != request('lead_status')) {
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $this->leadpermissions->check('users', $lead);
                //record notification
                $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
            }
        }

        /** ----------------------------------------------
         * send email [status]
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            $data = [];
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\LeadStatusChanged($user, $data, $lead);
                    $mail->build();
                }
            }
        }

        //reponse payload
        $payload = [
            'leads' => $leads,
            'old_status' => $old_status,
            'new_status' => request('lead_status'),
            'new_lead_status' => $new_lead_status,
        ];

        //process reponse
        return new UpdateStatusResponse($payload);
    }

    /**
     * update lead category
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateCategory($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        if (!\App\Models\Category::Where('category_id', request('lead_categoryid'))->Where('category_type', 'lead')->exists()) {
            //show error and reset values
            return new UpdateErrorResponse([
                'type' => 'update-category',
                'reset_target' => '#card-lead-category-text',
                'reset_value' => safestr(request('current_lead_category_text')),
                'error_message' => __('lang.invalid_category'),
            ]);
        }

        $categories = \App\Models\Category::Where('category_id', request('lead_categoryid'))->Where('category_type', 'lead')->first();
        $new_lead_category = $categories->category_name;

        //validate
        $lead->lead_categoryid = request('lead_categoryid');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'type' => 'update-category',
            'new_lead_category' => $new_lead_category,
            'leads' => $leads,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead value
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateValue($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_value' => [
                'nullable',
                'numeric',
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'type' => 'update-value',
                'value' => $lead->lead_value,
                'reset_target' => '#card-lead-value',
                'reset_value' => runtimeMoneyFormat($lead->lead_value),
                'error_message' => $messages,
            ]);
        }

        //save
        $lead->lead_value = request('lead_value');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'type' => 'update-value',
            'amount' => $lead->lead_value,
            'leads' => $leads,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead phone number
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updatePhone($id) {

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_phone' => [
                'nullable',
                new NoTags,
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'type' => 'update-phone',
                'reset_target' => '#card-lead-phone',
                'reset_value' => $lead->lead_phone,
                'error_message' => $messages,
            ]);
        }

        //validate
        $lead->lead_phone = request('lead_phone');
        $lead->save();

        //get refreshed
        $leads = $this->leadrepo->search($id);

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'type' => 'update-phone',
            'phone' => $lead->lead_phone,
            'leads' => $leads,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead email
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateEmail($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_email' => [
                'nullable',
                'email',
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'type' => 'update-email',
                'reset_target' => '#card-lead-email',
                'reset_value' => $lead->lead_email,
                'error_message' => $messages,
            ]);
        }

        //update
        $lead->lead_email = request('lead_email');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'type' => 'update-email',
            'email' => $lead->lead_email,
            'leads' => $leads,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead source
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateSource($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_source' => [
                'nullable',
                new NoTags,
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'type' => 'update-source',
                'reset_target' => '#card-lead-source-text',
                'reset_value' => $lead->lead_source,
                'error_message' => $messages,
            ]);
        }

        //validate
        $lead->lead_source = request('lead_source');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'type' => 'update-source',
            'source' => $lead->lead_source,
            'leads' => $leads,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead last contacted date
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateContacted($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_last_contacted' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    //skip for now, due to user/server time zone effect
                    if (\Carbon\Carbon::parse(request('lead_last_contacted'))->isFuture()) {
                        //return $fail(__('lang.date_cannot_be_in_future'));
                    }
                },
            ],
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            return new UpdateErrorResponse([
                'reset_target' => '#lead-contacted-container',
                'reset_value' => runtimeDate($lead->lead_created),
                'error_message' => $messages,
            ]);
        }

        //update
        $lead->lead_last_contacted = request('lead_last_contacted');
        $lead->save();

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'leads' => $leads,
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update lead assigned users
     * @param int $id lead id
     * @param object LeadAssignedRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function updateAssigned(LeadAssignedRepository $assignedrepo, $id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //currently assigned
        $currently_assigned = $lead->assigned->pluck('id')->toArray();

        //validation - data type
        if (request()->filled('assigned') && !is_array(request('assigned'))) {
            return new UpdateResponse([
                'type' => 'update-assigned',
                'leads' => $leads,
                'assigned' => $assignedrepo->getAssigned($id),
                'error' => true,
                'message' => __('lang.request_is_invalid'),
            ]);
        }

        //validate users exist
        if (request()->filled('assigned')) {
            foreach (request('assigned') as $user_id => $value) {
                if ($value == 'on') {
                    //validate user exists
                    if (\App\Models\User::Where('id', $user_id)->Where('type', 'team')->doesntExist()) {
                        return new UpdateResponse([
                            'type' => 'update-assigned',
                            'leads' => $leads,
                            'assigned' => $assignedrepo->getAssigned($id),
                            'error' => true,
                            'message' => __('lang.assiged_user_not_found'),
                        ]);
                    }

                }
            }
        }

        //delete all assigned
        $assignedrepo->delete($id);

        //add each user
        $newly_signed_users = [];
        if (request()->filled('assigned')) {
            foreach (request('assigned') as $user_id => $value) {
                if ($value == 'on') {
                    //add to assigned
                    $assigned_users = $assignedrepo->add($id, $user_id);
                    if (!in_array($user_id, $currently_assigned)) {
                        $newly_signed_users[] = $user_id;
                    }
                }
            }
        }

        /** ----------------------------------------------
         * record assignment events and send emails
         * ----------------------------------------------*/
        foreach ($newly_signed_users as $assigned_user_id) {
            if ($assigned_user = \App\Models\User::Where('id', $assigned_user_id)->first()) {

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

        //get refereshed
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //get assigned
        $assigned = $assignedrepo->getAssigned($id);

        //reponse payload
        $payload = [
            'type' => 'update-assigned',
            'leads' => $leads,
            'assigned' => $assigned,
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update a cards position (kanban drag & drop)
     * @return \Illuminate\Http\Response
     */
    public function updatePosition() {

        //validation
        if (!request()->filled('status')) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }
        if (!array_key_exists(request('status'), config('system.lead_statuses'))) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //validate
        if (!$this->leadmodel::find(request('lead_id'))) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search(request('lead_id'));
        $lead = $leads->first();

        //old status
        $old_status = $lead->lead_status;

        //(scenario - 1) card is placed in between 2 other cards
        if (is_numeric(request('previous_lead_id')) && is_numeric(request('next_lead_id'))) {
            //get previous lead
            if (!$previous_lead = $this->leadmodel::find(request('previous_lead_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //get next lead
            if (!$next_lead = $this->leadmodel::find(request('next_lead_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //calculate this leads new position & update it
            $new_position = ($previous_lead->lead_position + $next_lead->lead_position) / 2;
            $lead->lead_position = $new_position;
            $lead->lead_status = request('status');
            $lead->save();
        }

        //(scenario - 2) card is placed at the end of a list
        if (is_numeric(request('previous_lead_id')) && !request()->filled('next_lead_id')) {
            //get previous lead
            if (!$previous_lead = $this->leadmodel::find(request('previous_lead_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //calculate this leads new position & update it
            $new_position = $previous_lead->lead_position + config('settings.db_position_increment');
            $lead->lead_position = $new_position;
            $lead->lead_status = request('status');
            $lead->save();
        }

        //(scenario - 3) card is placed at the start of a list
        if (is_numeric(request('next_lead_id')) && !request()->filled('previous_lead_id')) {
            //get next lead
            if (!$next_lead = $this->leadmodel::find(request('next_lead_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //calculate this leads new position & update it
            $new_position = $next_lead->lead_position / 2;
            $lead->lead_position = $new_position;
            $lead->lead_status = request('status');
            $lead->save();
        }

        //(scenario - 4) card is placed on an empty board
        if (!request()->filled('previous_lead_id') && !request()->filled('next_lead_id')) {
            //update only status
            $lead->lead_status = request('status');
            $lead->save();
        }

        //status was changed - record event
        if ($old_status != $lead->lead_status) {
            //get refreshed lead
            $leads = $this->leadrepo->search(request('lead_id'));
            $lead = $leads->first();

            /** ----------------------------------------------
             * record event [status]
             * ----------------------------------------------*/
            $data = [
                'event_creatorid' => auth()->id(),
                'event_item' => 'status',
                'event_item_id' => '',
                'event_item_lang' => 'event_changed_lead_status',
                'event_item_content' => $lead->lead_status,
                'event_item_content2' => '',
                'event_parent_type' => 'lead',
                'event_parent_id' => $lead->lead_id,
                'event_parent_title' => $lead->lead_title,
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'yes',
                'event_clientid' => '',
                'eventresource_type' => 'lead',
                'eventresource_id' => $lead->lead_id,
                'event_notification_category' => 'notifications_leads_activity',
            ];
            //record event
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $this->leadpermissions->check('users', $lead);
                //record notification
                $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
            }

            /** ----------------------------------------------
             * send email [status]
             * ----------------------------------------------*/
            if (isset($emailusers) && is_array($emailusers)) {
                $data = [];
                //send to users
                if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                    foreach ($users as $user) {
                        $mail = new \App\Mail\LeadStatusChanged($user, $data, $lead);
                        $mail->build();
                    }
                }
            }

        }

    }

    /**
     * convert a lead into a customer
     * @param object LeadConvert instance of the request validation object
     * @param object ClientRepository instance of the repository
     * @param object UserRepository instance of the repository
     * @return object
     */
    public function convertLead(LeadConvert $request, ClientRepository $clientrepo, UserRepository $userrepo, $id) {

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //create new customer
        if (\App\Models\Client::where('client_created_from_leadid', $id)->exists()) {
            abort(409, __('lang.client_already_exists'));
        }

        //check for duplicate user
        if (\App\Models\User::Where('email', request('email'))->exists()) {
            abort(409, __('lang.user_already_exists'));
        }

        //set default client category
        request()->merge([
            'client_categoryid' => 2,
        ]);

        //save the client first
        if (request('send_welcome_email') == 'on') {
            if (!$client = $clientrepo->create([
                'send_email' => 'yes',
                'return' => 'client',
            ])) {
                abort(409);
            }
        } else {
            if (!$client = $clientrepo->create([
                'return' => 'client',
            ])) {
                abort(409);
            }
        }

        //update client
        $client->client_created_from_leadid = $id;
        $client->save();

        //client id
        $client_id = $client->client_id;

        //delete the lead (if requested)
        if (request('delete_lead') == 'on') {
            //delete lead
            $lead->delete();
            //payload
            $payload = [
                'action' => 'delete',
            ];
        } else {
            //update lead
            $lead->lead_converted = 'yes';
            $lead->lead_converted_clientid = $client_id;
            $lead->lead_converted_by_userid = auth()->id();
            $lead->lead_converted_date = now();
            $lead->lead_status = 2; //final stage
            $lead->save();
            //payload
            $payload = [
                'leads' => $leads,
                'action' => 'move',
            ];
        }

        //update any proposals and make them client proposals
        \App\Models\Proposal::where('docresource_type', 'lead')->where('doc_lead_id', $id)
            ->update([
                'docresource_type' => 'client',
                'docresource_id' => $client_id,
                'doc_client_id' => $client_id,
            ]);

        //general payload
        $payload += [
            'client_id' => $client_id,
        ];

        //get refreshed lead
        $leads = $this->leadrepo->search($id);

        //process reponse
        return new convertLeadResponse($payload);

    }

    /**
     * Archive a lead
     * @param object TimerRepository instance of the repository
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function archive($id) {

        //get lead and update status
        $lead = \App\Models\Lead::Where('lead_id', $id)->first();
        $lead->lead_active_state = 'archived';
        $lead->save();

        //get refreshed lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //apply permissions
        $this->applyPermissions($lead);

        //update custom fields
        $lead->fields = $this->getCustomFields($lead);

        //reponse payload
        $payload = [
            'leads' => $leads,
            'action' => 'archive',
        ];

        //show the form
        return new ArchiveResponse($payload);
    }

    /**
     * Activate a lead
     * @param object TimerRepository instance of the repository
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function activate($id) {

        //get lead and update status
        $lead = \App\Models\Lead::Where('lead_id', $id)->first();
        $lead->lead_active_state = 'active';
        $lead->save();

        //get refreshed lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //apply permissions
        $this->applyPermissions($lead);

        //reponse payload
        $payload = [
            'leads' => $leads,
            'action' => 'archive',
        ];

        //show the form
        return new ActivateResponse($payload);
    }

    /**
     * some main leads details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showMain($id) {

        //get leads
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //package to send to response
        $payload = [
            'type' => 'organisation',
            'lead' => $lead,
        ];

        //show the form
        return new contentResponse($payload);

    }

    /**
     * some leads organisation details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showOrganisation($id) {

        //get leads
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //package to send to response
        $payload = [
            'type' => 'show-organisation',
            'lead' => $lead,
        ];

        //show the form
        return new contentResponse($payload);

    }

    /**
     * some leads organisation details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editOrganisation($id) {

        //get leads
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //package to send to response
        $payload = [
            'type' => 'edit-organisation',
            'lead' => $lead,
        ];

        //show the form
        return new contentResponse($payload);

    }

    /**
     * update lead organisation
     * @param int $id lead id
     * @return \Illuminate\Http\Response
     */
    public function updateOrganisation($id) {

        //validate
        if (!$this->leadmodel::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the lead
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'lead_company_name' => [
                'nullable',
                new NoTags,
            ],
            'lead_job_position' => [
                'nullable',
                new NoTags,
            ],
            'lead_street' => [
                'nullable',
                new NoTags,
            ],
            'lead_city' => [
                'nullable',
                new NoTags,
            ],
            'lead_state' => [
                'nullable',
                new NoTags,
            ],
            'lead_zip' => [
                'nullable',
                new NoTags,
            ],
            'lead_country' => [
                'nullable',
                new NoTags,
            ],
            'lead_website' => [
                'nullable',
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

        //validate
        $lead->lead_company_name = request('lead_company_name');
        $lead->lead_job_position = request('lead_job_position');
        $lead->lead_street = request('lead_street');
        $lead->lead_city = request('lead_city');
        $lead->lead_state = request('lead_state');
        $lead->lead_zip = request('lead_zip');
        $lead->lead_country = request('lead_country');
        $lead->lead_website = request('lead_website');

        $lead->save();

        //get refreshed
        $leads = $this->leadrepo->search($id);

        //get refreshed & reprocess
        $leads = $this->leadrepo->search($id);
        $this->processLead($leads->first());

        //reponse payload
        $payload = [
            'type' => 'show-organisation',
            'lead' => $leads->first(),
        ];

        //show the form
        return new contentResponse($payload);
    }

    /**
     * show custom fields data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCustomFields($id) {

        //get leads
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //get customfields
        request()->merge([
            'sort_by' => 'customfields_position',
            'filter_field_status' => 'enabled',
        ]);
        $fields = $this->getCustomFields($lead);

        //package to send to response
        $payload = [
            'type' => 'show-custom-fields',
            'lead' => $lead,
            'fields' => $fields,
        ];

        //show the form
        return new contentResponse($payload);

    }

    /**
     * show custom fields data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editCustomFields($id) {

        //get leads
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //get customfields
        request()->merge([
            'sort_by' => 'customfields_position',
            'filter_field_status' => 'enabled',
        ]);
        $fields = $this->getCustomFields($lead);

        //package to send to response
        $payload = [
            'type' => 'edit-custom-fields',
            'lead' => $lead,
            'fields' => $fields,
        ];

        //show the form
        return new contentResponse($payload);

    }

    /**
     * show custom fields data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCustomFields($id) {

        //get leads
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //get customfields
        request()->merge([
            'sort_by' => 'customfields_position',
            'filter_field_status' => 'enabled',
        ]);
        $fields = $this->getCustomFields($lead);

        //update
        foreach ($fields as $field) {
            \App\Models\Lead::where('lead_id', $id)
                ->update([
                    $field->customfields_name => $_POST[$field->customfields_name],
                ]);
        }

        //refeshed data
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();
        $fields = $this->getCustomFields($lead);

        //package to send to response
        $payload = [
            'type' => 'show-custom-fields',
            'lead' => $lead,
            'fields' => $fields,
        ];

        //show the form
        return new contentResponse($payload);

    }

    /**
     * show my notes data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showMyNotes($id) {

        //get leads
        if ($note = \App\Models\Note::Where('noteresource_type', 'lead')
            ->Where('noteresource_id', $id)
            ->Where('note_creatorid', auth()->id())->first()) {
            $has_note = true;
        } else {
            $note = [];
            $has_note = false;
        }

        //refeshed data
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //package to send to response
        $payload = [
            'type' => 'show-notes',
            'note' => $note,
            'lead' => $lead,
            'has_note' => $has_note,
        ];

        //show the form
        return new contentResponse($payload);
    }

    /**
     * show my notes data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editMyNotes($id) {

        //get leads
        $note = \App\Models\Note::Where('noteresource_type', 'lead')
            ->Where('noteresource_id', $id)
            ->Where('note_creatorid', auth()->id())->first();

        //refeshed data
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //package to send to response
        $payload = [
            'type' => 'edit-notes',
            'note' => $note,
            'lead' => $lead,
        ];

        //show the form
        return new contentResponse($payload);
    }

    /**
     * delete note
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMyNotes($id) {

        //delete all notes by this user
        \App\Models\Note::Where('noteresource_type', 'lead')
            ->where('noteresource_id', $id)
            ->where('note_creatorid', auth()->id())->delete();

        //refeshed data
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        $payload = [
            'type' => 'show-notes',
            'note' => [],
            'lead' => $lead,
            'has_note' => false,
        ];

        //show the form
        return new contentResponse($payload);
    }

    /**
     * show text editor
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createMyNotes($id) {

        //delete all notes by this user
        \App\Models\Note::Where('noteresource_type', 'lead')
            ->where('noteresource_id', $id)
            ->where('note_creatorid', auth()->id())->delete();

        //refeshed data
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        $payload = [
            'type' => 'create-notes',
            'note' => [],
            'lead' => $lead,
        ];

        //show the form
        return new contentResponse($payload);
    }

    /**
     * update notes
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMyNotes($id) {

        //validation
        if (!request()->filled('lead_mynotes')) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //delete all notes by this user
        \App\Models\Note::Where('noteresource_type', 'lead')
            ->where('noteresource_id', $id)
            ->where('note_creatorid', auth()->id())->delete();

        //create note
        $note = new \App\Models\Note();
        $note->noteresource_type = 'lead';
        $note->noteresource_id = $id;
        $note->note_creatorid = auth()->id();
        $note->note_description = request('lead_mynotes');
        $note->note_visibility = 'private';
        $note->save();

        //refeshed data
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //package to send to response
        $payload = [
            'type' => 'show-notes',
            'note' => $note,
            'lead' => $lead,
            'has_note' => true,
        ];

        //show the form
        return new contentResponse($payload);
    }

    /**
     * show my notes data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showLogs($id) {

        $logs = \App\Models\Leadlog::Where('leadlog_leadid', $id)->orderBy('leadlog_id', 'DESC')->get();

        //package to send to response
        $payload = [
            'type' => 'show-logs',
            'logs' => $logs,
        ];

        //show the form
        return new contentResponse($payload);

    }

    /**
     * show form for cloning tasks
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cloneLead($id) {

        //get task
        $leads = $this->leadrepo->search($id);
        $lead = $leads->first();

        //all available lead statuses
        $statuses = \App\Models\LeadStatus::all();

        //payload
        $payload = [
            'lead' => $lead,
            'statuses' => $statuses,
        ];

        //show the view
        return new CloneResponse($payload);

    }

    /**
     * show form for cloning leads
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cloneStore(LeadAssignedRepository $assignedrepo, $id) {

        //lead
        $lead = \App\Models\Lead::Where('lead_id', $id)->first();

        //clone the lead
        $data = [
            'lead_title' => request('lead_title'),
            'lead_firstname' => request('lead_firstname'),
            'lead_lastname' => request('lead_lastname'),
            'lead_status' => request('lead_status'),
            'lead_email' => request('lead_email'),
            'lead_value' => request('lead_value'),
            'lead_phone' => request('lead_phone'),
            'lead_company_name' => request('lead_company_name'),
            'lead_website' => request('lead_website'),
            'copy_checklist' => (request('copy_checklist') == 'on') ? true : false,
            'copy_files' => (request('copy_files') == 'on') ? true : false,
        ];
        $new_lead = $this->leadrepo->cloneLead($lead, $data);

        //assign the lead to self, for none admin users
        if (auth()->user()->role->role_assign_leads == 'no') {
            $assignedrepo->add($new_lead->lead_id, auth()->id());
        }

        //get table friendly collection
        $leads = $this->leadrepo->search($new_lead->lead_id);

        //process for timers
        $this->processLeads($leads);

        //apply some permissions
        if ($leads) {
            foreach ($leads as $lead) {
                $this->applyPermissions($lead);
            }
        }

        //apply custom fields
        if ($leads) {
            foreach ($leads as $lead) {
                $lead->fields = $this->getCustomFields($lead);
            }
        }

        //payload
        $payload = [
            'lead' => $leads->first(),
            'leads' => $leads,
        ];

        //show the view
        return new CloneStoreResponse($payload);

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
                __('lang.leads'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'leads',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_leads' => 'active',
            'sidepanel_id' => 'sidepanel-filter-leads',
            'dynamic_search_url' => url('leads/search?action=search&leadresource_id=' . request('leadresource_id') . '&leadresource_type=' . request('leadresource_type')),
            'add_button_classes' => '',
            'load_more_button_route' => 'leads',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_lead'),
            'add_modal_create_url' => url('leads/create?leadresource_id=' . request('leadresource_id') . '&leadresource_type=' . request('leadresource_type')),
            'add_modal_action_url' => url('leads?leadresource_id=' . request('leadresource_id') . '&leadresource_type=' . request('leadresource_type')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //leads list page
        if ($section == 'leads') {
            $page += [
                'meta_title' => __('lang.leads'),
                'heading' => __('lang.leads'),

            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //lead page
        if ($section == 'lead') {
            //adjust
            $page['page'] = 'lead';
            //add
            $page += [
                'crumbs_special_class' => 'main-pages-crumbs',
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
        //TO DO IN THE FUTURE
        return [];
    }
}