<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\ProjectValidation;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Projects\ActivateResponse;
use App\Http\Responses\Projects\ArchiveResponse;
use App\Http\Responses\Projects\ChangeCategoryUpdateResponse;
use App\Http\Responses\Projects\ChangeStatusResponse;
use App\Http\Responses\Projects\CommonResponse;
use App\Http\Responses\Projects\CreateCloneResponse;
use App\Http\Responses\Projects\CreateResponse;
use App\Http\Responses\Projects\DestroyResponse;
use App\Http\Responses\Projects\DetailsResponse;
use App\Http\Responses\Projects\EditResponse;
use App\Http\Responses\Projects\PrefillProjectResponse;
use App\Http\Responses\Projects\ShowDynamicResponse;
use App\Http\Responses\Projects\ShowResponse;
use App\Http\Responses\Projects\StoreCloneResponse;
use App\Http\Responses\Projects\StoreResponse;
use App\Http\Responses\Projects\UpdateDetailsResponse;
use App\Http\Responses\Projects\UpdateProgressResponse;
use App\Http\Responses\Projects\UpdateResponse;
use App\Http\Responses\Projects\Views\CardResponse;
use App\Http\Responses\Projects\Views\ListResponse;
use App\Permissions\ProjectPermissions;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CloneProjectRepository;
use App\Repositories\CustomFieldsRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\FileRepository;
use App\Repositories\MilestoneCategoryRepository;
use App\Repositories\MilestoneRepository;
use App\Repositories\ProjectAssignedRepository;
use App\Repositories\ProjectManagerRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\TimerRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Projects extends Controller {

    /**
     * The project repository instance.
     */
    protected $projectrepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The project permission instance.
     */
    protected $projectpermissions;

    /**
     * The file repository instance.
     */
    protected $filerepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    /**
     * The customrepo repository instance.
     */
    protected $customrepo;

    //contruct
    public function __construct(
        ProjectRepository $projectrepo,
        ProjectPermissions $projectpermissions,
        TagRepository $tagrepo,
        UserRepository $userrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        FileRepository $filerepo,
        CustomFieldsRepository $customrepo) {

        //parent
        parent::__construct();

        //vars
        $this->projectrepo = $projectrepo;
        $this->tagrepo = $tagrepo;
        $this->userrepo = $userrepo;
        $this->projectpermissions = $projectpermissions;
        $this->filerepo = $filerepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
        $this->customrepo = $customrepo;

        //authenticated
        $this->middleware('auth');

        //Permissions on methods
        $this->middleware('projectsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
            'changeStatusUpdate',
            'archive',
            'activate',
            'changeProgressUpdate',
            'changeCoverImageUpdate',
            'assignedUsersUpdate',
        ]);

        $this->middleware('projectsMiddlewareShow')->only([
            'show',
            'showDynamic',
            'updateDescription',
            'details',
        ]);

        $this->middleware('projectsMiddlewareEdit')->only([
            'edit',
            'update',
            'changeStatus',
            'changeStatusUpdate',
            'stopAllTimers',
            'updateDescription',
            'archive',
            'activate',
            'changeProgress',
            'changeProgressUpdate',
            'changeCover',
            'changeCoverUpdate',
        ]);

        $this->middleware('projectsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('projectsMiddlewareDestroy')->only([
            'destroy',
        ]);

        //only needed for the [action] methods
        $this->middleware('projectsMiddlewareBulkEdit')->only([
            'changeCategoryUpdate',
        ]);
    }

    /**
     * Display a listing of projects
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function index(CategoryRepository $categoryrepo) {

        //get team projects
        $projects = $this->projectrepo->search();

        //apply some permissions
        if ($projects) {
            foreach ($projects as $project) {
                $this->applyPermissions($project);
            }
        }

        //get all categories (type: project) - for filter panel
        $categories = $categoryrepo->get('project');

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('project');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('projects'),
            'projects' => $projects,
            'stats' => $this->statsWidget(),
            'categories' => $categories,
            'tags' => $tags,
        ];

        //show the the corretc vew
        switch (auth()->user()->pref_view_projects_layout) {
        case 'list':
            return new ListResponse($payload);
        case 'card':
            return new CardResponse($payload);

        }
    }

    /**
     * Show the form for creating a new project
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //new project default permissions settings
        $project = $this->defautProjectPermissions();

        //project defaults
        $project['project_billing_rate'] = config('system.settings_projects_default_hourly_rate');
        $project['project_billing_estimated_hours'] = 0;
        $project['project_billing_costs_estimate'] = 0;

        //client categories
        $categories = $categoryrepo->get('project');

        //get templates
        request()->merge([
            'filter_project_type' => 'template',
        ]);
        $templates = $this->projectrepo->search('', ['apply_filters' => false]);

        //get tags
        $tags = $this->tagrepo->getByType('project');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'project' => $project,
            'templates' => $templates,
            'categories' => $categories,
            'tags' => $tags,
            'fields' => $this->getCustomFields(),
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created project in storage.
     * @param object ProjectValidation instance of the request validation object
     * @param object ProjectAssignedRepository instance of the repository
     * @param object MilestoneRepository instance of the repository
     * @param object MilestoneCategoryRepository instance of the repository
     * @param object ProjectManagerRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(
        ProjectValidation $request,
        ProjectAssignedRepository $assignedrepo,
        MilestoneRepository $milestonerepo,
        MilestoneCategoryRepository $milestonecategories,
        ClientRepository $clientrepo,
        CloneProjectRepository $clonerepo,
        ProjectManagerRepository $managerrepo) {

        //what are we creating
        if (is_numeric(request('project_template_selector'))) {
            $creation_type = 'template';
        } else {
            $creation_type = 'new';
        }

        //default permissions (if applicabe)
        if (config('system.settings_projects_allow_setting_permission_on_project_creation') == 'no') {
            $this->defautProjectPermissionsMerge();
        }

        //custom field validation
        if ($messages = $this->customFieldValidationFailed()) {
            abort(409, $messages);
        }

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
                'project_clientid' => $client_id,
            ]);
        }

        //create the project
        if (!$project_id = $this->projectrepo->create()) {
            abort(409);
        }

        //add tags
        $this->tagrepo->add('project', $project_id);

        //assign specified users
        if (config('system.settings_projects_permissions_basis') == 'user_roles') {
            $assigned_users = $assignedrepo->add($project_id, '');
        }

        //assign category users
        if (config('system.settings_projects_permissions_basis') == 'category_based') {
            $this->projectrepo->assignCategoryUsers(request('project_categoryid'), $project_id);
        }

        //project manager
        $managerrepo->add($project_id);

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'file_clientid' => request('project_clientid'),
                    'fileresource_type' => 'project',
                    'fileresource_id' => $project_id,
                    'file_directory' => $uniqueid,
                    'file_uniqueid' => $uniqueid,
                    'file_filename' => $file_name,
                ];
                //process and save to db
                $this->filerepo->process($data);
            }
        }

        //get the project object (friendly for rendering in blade template)
        $projects = $this->projectrepo->search($project_id, ['apply_filters' => false]);
        $project = $projects->first();

        //add default project categories
        if ($creation_type == 'new') {
            $position = $milestonecategories->addProjectMilestones($project);
            $milestonerepo->addUncategorised($project_id, $position);
        }

        //clone template projects resources
        if ($creation_type == 'template') {
            $data = [
                'new_project_id' => $project_id,
                'template_project_id' => request('project_template_selector'),
            ];
            $clonerepo->cloneTemplate($data);
        }

        //apply permissions
        $this->applyPermissions($project);

        //counting all rows
        $rows = $this->projectrepo->search();
        $count = $rows->count();

        /** ----------------------------------------------
         * record event [project created]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'new_project',
            'event_item_id' => '',
            'event_item_lang' => 'event_created_project',
            'event_item_content' => $project->project_title,
            'event_item_content2' => '',
            'event_parent_type' => 'project',
            'event_parent_id' => $project->project_id,
            'event_parent_title' => $project->project_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'event_clientid' => $project->project_clientid,
            'eventresource_type' => 'project',
            'eventresource_id' => $project->project_id,
            'event_notification_category' => 'notifications_projects_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users
            $users = $this->userrepo->getClientUsers($project->project_clientid, 'all', 'ids');
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [project created]
         * - This only needs to go to client account owner
         * ----------------------------------------------*/
        $data = [];
        //get account owner
        if ($owner = $this->userrepo->getClientAccountOwner($project->project_clientid)) {
            if ($owner->notifications_new_project == 'yes_email') {
                $mail = new \App\Mail\ProjectCreated($owner, $data, $project);
                $mail->build();
            }
        }

        /** ----------------------------------------------
         * record assignment events and send emails
         * ----------------------------------------------*/
        if (config('system.settings_projects_permissions_basis') == 'user_roles') {
            foreach ($assigned_users as $assigned_user_id) {
                if ($assigned_user = \App\Models\User::Where('id', $assigned_user_id)->first()) {

                    //record event
                    $data = [
                        'event_creatorid' => auth()->id(),
                        'event_item' => 'assigned',
                        'event_item_id' => '',
                        'event_item_lang' => 'event_assigned_user_to_a_project',
                        'event_item_lang_alt' => 'event_assigned_user_to_a_project_alt',
                        'event_item_content' => __('lang.assigned'),
                        'event_item_content2' => $assigned_user_id,
                        'event_item_content3' => $assigned_user->first_name,
                        'event_parent_type' => 'project',
                        'event_parent_id' => $project->project_id,
                        'event_parent_title' => $project->project_title,
                        'event_show_item' => 'yes',
                        'event_show_in_timeline' => 'yes',
                        'event_clientid' => $project->project_clientid,
                        'eventresource_type' => 'project',
                        'eventresource_id' => $project->project_id,
                        'event_show_in_timeline' => 'no',
                        'event_client_visibility' => 'no',
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
                            $mail = new \App\Mail\ProjectAssignment($assigned_user, $data, $project);
                            $mail->build();
                        }
                    }
                }
            }
        }

        //reponse payload
        $payload = [
            'projects' => $projects,
            'id' => $project_id,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified project
     * @param object TimerRepository instance of the repository
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function show(TimerRepository $timerrepo, $id) {

        //get the project
        $projects = $this->projectrepo->search($id);

        //project
        $project = $projects->first();

        //set page
        $page = $this->pageSettings('project', $project);

        //refresh project
        $this->projectrepo->refreshProject($project);

        //apply permissions
        $this->applyPermissions($project);

        //get tags
        $tags_resource = $this->tagrepo->getByResource('project', $id);
        $tags_user = $this->tagrepo->getByType('project');
        $tags = $tags_resource->merge($tags_user);
        $tags = $tags->unique('tag_title');

        //clients contacts
        $contacts = \App\Models\User::where('clientid', $project['project_clientid'])->where('type', 'client')->get();

        //set intitial loading of timeline
        $page['dynamic_url'] = url('timeline/project?source=ext&timelineresource_type=project&timelineresource_id=' . $project->project_id);

        /** --------------------------------------------------------------------------------
         *  mark general project event-tracking events as 'read'. Excluding the following,
         *  which must only be marked as read, when the actual content item has been viewed
         *  [excluding]
         *         - Task, Invoice, Estimate, Ticket, comment, file
         *
         * -------------------------------------------------------------------------------*/
        \App\Models\EventTracking::where('resource_id', $id)
            ->where('resource_type', 'project')
            ->whereNotIn('eventtracking_source', ['task', 'ticket', 'invoice', 'estimate', 'file', 'comment'])
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //stats - time logged
        $time_logged = $timerrepo->projectLoggedHours([
            'timer_projectid' => $id,
            'timer_billing_status' => 'all',
            'return' => 'human_readable',
        ]);

        //reponse payload
        $payload = [
            'page' => $page,
            'project' => $project,
            'time_logged' => $time_logged,
            'tags' => $tags,
            'contacts' => $contacts,
            'fields' => $this->getCustomFields($project),
        ];

        //response
        return new ShowResponse($payload);
    }

    /**
     * Display the specified project
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function showDynamic($id) {

        //get the project
        $projects = $this->projectrepo->search($id);

        //project
        $project = $projects->first();

        $page = $this->pageSettings('project', $project);

        //apply permissions
        $this->applyPermissions($project);

        //set dynamic url for use in template
        switch (request()->segment(3)) {
        case 'files':
        case 'invoices':
        case 'expenses':
        case 'estimates':
        case 'payments':
        case 'timesheets':
        case 'notes':
        case 'tickets':
        case 'milestones':
        case 'tasks':
            $sections = request()->segment(3);
            $section = rtrim($sections, 's');
            $page['dynamic_url'] = url($sections . '?source=ext&' . $section . 'resource_type=project&' . $section . 'resource_id=' . $project->project_id);
            break;
        case 'details':
            $page['dynamic_url'] = url('projects/' . $project->project_id . '/project-details');
            break;
        default:
            $page['dynamic_url'] = url('comments?source=ext&commentresource_type=project&commentresource_id=' . $project->project_id);
            break;
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'project' => $project,
        ];

        //response
        return new ShowDynamicResponse($payload);
    }

    /**
     * Show the form for editing the specified project
     * @param object CategoryRepository instance of the repository
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //get the project
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        //apply permissions
        $this->applyPermissions($project);

        //client categories
        $categories = $categoryrepo->get('project');

        //get project tags and users tags
        $tags_resource = $this->tagrepo->getByResource('project', $id);
        $tags_user = $this->tagrepo->getByType('project');
        $tags = $tags_resource->merge($tags_user);
        $tags = $tags->unique('tag_title');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'project' => $project,
            'categories' => $categories,
            'tags' => $tags,
            'fields' => $this->getCustomFields($project),
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified project in storage.
     * @param object ProjectValidation instance of the request validation object
     * @param object ProjectAssignedRepository instance of the repository
     * @param object ProjectManagerRepository instance of the repository
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectValidation $request, ProjectAssignedRepository $assignedrepo, ProjectManagerRepository $managerrepo, $id) {

        //get project
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        //default permissions (if applicabe)
        if (config('system.settings_projects_allow_setting_permission_on_project_creation') == 'no') {
            $this->defautProjectPermissionsMerge();
        }

        //custom field validation
        if ($messages = $this->customFieldValidationFailed()) {
            abort(409, $messages);
        }

        //update
        if (!$this->projectrepo->update($id)) {
            abort(409);
        }

        //delete & update tags
        $this->tagrepo->delete('project', $id);
        $this->tagrepo->add('project', $id);

        //currently assigned
        $currently_assigned = $project->assigned->pluck('id')->toArray();

        //add each user
        $newly_signed_users = [];
        $assignedrepo->delete($id);
        if (request()->filled('assigned')) {
            foreach (request('assigned') as $key => $user_id) {
                $assigned_users = $assignedrepo->add($id, $user_id);
                if (!in_array($user_id, $currently_assigned)) {
                    $newly_signed_users[] = $user_id;
                }
            }
        }
        //update manager
        $managerrepo->delete($id);
        $managerrepo->add($id);

        //get project
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        /** ----------------------------------------------
         * record assignment events and send emails
         * ----------------------------------------------*/
        foreach ($newly_signed_users as $assigned_user_id) {
            if ($assigned_user = \App\Models\User::Where('id', $assigned_user_id)->first()) {

                //record event
                $data = [
                    'event_creatorid' => auth()->id(),
                    'event_item' => 'assigned',
                    'event_item_id' => '',
                    'event_item_lang' => 'event_assigned_user_to_a_project',
                    'event_item_lang_alt' => 'event_assigned_user_to_a_project_alt',
                    'event_item_content' => __('lang.assigned'),
                    'event_item_content2' => $assigned_user_id,
                    'event_item_content3' => $assigned_user->first_name,
                    'event_parent_type' => 'project',
                    'event_parent_id' => $project->project_id,
                    'event_parent_title' => $project->project_title,
                    'event_show_item' => 'yes',
                    'event_show_in_timeline' => 'yes',
                    'event_clientid' => $project->project_clientid,
                    'eventresource_type' => 'project',
                    'eventresource_id' => $project->project_id,
                    'event_show_in_timeline' => 'no',
                    'event_client_visibility' => 'no',
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
                        $mail = new \App\Mail\ProjectAssignment($assigned_user, $data, $project);
                        $mail->build();
                    }
                }
            }
        }
        //apply permissions
        $this->applyPermissions($project);

        //reponse payload
        $payload = [
            'projects' => $projects,
            'project_id' => $id,
            'stats' => $this->statsWidget(),
        ];

        //generate a response
        return new UpdateResponse($payload);
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
            'customfields_type' => 'projects',
            'filter_show_standard_form_status' => 'enabled',
            'filter_field_status' => 'enabled',
            'sort_by' => 'customfields_position',
        ]);

        //show all fields
        config(['settings.custom_fields_display_limit' => 1000]);

        //get fields
        $fields = $this->customrepo->search();

        //when in editing view - get current value that is stored for this custom field
        if ($obj instanceof \App\Models\Project) {
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
        $fields = \App\Models\CustomField::Where('customfields_type', 'projects')->get();
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
     * Remove the specified project from storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo) {

        //delete each record in the array
        $allrows = array();

        foreach (request('ids') as $id => $value) {
            //only checked items
            if ($value == 'on') {
                //destroy the project and all linked items
                $destroyrepo->destroyProject($id);
                //add to array
                $allrows[] = $id;
            }
        }

        //reponse payload
        $payload = [
            'project_id' => $id,
            'allrows' => $allrows,
            'stats' => $this->statsWidget(),
        ];

        //generate a response
        return new DestroyResponse($payload);

    }

    /**
     * Return ajax details for project
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function details($id) {

        //get the project
        $project = $this->projectrepo->search($id, ['apply_filters' => false]);

        //get tags
        $tags = $this->tagrepo->getByResource('project', $id);

        //not found
        if (!$project = $project->first()) {
            abort(409, __('lang.project_not_found'));
        }

        //mark all project events as read
        \App\Models\EventTracking::where('resource_id', $id)
            ->where('resource_type', 'project')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('project', $project),
            'project' => $project,
            'tags' => $tags,
        ];

        //response
        return new DetailsResponse($payload);
    }

    /**
     * array of default project users permissions.
     * @return array
     */
    private function defautProjectPermissions() {
        //default permissions
        return [
            'clientperm_tasks_view' => config('system.settings_projects_clientperm_tasks_view'),
            'clientperm_tasks_collaborate' => config('system.settings_projects_clientperm_tasks_collaborate'),
            'clientperm_tasks_create' => config('system.settings_projects_clientperm_tasks_create'),
            'clientperm_timesheets_view' => config('system.settings_projects_clientperm_timesheets_view'),
            'clientperm_projects_view' => config('system.settings_projects_clientperm_projects_view'),
            'clientperm_assigned_view' => config('system.settings_projects_clientperm_assigned_view'),
            'assignedperm_tasks_collaborate' => config('system.settings_projects_assignedperm_tasks_collaborate'),
        ];
    }

    /**
     * merge default user permission for creating a project (client/team)
     * into the request object. This is normally used when the admin has set that
     * project permission options are not made available during project creation process
     * @return null
     */
    private function defautProjectPermissionsMerge() {

        //get the permissios array
        $permissions = $this->defautProjectPermissions();

        //loop and merge into request
        foreach ($permissions as $key => $value) {
            //change db setting to a checkbox type value (on/off)
            $value = ($value == 'yes') ? 'on' : '';
            request()->merge([$key => $value]);
        }
    }

    /**
     * Show the form for changing a projects status
     * @return \Illuminate\Http\Response
     */
    public function changeStatus() {

        //get the project
        $project = \App\Models\Project::Where('project_id', request()->route('project'))->first();

        //reponse payload
        $payload = [
            'project' => $project,
        ];

        //show the form
        return new ChangeStatusResponse($payload);
    }

    /**
     * Stop all the timers on this project
     * @param object TimerRepository instance of the repository
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function stopAllTimers(TimerRepository $timerepo, $id) {

        //stop all running timers for this project
        $data = [
            'timer_projectid' => $id,
        ];
        $timerepo->stopRunningTimers($data);

        //reponse payload
        $payload = [
            'type' => 'success-notification',
        ];

        //show the form
        return new CommonResponse($payload);
    }

    /**
     * Archive a project
     * @param object TimerRepository instance of the repository
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function archive($id) {

        //get project and update status
        $project = \App\Models\Project::Where('project_id', $id)->first();
        $project->project_active_state = 'archived';
        $project->save();

        //get refreshed project
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        //apply permissions
        $this->applyPermissions($project);

        //reponse payload
        $payload = [
            'projects' => $projects,
            'action' => 'archive',
        ];

        //show the form
        return new ArchiveResponse($payload);
    }

    /**
     * Activate a project
     * @param object TimerRepository instance of the repository
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function activate($id) {

        //get project and update status
        $project = \App\Models\Project::Where('project_id', $id)->first();
        $project->project_active_state = 'active';
        $project->save();

        //get refreshed project
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        //apply permissions
        $this->applyPermissions($project);

        //reponse payload
        $payload = [
            'projects' => $projects,
            'action' => 'archive',
        ];

        //show the form
        return new ActivateResponse($payload);
    }

    /**
     * change status project status
     * @return \Illuminate\Http\Response
     */
    public function changeStatusUpdate() {

        //validate the project exists
        $project = \App\Models\Project::Where('project_id', request()->route('project'))->first();

        //old status
        $old_status = $project->project_status;

        //validate
        if (!in_array(request('project_status'), ['not_started', 'in_progress', 'on_hold', 'cancelled', 'completed'])) {
            abort(409, __('lang.invalid_status'));
        }

        //update the project
        $project->project_status = request('project_status');
        $project->save();

        //get refreshed project
        $projects = $this->projectrepo->search(request()->route('project'), ['apply_filters' => false]);
        $project = $projects->first();

        //clients contacts (needed for left panel - on update)
        $contacts = \App\Models\User::where('clientid', $project['project_clientid'])->where('type', 'client')->get();

        //apply permissions
        $this->applyPermissions($project);

        /** ----------------------------------------------
         * record event [status]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'status',
            'event_item_id' => '',
            'event_item_lang' => 'event_changed_project_status',
            'event_item_content' => $project->project_status,
            'event_item_content2' => '',
            'event_parent_type' => 'project',
            'event_parent_id' => $project->project_id,
            'event_parent_title' => $project->project_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'event_clientid' => $project->project_clientid,
            'eventresource_type' => 'project',
            'eventresource_id' => $project->project_id,
            'event_notification_category' => 'notifications_projects_activity',
        ];
        //record event
        if ($old_status != request('project_status')) {
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $this->projectpermissions->check('users', $project);
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
                    $mail = new \App\Mail\ProjectStatusChanged($user, $data, $project);
                    $mail->build();
                }
            }
        }

        //reponse payload
        $payload = [
            'projects' => $projects,
            'project_id' => request()->route('project'),
            'stats' => $this->statsWidget(),
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * update project description and also the tags
     * @return \Illuminate\Http\Response
     */
    public function updateDescription() {

        //get the project
        $project = \App\Models\Project::Where('project_id', request()->route('project'))->first();

        //update description
        $project->project_description = request('description');

        //save
        $project->save();

        //delete & update tags
        $this->tagrepo->delete('project', $project->project_id);
        $this->tagrepo->add('project', $project->project_id);

        //get tags
        $tags = $this->tagrepo->getByResource('project', $project->project_id);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('project', $project),
            'project' => $project,
            'tags' => $tags,
        ];

        //response
        return new UpdateDetailsResponse($payload);

    }

    /**
     * Show the form for updating the project
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all project categories
        $categories = $categoryrepo->get('project');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * Show the form for updating the project
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'project')
            ->first()) {
            abort(409, __('lang.category_not_found'));
        }

        //update each project
        $allrows = array();
        foreach (request('ids') as $project_id => $value) {
            if ($value == 'on') {
                $project = \App\Models\Project::Where('project_id', $project_id)->first();

                //update the category
                $project->project_categoryid = request('category');
                $project->save();

                /** ----------------------------------------------------------------------------
                 * [CATEGORY USERS]
                 * update users assigned to this project, based on the new category users list
                 * ----------------------------------------------------------------------------*/
                if (config('system.settings_projects_permissions_basis') == 'category_based') {
                    $this->projectrepo->assignCategoryUsers(request('category'), $project_id);
                }

                //get the project in rendering friendly format
                $projects = $this->projectrepo->search($project_id, ['apply_filters' => false]);

                //apply permissions
                $this->applyPermissions($projects->first());
                //add to array
                $allrows[] = $projects;
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
     * Show the form for updating the project
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function assignedUsers($id) {

        //permission
        if (auth()->user()->role->role_assign_projects != 'yes') {
            abort(403);
        }

        //get the project
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        if (!$project = $projects->first()) {
            abort(404);
        }

        //assigned users
        $users = $project->assigned;

        $html = view('pages/projects/components/modals/assigned', compact('users', 'project'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * Show the form for updating the project
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function assignedUsersUpdate(ProjectAssignedRepository $assignedrepo, $id) {

        //get the project
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        if (!$project = $projects->first()) {
            abort(404);
        }

        //currently assigned
        $currently_assigned = $project->assigned->pluck('id')->toArray();

        //add each user
        $newly_signed_users = [];
        $assignedrepo->delete($id);
        if (request()->filled('assigned')) {
            foreach (request('assigned') as $key => $user_id) {
                $assigned_users = $assignedrepo->add($id, $user_id);
                if (!in_array($user_id, $currently_assigned)) {
                    $newly_signed_users[] = $user_id;
                }
            }
        }

        /** ----------------------------------------------
         * record assignment events and send emails
         * ----------------------------------------------*/
        foreach ($newly_signed_users as $assigned_user_id) {
            if ($assigned_user = \App\Models\User::Where('id', $assigned_user_id)->first()) {

                //record event
                $data = [
                    'event_creatorid' => auth()->id(),
                    'event_item' => 'assigned',
                    'event_item_id' => '',
                    'event_item_lang' => 'event_assigned_user_to_a_project',
                    'event_item_lang_alt' => 'event_assigned_user_to_a_project_alt',
                    'event_item_content' => __('lang.assigned'),
                    'event_item_content2' => $assigned_user_id,
                    'event_item_content3' => $assigned_user->first_name,
                    'event_parent_type' => 'project',
                    'event_parent_id' => $project->project_id,
                    'event_parent_title' => $project->project_title,
                    'event_show_item' => 'yes',
                    'event_show_in_timeline' => 'yes',
                    'event_clientid' => $project->project_clientid,
                    'eventresource_type' => 'project',
                    'eventresource_id' => $project->project_id,
                    'event_show_in_timeline' => 'no',
                    'event_client_visibility' => 'no',
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
                        $mail = new \App\Mail\ProjectAssignment($assigned_user, $data, $project);
                        $mail->build();
                    }
                }
            }
        }

        //get refreshed
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        //apply permissions
        $this->applyPermissions($project);

        //reponse payload
        $payload = [
            'projects' => $projects,
            'project_id' => request()->route('project'),
            'stats' => $this->statsWidget(),
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * pass the project through the ProjectPermissions class and apply user permissions.
     * @param object project instance of the project model object
     * @return object
     */
    private function applyPermissions($project = '') {

        //sanity - make sure this is a valid project object
        if ($project instanceof \App\Models\Project) {
            //edit permissions
            $project->permission_edit_project = $this->projectpermissions->check('edit', $project);
            //delete permissions
            $project->permission_delete_project = $this->projectpermissions->check('delete', $project);
        }
    }

    /**
     * show the form for cloning an project
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function createClone(CategoryRepository $categoryrepo, $id) {

        //get the project
        $project = \App\Models\Project::Where('project_id', $id)->first();

        //get tags
        $tags = $this->tagrepo->getByType('project');

        //project categories
        $categories = $categoryrepo->get('project');

        //reponse payload
        $payload = [
            'project' => $project,
            'tags' => $tags,
            'categories' => $categories,
        ];

        //show the form
        return new CreateCloneResponse($payload);
    }

    /**
     * clone the project
     * @return \Illuminate\Http\Response
     */
    public function storeClone(CloneProjectRepository $clonerepo, $id) {

        //get the invoice
        if (!$project = \App\Models\Project::Where('project_id', $id)->first()) {
            abort(404);
        }

        //clone data
        $data = [
            'project_id' => $id,
            'project_clientid' => request('project_clientid'),
            'project_title' => request('project_title'),
            'project_date_start' => request('project_date_start'),
            'project_date_due' => request('project_date_due'),
            'tags' => request('tags'),
            'project_categoryid' => request('project_categoryid'),
            'copy_milestones' => request('copy_milestones'),
            'copy_tasks' => request('copy_tasks'),
            'copy_tasks_files' => request('copy_tasks_files'),
            'copy_tasks_checklist' => request('copy_tasks_checklist'),
            'copy_invoices' => request('copy_invoices'),
            'copy_estimates' => request('copy_estimates'),
            'copy_files' => request('copy_files'),
            'return' => 'object',
        ];

        //clone invoice
        if (!$project = $clonerepo->clone($data)) {
            abort(409, __('lang.cloning_failed'));
        }

        /** ----------------------------------------------
         * record event [project created]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'new_project',
            'event_item_id' => '',
            'event_item_lang' => 'event_created_project',
            'event_item_content' => $project->project_title,
            'event_item_content2' => '',
            'event_parent_type' => 'project',
            'event_parent_id' => $project->project_id,
            'event_parent_title' => $project->project_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'event_clientid' => $project->project_clientid,
            'eventresource_type' => 'project',
            'eventresource_id' => $project->project_id,
            'event_notification_category' => 'notifications_projects_activity',
        ];
        //record event
        $event_id = $this->eventrepo->create($data);

        //payload
        $payload = [
            'project_id' => $project->project_id,
        ];

        //show the form
        return new StoreCloneResponse($payload);
    }

    /**
     * prefill the project using the project template data
     * @return \Illuminate\Http\Response
     */
    public function prefillProject() {

        //get the template
        $template = \App\Models\Project::Where('project_id', request('id'))->first();

        //custom fiels
        $fields = $this->getCustomFields($template);

        //reponse payload
        $payload = [
            'template' => $template,
            'fields' => $fields,
        ];

        //if we are just resettings
        if (request('action') == 'reset') {
            $payload['fields'] = $this->getCustomFields();
        }

        //show the form
        return new PrefillProjectResponse($payload);
    }

    /**
     * show form to update progress
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeProgress($id) {

        //check if file exists in the database
        $project = \App\Models\Project::Where('project_id', $id)->first();

        //reponse payload
        $payload = [
            'type' => 'show',
            'project' => $project,
        ];

        //show the form
        return new UpdateProgressResponse($payload);

    }

    /**
     * show form to update progress
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeProgressUpdate($id) {

        //check if file exists in the database
        $project = \App\Models\Project::Where('project_id', $id)->first();

        //reset existing account owner
        \App\Models\Project::where('project_id', $id)
            ->update([
                'project_progress' => request('project_progress'),
                'project_progress_manually' => (request('project_progress_manually') == 'on') ? 'yes' : 'no',
            ]);

        //get refreshed
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        //apply permissions
        $this->applyPermissions($project);

        //reponse payload
        $payload = [
            'type' => 'update',
            'project' => $project,
            'projects' => $projects,
        ];

        //show the form
        return new UpdateProgressResponse($payload);

    }

    /**
     * show the form to change the cover image
     *
     * @return \Illuminate\Http\Response
     */
    public function changeCoverImage($id) {

        //check if file exists in the database
        $project = \App\Models\Project::Where('project_id', $id)->first();

        //page
        $html = view('pages/projects/views/cards/modals/update-cover', compact('project'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXUUpdateConverImage',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * save new image
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeCoverImageUpdate(FileRepository $filerepo, $id) {

        //upload data
        $data = [
            'directory' => request('cover_directory'),
            'filename' => request('cover_filename'),
        ];

        //process and save to db
        if (!$filerepo->processUpload($data)) {
            abort(409);
        }

        //reset existing account owner
        \App\Models\Project::where('project_id', $id)
            ->update([
                'project_cover_directory' => request('cover_directory'),
                'project_cover_filename' => request('cover_filename'),
            ]);

        //get refreshed
        $projects = $this->projectrepo->search($id, ['apply_filters' => false]);
        $project = $projects->first();

        //apply permissions
        $this->applyPermissions($project);

        //update card
        $html = view('pages/projects/views/cards/layout/ajax', compact('projects'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#project_" . $project->project_id,
            'action' => 'replace-with',
            'value' => $html);

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

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

        //common settings
        $page = [
            'crumbs' => [
                __('lang.projects'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'projects',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_projects' => 'active',
            'submenu_projects' => 'active',
            'submenu_projects_category_' . request('filter_category') => 'active',
            'sidepanel_id' => 'sidepanel-filter-projects',
            'dynamic_search_url' => url('projects/search?action=search&projectresource_id=' . request('projectresource_id') . '&projectresource_type=' . request('projectresource_type') . '&filter_category=' . request('filter_category')),
            'add_button_classes' => 'add-edit-project-button',
            'load_more_button_route' => 'projects',
            'source' => 'list',
        ];

        //show category
        if (request()->filled('filter_category')) {
            if ($category = \App\Models\Category::Where('category_type', 'project')->where('category_id', request('filter_category'))->first()) {
                $page['crumbs'] = [
                    __('lang.projects'),
                    $category->category_name,
                ];
            }
        }

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_project'),
            'add_modal_create_url' => url('projects/create?projectresource_id=' . request('projectresource_id') . '&projectresource_type=' . request('projectresource_type') . '&filter_category=' . request('filter_category')),
            'add_modal_action_url' => url('projects?projectresource_id=' . request('projectresource_id') . '&projectresource_type=' . request('projectresource_type') . '&filter_category=' . request('filter_category')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //projects list page
        if ($section == 'projects') {
            $page += [
                'meta_title' => __('lang.projects'),
                'heading' => __('lang.projects'),
                'sidepanel_id' => 'sidepanel-filter-projects',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //project page
        if ($section == 'project') {
            //adjust
            $page['page'] = 'project';

            //crumbs
            $page['crumbs'] = [
                __('lang.project'),
                '#' . $data->project_id,
            ];

            //add
            $page += [
                'crumbs_special_class' => 'main-pages-crumbs',
                'meta_title' => __('lang.projects') . ' - ' . $data->project_title,
                'heading' => $data->project_title,
                'project_id' => request()->segment(2),
                'source_for_filter_panels' => 'ext',
                'section' => 'overview',
            ];
            //ajax loading and tabs
            return $page;
        }

        //ext page settings
        if ($section == 'ext') {
            $page += [
                'list_page_actions_size' => 'col-lg-12',

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

        //get expense (all rows - for stats etc)
        $count_all = $this->projectrepo->search('', ['stats' => 'count-all']);
        $count_in_progress = $this->projectrepo->search('', ['stats' => 'count-in-progress']);
        $count_on_hold = $this->projectrepo->search('', ['stats' => 'count-on-hold']);
        $count_completed = $this->projectrepo->search('', ['stats' => 'count-completed']);

        //default values
        $stats = [
            [
                'value' => $count_all,
                'title' => __('lang.all'),
                'percentage' => '100%',
                'color' => 'bg-info',
            ],
            [
                'value' => $count_in_progress,
                'title' => __('lang.in_progress'),
                'percentage' => '100%',
                'color' => 'bg-primary',
            ],
            [
                'value' => $count_on_hold,
                'title' => __('lang.on_hold'),
                'percentage' => '100%',
                'color' => 'bg-danger',
            ],
            [
                'value' => $count_completed,
                'title' => __('lang.completed'),
                'percentage' => '100%',
                'color' => 'bg-success',
            ],
        ];

        //return
        return $stats;
    }

}