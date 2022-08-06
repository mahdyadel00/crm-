<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for tasks
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\TaskRecurrringSettings;
use App\Http\Requests\Tasks\TaskStoreUpdate;
use App\Http\Responses\Tasks\ActivateResponse;
use App\Http\Responses\Tasks\ArchiveResponse;
use App\Http\Responses\Tasks\AttachFilesResponse;
use App\Http\Responses\Tasks\ChecklistResponse;
use App\Http\Responses\Tasks\CloneResponse;
use App\Http\Responses\Tasks\CloneStoreResponse;
use App\Http\Responses\Tasks\contentResponse;
use App\Http\Responses\Tasks\CreateResponse;
use App\Http\Responses\Tasks\DestroyResponse;
use App\Http\Responses\Tasks\IndexKanbanResponse;
use App\Http\Responses\Tasks\IndexListResponse;
use App\Http\Responses\Tasks\RecurringSettingsResponse;
use App\Http\Responses\Tasks\ShowResponse;
use App\Http\Responses\Tasks\StoreChecklistResponse;
use App\Http\Responses\Tasks\StoreCommentResponse;
use App\Http\Responses\Tasks\StoreResponse;
use App\Http\Responses\Tasks\TimerStartResponse;
use App\Http\Responses\Tasks\TimerStopResponse;
use App\Http\Responses\Tasks\UpdateChecklistResponse;
use App\Http\Responses\Tasks\UpdateErrorResponse;
use App\Http\Responses\Tasks\UpdateResponse;
use App\Http\Responses\Tasks\UpdateStatusResponse;
use App\Http\Responses\Tasks\UpdateTagsResponse;
use App\Mail\TaskStatusChanged;
use App\Models\Checklist;
use App\Models\Comment;
use App\Models\Task;
use App\Models\Timer;
use App\Permissions\AttachmentPermissions;
use App\Permissions\ChecklistPermissions;
use App\Permissions\CommentPermissions;
use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use App\Repositories\AttachmentRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ChecklistRepository;
use App\Repositories\CommentRepository;
use App\Repositories\CustomFieldsRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\ProjectAssignedRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\TaskAssignedRepository;
use App\Repositories\TaskRepository;
use App\Repositories\TaskStatusRepository;
use App\Repositories\TimerRepository;
use App\Repositories\UserRepository;
use App\Rules\CheckBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Intervention\Image\Exception\NotReadableException;
use Validator;

class Tasks extends Controller {

    /**
     * The task repository instance.
     */
    protected $taskrepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The timer repository instance.
     */
    protected $timerrepo;

    /**
     * The task model instance.
     */
    protected $taskmodel;

    /**
     * The comment permission instance.
     */
    protected $commentpermissions;

    /**
     * The attachment permission instance.
     */
    protected $attachmentpermissions;

    /**
     * The checklist permission instance.
     */
    protected $checklistpermissions;

    /**
     * The task permission instance.
     */
    protected $taskpermissions;

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

    protected $projectpermission;

    public function __construct(
        TaskRepository $taskrepo,
        TagRepository $tagrepo,
        UserRepository $userrepo,
        TimerRepository $timerrepo,
        TaskPermissions $taskpermissions,
        CommentPermissions $commentpermissions,
        AttachmentPermissions $attachmentpermissions,
        ChecklistPermissions $checklistpermissions,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        Task $taskmodel,
        CustomFieldsRepository $customrepo,
        TaskStatusRepository $statusrepo,
        ProjectPermissions $projectpermission
    ) {

        //core controller instantation
        parent::__construct();

        $this->taskrepo = $taskrepo;
        $this->tagrepo = $tagrepo;
        $this->userrepo = $userrepo;
        $this->taskpermissions = $taskpermissions;
        $this->taskmodel = $taskmodel;
        $this->commentpermissions = $commentpermissions;
        $this->attachmentpermissions = $attachmentpermissions;
        $this->checklistpermissions = $checklistpermissions;
        $this->timerrepo = $timerrepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
        $this->customrepo = $customrepo;
        $this->projectpermission = $projectpermission;

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('tasksMiddlewareTimer')->only([
            'timerStart',
            'timerStop',
            'timerStopAll',
        ]);

        //Permissions on methods
        $this->middleware('tasksMiddlewareIndex')->only([
            'index',
            'update',
            'toggleStatus',
            'store',
            'updateStartDate',
            'updateDueDate',
            'updateStatus',
            'updatePriority',
            'updateVisibility',
            'updateMilestone',
            'updateAssigned',
            'timerStart',
            'timerStop',
            'timerStopAll',
            'archive',
            'activate',
            'cloneStore',
            'recurringSettingsUpdate',
            'stopRecurring',
        ]);

        $this->middleware('tasksMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('tasksMiddlewareShow')->only([
            'show',
            'showCustomFields',
            'updateCustomFields',
            'showMyNotes',
            'createMyNotes',
            'editMyNotes',
            'deleteMyNotes',
        ]);

        $this->middleware('tasksMiddlewareEdit')->only([
            'updateDescription',
            'updateTitle',
            'updateStartDate',
            'updateDueDate',
            'updateVisibility',
            'updateMilestone',
            'updateAssigned',
            'storeChecklist',
            'archive',
            'activate',
            'editCustomFields',
            'updateCustomFields',
            'updateTags',
            'recurringSettings',
            'recurringSettingsUpdate',
        ]);

        $this->middleware('tasksMiddlewareParticipate')->only([
            'storeComment',
            'attachFiles',
            'updateStatus',
            'updatePriority',
        ]);

        $this->middleware('tasksMiddlewareDeleteAttachment')->only([
            'deleteAttachment',
        ]);

        $this->middleware('tasksMiddlewareDownloadAttachment')->only([
            'downloadAttachment',
        ]);

        $this->middleware('tasksMiddlewareDeleteComment')->only([
            'deleteComment',
        ]);

        $this->middleware('tasksMiddlewareEditDeleteChecklist')->only([
            'updateChecklist',
            'deleteChecklist',
            'toggleChecklistStatus',
        ]);

        $this->middleware('tasksMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->middleware('tasksMiddlewareAssign')->only([
            'updateAssigned',
        ]);

        $this->middleware('tasksMiddlewareCloning')->only([
            'cloneTask',
            'cloneStore',
        ]);
    }

    /**
     * Display a listing of tasks
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (auth()->user()->pref_view_tasks_layout == 'list') {
            $payload = $this->indexList();
            return new IndexListResponse($payload);
        } else {
            $payload = $this->indexKanban();
            return new IndexKanbanResponse($payload);
        }
    }

    /**
     * Display a listing of tasks
     * @return \Illuminate\Http\Response
     */
    public function indexList() {

        //defaults
        $milestones = [];

        //get stats before other filters has been applied
        $stats = $this->statsWidget();

        //get tasks
        $tasks = $this->taskrepo->search();

        //count rows
        $count = $tasks->total();

        //process for timers
        $this->processTasks($tasks);

        //apply some permissions
        if ($tasks) {
            foreach ($tasks as $task) {
                $this->applyPermissions($task);
            }
        }

        //basic page settings
        $page = $this->pageSettings('tasks', ['count' => $count]);

        //page setting for embedded view
        if (request('source') == 'ext') {
            $page = $this->pageSettings('ext', ['count' => $count]);
        }

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('task');

        //all available lead statuses
        $statuses = \App\Models\TaskStatus::all();

        //get all milestones if viewing from project page (for use in filter panel)
        if (request()->filled('taskresource_id') && request('taskresource_type') == 'project') {
            $milestones = \App\Models\Milestone::Where('milestone_projectid', request('taskresource_id'))->get();
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'milestones' => $milestones,
            'tasks' => $tasks,
            'stats' => $stats,
            'tags' => $tags,
            'statuses' => $statuses,
        ];

        //show the view
        return $payload;
    }

    /**
     * Display a listing of tasks
     * @return \Illuminate\Http\Response
     */
    public function indexKanban() {

        //defaults
        $milestones = [];

        //get stats before other filters has been applied
        $stats = $this->statsWidget();

        $boards = $this->taskBoards();

        //basic page settings
        $page = $this->pageSettings('tasks', []);

        //page setting for embedded view
        if (request('source') == 'ext') {
            $page = $this->pageSettings('ext', []);
        }

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('task');

        //all available lead statuses
        $statuses = \App\Models\TaskStatus::all();

        //get all milestones if viewing from project page (for use in filter panel)
        if (request()->filled('taskresource_id') && request('taskresource_type') == 'project') {
            $milestones = \App\Models\Milestone::Where('milestone_projectid', request('taskresource_id'))->get();
        }

        //check if the user has participation rights on the task
        if (auth()->user()->is_client) {
            if (request()->filled('taskresource_id') && request('taskresource_type') == 'project') {
                if ($this->projectpermission->check('tasks-participate', request('taskresource_id'))) {
                    config(['visibility.tasks_participate' => true]);
                }
            }
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'boards' => $boards,
            'milestones' => $milestones,
            'stats' => $stats,
            'tags' => $tags,
            'statuses' => $statuses,
        ];

        //show the view
        return $payload;
    }

    /**
     * process/group tasks into boards
     * @return object
     */
    private function taskBoards() {

        $statuses = \App\Models\TaskStatus::orderBy('taskstatus_position', 'asc')->get();

        foreach ($statuses as $status) {
            request()->merge([
                'filter_single_task_status' => $status->taskstatus_id,
                'query_type' => 'kanban',
            ]);

            //get tasks
            $tasks = $this->taskrepo->search();

            //count rows
            $count = $tasks->total();

            //process for timers
            $this->processTasks($tasks);

            //apply some permissions
            if ($tasks) {
                foreach ($tasks as $task) {
                    $this->applyPermissions($task);
                }
            }

            //apply custom fields
            if ($tasks) {
                foreach ($tasks as $task) {
                    $task->fields = $this->getCustomFields($task);
                }
            }

            //initial loadmore button
            if ($tasks->currentPage() < $tasks->lastPage()) {
                $boards[$status->taskstatus_id]['load_more'] = '';
                $boards[$status->taskstatus_id]['load_more_url'] = loadMoreButtonUrl($tasks->currentPage() + 1, $status->taskstatus_id);
            } else {
                $boards[$status->taskstatus_id]['load_more'] = 'hidden';
                $boards[$status->taskstatus_id]['load_more_url'] = '';
            }

            $boards[$status->taskstatus_id]['name'] = $status->taskstatus_title;
            $boards[$status->taskstatus_id]['id'] = $status->taskstatus_id;
            $boards[$status->taskstatus_id]['tasks'] = $tasks;
            $boards[$status->taskstatus_id]['color'] = $status->taskstatus_color;

        }

        return $boards;
    }

    /**
     * Show the form for creating a new task
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //default
        $milestones = [];

        //page settings
        $page = $this->pageSettings('create');

        //get tags
        $tags = $this->tagrepo->getByType('task');

        $statuses = \App\Models\TaskStatus::orderBy('taskstatus_position', 'asc')->get();

        //milestones
        if (request()->filled('taskresource_id') && request('taskresource_type') == 'project') {
            $milestones = \App\Models\Milestone::Where('milestone_projectid', request('taskresource_id'))->get();
        }

        //get customfields
        request()->merge([
            'filter_show_standard_form_status' => 'enabled',
            'filter_field_status' => 'enabled',
            'sort_by' => 'customfields_position',
        ]);
        $fields = $this->getCustomFields();

        //reponse payload
        $payload = [
            'page' => $page,
            'tags' => $tags,
            'milestones' => $milestones,
            'stats' => $this->statsWidget(),
            'fields' => $fields,
            'statuses' => $statuses,
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
            'customfields_type' => 'tasks',
        ]);

        //show all fields
        config(['settings.custom_fields_display_limit' => 1000]);

        //get fields
        $fields = $this->customrepo->search();

        //when in editing view - get current value that is stored for this custom field
        if ($obj instanceof \App\Models\Task) {
            foreach ($fields as $field) {
                $field->current_value = $obj[$field->customfields_name];
            }
        }

        return $fields;
    }

    /**
     * Store a newly created task in storage.
     * @param object TaskStoreUpdate instance of the request validation object
     * @param object TaskAssignedRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(TaskStoreUpdate $request, TaskAssignedRepository $assignedrepo) {

        //defaults
        $assigned_users = [];

        //get client id of attached project (if this is a project task)
        $project = \App\Models\Project::find(request('task_projectid'));
        $client_id = $project->project_clientid;

        //custom field validation
        if ($messages = $this->customFieldValidationFailed()) {
            abort(409, $messages);
        }

        request()->merge([
            'task_clientid' => $project->project_clientid,
        ]);

        //validate milestone id
        if (request()->filled('task_milestoneid')) {
            if (!\App\Models\Milestone::where('milestone_id', request('task_milestoneid'))
                ->where('milestone_projectid', request('task_projectid'))->first()) {
                abort(409, __('lang.item_not_found'));
            }
        }

        //no milestone provided - get default milestone
        if (!request()->filled('task_milestoneid')) {
            if ($milestone = \App\Models\Milestone::where('milestone_type', 'uncategorised')
                ->where('milestone_projectid', request('task_projectid'))->first()) {
                request()->merge([
                    'task_milestoneid' => $milestone->milestone_id,
                ]);
            } else {
                abort(409, __('lang.milestone_not_found'));
                Log::critical("add task - default milestone could not be found", ['process' => '[tasks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => request('task_projectid')]);
            }
        }

        //get the last row (order by position - desc)
        if ($last = $this->taskmodel::orderBy('task_position', 'desc')->first()) {
            $position = $last->task_position + config('settings.db_position_increment');
        } else {
            //default position increment
            $position = config('settings.db_position_increment');
        }

        //create new record
        if (!$task_id = $this->taskrepo->create($position)) {
            abort(409);
        }

        //add tags
        $this->tagrepo->add('task', $task_id);

        /**
         * [client added task]
         *     - task will remain un-assigned
         * [team added task - with no assigning permission]
         *     - assigned to the user adding the task
         * [team added task - with assigning permission]
         *     - assign as per posted list (or none)
         * */
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_assign_tasks == 'no') {
                $assigned_users = $assignedrepo->add($task_id, auth()->id());
            } else {
                $assigned_users = $assignedrepo->add($task_id, '');
            }
        }

        //get the task object (friendly for rendering in blade template)
        $tasks = $this->taskrepo->search($task_id, ['apply_filters' => false]);
        $task = $tasks->first();

        //process task (timers)
        $this->processTask($task);

        //apply permissions
        $this->applyPermissions($task);

        //custom fields
        $task->fields = $this->getCustomFields($task);

        /** ----------------------------------------------
         * record assignment events and send emails
         * ----------------------------------------------*/
        foreach ($assigned_users as $assigned_user_id) {
            if ($assigned_user = \App\Models\User::Where('id', $assigned_user_id)->first()) {

                $data = [
                    'event_creatorid' => auth()->id(),
                    'event_item' => 'assigned',
                    'event_item_id' => '',
                    'event_item_lang' => 'event_assigned_user_to_a_task',
                    'event_item_lang_alt' => 'event_assigned_user_to_a_task_alt',
                    'event_item_content' => __('lang.assigned'),
                    'event_item_content2' => $assigned_user_id,
                    'event_item_content3' => $assigned_user->first_name,
                    'event_parent_type' => 'task',
                    'event_parent_id' => $task->task_id,
                    'event_parent_title' => $task->task_title,
                    'event_show_item' => 'yes',
                    'event_show_in_timeline' => 'yes',
                    'event_clientid' => $task->task_clientid,
                    'eventresource_type' => 'project',
                    'eventresource_id' => $task->task_projectid,
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
                        $mail = new \App\Mail\TaskAssignment($assigned_user, $data, $task);
                        $mail->build();
                    }
                }
            }
        }

        //counting rows
        $rows = $this->taskrepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'task' => $task,
            'count' => $count,
            'stats' => $this->statsWidget(),
        ];

        //card view response
        if (auth()->user()->pref_view_tasks_layout == 'kanban') {
            request()->merge([
                'filter_task_status' => request('task_status'),
            ]);
            if (request()->filled('taskresource_id')) {
                request()->merge([
                    'filter_task_projectid' => request('task_projectid'),
                ]);
            }
            //counting rows
            $rows = $this->taskrepo->search();
            //payload
            $board['tasks'] = $tasks;
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
        $fields = \App\Models\CustomField::Where('customfields_type', 'tasks')->get();
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
     * Display the specified task
     * @param object TaskAssignedRepository instance of the repository
     * @param object ProjectAssignedRepository instance of the repository
     * @param object CommentRepository instance of the repository
     * @param object AttachmentRepository instance of the repository
     * @param object ChecklistRepository instance of the repository
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function show(
        TaskAssignedRepository $assignedrepo,
        ProjectAssignedRepository $projectassignedrepo,
        CommentRepository $commentrepo,
        AttachmentRepository $attachmentrepo,
        ChecklistRepository $checklistrepo, $id) {

        //get the task
        $tasks = $this->taskrepo->search($id);

        //task
        $task = $tasks->first();

        //apply permissions
        $this->applyPermissions($task);

        //process task
        $this->processTask($task);

        //get tags
        $tags_resource = $this->tagrepo->getByResource('task', $id);
        $tags_system = $this->tagrepo->getByType('task');
        $tags = $tags_resource->merge($tags_system);
        $tags = $tags->unique('tag_title');

        //get assigned users
        $assigned = $assignedrepo->getAssigned($id);

        //get team members who are assigned to this tasks project
        $project_assigned = $projectassignedrepo->getAssigned($task->task_projectid);

        //get clients users
        $client_users = \App\Models\User::Where('clientid', $task->task_clientid)->orderBy('first_name', 'asc')->get();

        //comments
        request()->merge([
            'commentresource_type' => 'task',
            'commentresource_id' => $id,
        ]);
        $comments = $commentrepo->search();
        foreach ($comments as $comment) {
            $this->applyCommentPermissions($comment);
        }

        //attachments
        request()->merge([
            'attachmentresource_type' => 'task',
            'attachmentresource_id' => $id,
        ]);
        $attachments = $attachmentrepo->search();
        foreach ($attachments as $attachment) {
            $this->applyAttachmentPermissions($attachment);
        }

        //checklists
        request()->merge([
            'checklistresource_type' => 'task',
            'checklistresource_id' => $id,
        ]);
        $checklists = $checklistrepo->search();
        foreach ($checklists as $checklist) {
            $this->applyChecklistPermissions($checklist);
        }

        //milestone
        $milestones = \App\Models\Milestone::Where('milestone_projectid', $task->task_projectid)->get();

        //page settings
        $page = $this->pageSettings('task', $task);

        //mark events as read
        \App\Models\EventTracking::where('parent_id', $id)
            ->where('parent_type', 'task')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //get users reminders
        if ($reminder = \App\Models\Reminder::Where('reminderresource_type', 'task')
            ->Where('reminderresource_id', $id)
            ->Where('reminder_userid', auth()->id())->first()) {
            $has_reminder = true;
        } else {
            $reminder = [];
            $has_reminder = false;
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'task' => $task,
            'id' => $id,
            'tags' => $tags,
            'current_tags' => $task->tags,
            'assigned' => $assigned,
            'project_assigned' => $project_assigned,
            'comments' => $comments,
            'attachments' => $attachments,
            'checklists' => $checklists,
            'milestones' => $milestones,
            'reminder' => $reminder,
            'resource_type' => 'task',
            'resource_id' => $id,
            'has_reminder' => $has_reminder,
            'progress' => $this->checklistProgress($checklists),
            'client_users' => $client_users,
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
     * Update the specified task in storage.
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //reponse payload
        $payload = [
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified task from storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo) {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {

            //only checked items
            if ($value == 'on') {

                //delete the task and associated items
                $destroyrepo->destroyTask($id);

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
     * Start a users timer for a given task
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function timerStart($id) {

        $action = 'start';

        //get the task and apply permissions
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();
        $this->applyPermissions($task);

        //stop running timer for this user
        $this->timerrepo->stopRunningTimers([
            'timer_creatorid' => auth()->id(),
        ]);

        //create a new timer for this user
        if (!$this->timerrepo->createTimer($task)) {
            $action = 'failed';
        }

        //needed by the topnav timer dropdown
        request()->merge([
            'users_running_timer_task_id' => $task->task_id,
            'users_running_timer_title' => $task->task_title,
            'users_running_timer_task_title' => str_slug($task->task_title),
        ]);

        $payload = [
            'task' => $task,
        ];

        //process reponse
        return new TimerStartResponse($payload);
    }

    /**
     * Start a users timer for a given task
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function timerStartTopnav() {

        //get the task and apply permissions
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();
        $this->applyPermissions($task);

        //stop running timer for this user
        $this->timerrepo->stopRunningTimers([
            'timer_creatorid' => auth()->id(),
        ]);

        //create a new timer for this user
        if (!$this->timerrepo->createTimer($task)) {

        }

        $payload = [];

        //process reponse
        return new TimerStartResponse($payload);
    }

    /**
     * Stop a users timer for a given task
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function timerStop($id) {

        //get the task and apply permissions
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();
        $this->applyPermissions($task);

        //stop running timer for this user
        $this->timerrepo->stopRunningTimers([
            'timer_creatorid' => auth()->id(),
        ]);

        $payload = [
            'task_id' => $id,
        ];

        //process reponse
        return new TimerStopResponse($payload);
    }

    /**
     * Stop a users timer for a given task
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function timerStopUser() {

        //stop running timer for this user
        $this->timerrepo->stopRunningTimers([
            'timer_creatorid' => auth()->id(),
        ]);

        //process reponse
        return new TimerStopResponse([]);
    }

    /**
     * Stop a users timer for a given task
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function timerStopAll($id) {

        //get the task and apply permissions
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();
        $this->applyPermissions($task);

        //stop all running timers for this task
        $this->timerrepo->stopRunningTimers([
            'task_id' => $id,
        ]);

        $payload = [
            'task_id' => $id,
        ];

        //process reponse
        return new TimerStopResponse($payload);
    }

    /**
     * send each task for processing
     * @return null
     */
    private function processTasks($tasks = '') {
        //sanity - make sure this is a valid tasks object
        if ($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            foreach ($tasks as $task) {
                $this->processTask($task);
            }
        }
    }

    /**
     * check the task for the following:
     *    1. Check if task is assigned to me - add 'assigned_to_me' (yes/no) attribute
     *    2. check if there are any running timers on the tasks - add 'running_timer' (yes/no)
     * @param object task instance of the task model object
     * @return object
     */
    private function processTask($task = '') {

        //sanity - make sure this is a valid task object
        if ($task instanceof \App\Models\Task) {

            //default values
            $task->assigned_to_me = false;
            $task->running_timers = false;
            $task->timer_current_status = false;
            $task->has_attachments = false;
            $task->has_comments = false;
            $task->has_checklist = false;

            //check if the task is assigned to me
            foreach ($task->assigned as $user) {
                if ($user->id == auth()->id()) {
                    //its assigned to me
                    $task->assigned_to_me = true;
                }
            }

            $task->has_attachments = ($task->attachments_count > 0) ? true : false;
            $task->has_comments = ($task->comments_count > 0) ? true : false;
            $task->has_checklist = ($task->checklists_count > 0) ? true : false;

            //check if there are any running timers
            foreach ($task->timers as $timer) {
                if ($timer->timer_status == 'running') {
                    //its has a running timer
                    $task->running_timers = true;
                    if ($timer->timer_creatorid == auth()->id()) {
                        $task->timer_current_status = true;
                    }
                }
            }

            //get users current/refreshed time for the task (if applcable)
            $task->my_time = $this->timerrepo->sumTimers($task->task_id, auth()->id());

            //custom fields
            $task->fields = $this->getCustomFields($task);
        }
    }

    /**
     * update task description
     * @param int $id task id
     * @return object
     */
    public function updateDescription($id) {

        $task = $this->taskmodel::find($id);
        $task->task_description = request('task_description');
        $task->save();

        //update card description
        $jsondata['dom_html'][] = [
            'selector' => '#card-description-container',
            'action' => 'replace',
            'value' => clean($task->task_description),
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#card-description-container',
            'action' => 'show',
        ];
        return response()->json($jsondata);
    }

    /**
     * update resource
     * @param int $id task id
     * @return null
     */
    public function updateStartDate($id) {

        //get the task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //save task_date_due to request so can access it n validation
        request()->merge(['task_date_due' => $task->task_date_due]);

        //validate
        $validator = Validator::make(request()->all(), [
            'task_date_start' => [
                'bail',
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (request('task_date_due') != '') {
                        if (strtotime($value) > strtotime(request('task_date_due'))) {
                            return $fail(__('lang.start_date_must_be_before_due_date'));
                        }
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
                'reset_target' => '#task-start-date-container',
                'reset_value' => runtimeDate($task->task_date_start),
                'error_message' => $messages,
            ]);
        }

        //update
        $task->task_date_start = request('task_date_start');
        $task->save();

        //update and apply permissions
        $this->processTask($task);
        $this->applyPermissions($task);

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update resource
     * @param int $id task id
     * @return null
     */
    public function updateDueDate($id) {

        //get the task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //save task_date_start to request so can access it in validation
        request()->merge(['task_date_start' => $task->task_date_start]);

        //validate
        $validator = Validator::make(request()->all(), [
            'task_date_due' => [
                'bail',
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (request('task_date_due') != '') {
                        if (strtotime($value) < strtotime(request('task_date_start'))) {
                            return $fail(__('lang.due_date_must_be_after_start_date'));
                        }
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
                'reset_target' => '#task-due-date-container',
                'reset_value' => runtimeDate($task->task_date_due),
                'error_message' => $messages,
            ]);
        }

        //update
        $task->task_date_due = request('task_date_due');
        $task->save();

        //process and apply permissions
        $this->processTask($task);
        $this->applyPermissions($task);

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update task status
     * @param object ProjectPermissions instance of the repository
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(ProjectPermissions $projectpermissions, $id) {

        //get the task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //old status
        $old_status = $task->task_status;

        //validate
        $validator = Validator::make(request()->all(), [
            'task_status' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\TaskStatus::Where('taskstatus_id', $value)->doesntExist()) {
                        return $fail(__('lang.invalid_status'));
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
                'reset_target' => '#card-task-status-text',
                'reset_value' => safestr(request('current_task_status_text')),
                'error_message' => $messages,
            ]);
        }

        //we are moving task to a new board - update its position to top of the new list
        if ($old_status != request('task_status')) {
            if ($first_task = \App\Models\Task::Where('task_status', request('task_status'))->orderBy('task_position', 'ASC')->first()) {
                $task->task_position = $first_task->task_position / 2;
            }
        }

        //update
        $task->task_status = request('task_status');
        $task->save();

        //get refreshed
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //process and apply permissions
        $this->processTask($task);
        $this->applyPermissions($task);

        /** ----------------------------------------------
         * record event [status]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'status',
            'event_item_id' => '',
            'event_item_lang' => 'event_changed_task_status',
            'event_item_content' => $task->task_status,
            'event_item_content2' => '',
            'event_parent_type' => 'task',
            'event_parent_id' => $task->task_id,
            'event_parent_title' => $task->task_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'no',
            'event_clientid' => $task->task_clientid,
            'eventresource_type' => 'project',
            'eventresource_id' => $task->task_projectid,
            'event_notification_category' => 'notifications_tasks_activity',
        ];
        //record event
        if ($old_status != request('task_status')) {
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $projectpermissions->check('users', $task);
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
                    $mail = new \App\Mail\TaskStatusChanged($user, $data, $task);
                    $mail->build();
                }
            }
        }

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'stats' => $this->statsWidget(),
            'old_status' => $old_status,
            'new_status' => request('task_status'),
            'display_status' => runtimeLang(request('task_status')),
        ];

        //process reponse
        return new UpdateStatusResponse($payload);
    }

    /**
     * update task priority
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function updatePriority($id) {

        //get the task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'task_priority' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!array_key_exists($value, config('settings.task_priority'))) {
                        return $fail(__('lang.invalid_priority'));
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
                'reset_target' => '#card-task-priority-text',
                'reset_value' => safestr(request('current_task_priority_text')),
                'error_message' => $messages,
            ]);
        }

        //save
        $task->task_priority = request('task_priority');
        $task->save();

        //process and permissions
        $this->processTask($task);
        $this->applyPermissions($task);

        //reponse payload
        $payload = [
            'type' => 'update-priority',
            'tasks' => $tasks,
            'stats' => $this->statsWidget(),
            'display_priority' => runtimeLang(request('task_priority')),

        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update task visibility
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function updateVisibility($id) {

        //get the task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //validate
        $validator = Validator::make(request()->all(), [
            'task_client_visibility' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['yes', 'no'])) {
                        return $fail(__('lang.client_visibility_invalid'));
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
                'error_message' => $messages,
            ]);
        }

        //validate
        $task->task_client_visibility = request('task_client_visibility');
        $task->save();

        //process and apply permissions
        $this->processTask($task);
        $this->applyPermissions($task);

        //reponse payload
        $payload = [
            'type' => 'update-vivibility',
            'tasks' => $tasks,
            'stats' => $this->statsWidget(),
            'display_text' => ($task->task_client_visibility == 'yes') ? __('lang.visible') : __('lang.hidden'),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update task milestone
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function updateMilestone($id) {

        //get the task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //validate
        if (!\App\Models\Milestone::Where('milestone_id', request('task_milestoneid'))->where('milestone_projectid', $task->task_projectid)->exists()) {
            //show error and reset values
            $payload = [
                'reset_target' => '',
                'reset_value' => '',
                'error_message' => __('lang.invalid_or_missing_data'),
            ];
            //process reponse
            return new UpdateErrorResponse($payload);
        }

        //validate
        $task->task_milestoneid = request('task_milestoneid');
        $task->save();

        //get refreshed
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //process
        $this->processTask($task);
        $this->applyPermissions($task);

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update task title
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function updateTitle($id) {

        //get the task
        $task = $this->taskmodel::find($id);

        //validation
        if (hasHTML(request('task_title'))) {
            //[type options] error|success
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => __('lang.title') . ' ' . __('lang.must_not_contain_any_html'),
            ];

            //update back the title
            $jsondata['dom_html'][] = [
                'selector' => '#card-title-editable',
                'action' => 'replace',
                'value' => safestr($task->task_title),
            ];
            return response()->json($jsondata);
        }

        //validation
        if (!request()->filled('task_title')) {

            //[type options] error|success
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => __('lang.title_is_required'),
            ];

            //update back the title
            $jsondata['dom_html'][] = [
                'selector' => '#card-title-editable',
                'action' => 'replace',
                'value' => safestr($task->task_title),
            ];

            return response()->json($jsondata);

        } else {
            $task->task_title = request('task_title');
            $task->save();

            //get refreshed & reprocess
            $tasks = $this->taskrepo->search($id);
            $this->processTask($tasks->first());

            //update table row
            $jsondata['dom_html'][] = [
                'selector' => "#table_task_title_$id",
                'action' => 'replace',
                'value' => str_limit(safestr($task->task_title), 25),
            ];
            //update kanban card title
            $jsondata['dom_html'][] = [
                'selector' => "#kanban_task_title_$id",
                'action' => 'replace',
                'value' => str_limit(safestr($task->task_title), 45),
            ];
            //update card
            $jsondata['dom_html'][] = [
                'selector' => '#card-title-editable',
                'action' => 'replace',
                'value' => safestr($task->task_title),
            ];

            return response()->json($jsondata);
        }
    }

    /**
     * update task assigned users
     * @param object TaskAssignedRepository instance of the repository
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function updateAssigned(TaskAssignedRepository $assignedrepo, $id) {

        //fix - remove own tasks filter- so that a user with "assign tasks" role can use this method
        $data = [
            'apply_filters' => false,
        ];

        //get the task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //currently assigned
        $currently_assigned = $task->assigned->pluck('id')->toArray();

        //milestone
        $milestones = \App\Models\Milestone::Where('milestone_projectid', $task->task_projectid)->get();

        //validation - data type
        if (request()->filled('assigned') && !is_array(request('assigned'))) {
            return new UpdateResponse([
                'type' => 'update-assigned',
                'tasks' => $tasks,
                'task' => $task,
                'assigned' => $assignedrepo->getAssigned($id),
                'milestones' => $milestones,
                'error' => true,
                'message' => __('lang.request_is_invalid'),
            ]);
        }

        //validate users exist
        if (request()->filled('assigned')) {
            foreach (request('assigned') as $user_id => $value) {
                if ($value == 'on') {
                    //validate user exists
                    if (\App\Models\User::Where('id', $user_id)->doesntExist()) {
                        return new UpdateResponse([
                            'type' => 'update-assigned',
                            'tasks' => $tasks,
                            'task' => $task,
                            'assigned' => $assignedrepo->getAssigned($id),
                            'milestones' => $milestones,
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
                    $assigned_users = $assignedrepo->add($id, $user_id);
                    if (!in_array($user_id, $currently_assigned)) {
                        $newly_signed_users[] = $user_id;
                    }
                }
            }
        }

        //stop timers of recently un-assigned users
        foreach ($currently_assigned as $current_user) {
            if (!in_array($current_user, $newly_signed_users)) {
                //reset existing account owner
                \App\Models\Timer::where('timer_taskid', $id)->where('timer_creatorid', $current_user)
                    ->update(['timer_status' => 'stopped']);
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
                    'event_item_lang' => 'event_assigned_user_to_a_task',
                    'event_item_lang_alt' => 'event_assigned_user_to_a_task_alt',
                    'event_item_content' => __('lang.assigned'),
                    'event_item_content2' => $assigned_user_id,
                    'event_item_content3' => $assigned_user->first_name,
                    'event_parent_type' => 'task',
                    'event_parent_id' => $task->task_id,
                    'event_parent_title' => $task->task_title,
                    'event_show_item' => 'yes',
                    'event_show_in_timeline' => 'yes',
                    'event_clientid' => $task->task_clientid,
                    'eventresource_type' => 'project',
                    'eventresource_id' => $task->task_projectid,
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
                        $mail = new \App\Mail\TaskAssignment($assigned_user, $data, $task);
                        $mail->build();
                    }
                }
            }
        }

        //get refereshed
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //process and apply permissions
        $this->processTask($task);
        $this->applyPermissions($task);

        //get assigned
        $assigned = $assignedrepo->getAssigned($id);

        //reponse payload
        $payload = [
            'type' => 'update-assigned',
            'tasks' => $tasks,
            'task' => $task,
            'assigned' => $assigned,
            'milestones' => $milestones,
        ];

        //process reponse
        return new UpdateResponse($payload);
    }

    /**
     * update task priority
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function updateTags($id) {

        //delete & update tags
        $this->tagrepo->delete('task', $id);
        $this->tagrepo->add('task', $id);

        //get tags
        $tags_resource = $this->tagrepo->getByResource('task', $id);
        $tags_system = $this->tagrepo->getByType('task');
        $tags = $tags_resource->merge($tags_system);
        $tags = $tags->unique('tag_title');

        //get refreshed task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //reponse payload
        $payload = [
            'task' => $task,
            'tags' => $tags,
            'current_tags' => $task->tags,
        ];

        //process reponse
        return new UpdateTagsResponse($payload);
    }

    /**
     * save task comment
     * @param object CommentRepository instance of the repository
     * @return \Illuminate\Http\Response
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
            'commentresource_type' => 'task',
            'commentresource_id' => $id,
            'comment_text' => request('comment_text'),
        ]);
        $comment_id = $commentrepo->create();

        //get complete comment
        $comments = $commentrepo->search($comment_id);
        $comment = $comments->first();
        $this->applyCommentPermissions($comment);

        //get task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();
        $this->processTask($task);

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
            'event_parent_type' => 'task',
            'event_parent_id' => $task->task_id,
            'event_parent_title' => $task->task_title,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'no',
            'event_clientid' => $task->task_clientid,
            'eventresource_type' => 'project',
            'eventresource_id' => $task->task_projectid,
            'event_notification_category' => 'notifications_tasks_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users
            $users = $this->taskpermissions->check('users', $task);
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
                    $mail = new \App\Mail\TaskComment($user, $data, $task);
                    $mail->build();
                }
            }
        }

        //reponse payload
        $payload = [
            'comments' => $comments,
            'tasks' => $tasks,
        ];

        //show the form
        return new StoreCommentResponse($payload);
    }

    /**
     * store checklist
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
            'checklistresource_type' => 'task',
            'checklistresource_id' => $id,
            'checklist_text' => request('checklist_text'),
        ]);

        //get next position
        if ($last = \App\Models\Checklist::Where('checklistresource_type', 'task')
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

        //get task
        $tasks = $this->taskrepo->search($id);
        $this->processTask($tasks->first());

        //reponse payload
        $payload = [
            'checklists' => $checklists,
            'progress' => $this->checklistProgress($checklistrepo->search()),
            'tasks' => $tasks,
        ];

        //show the form
        return new StoreChecklistResponse($payload);
    }

    /**
     * update a task checklist
     * @param object ChecklistRepository instance of the repository
     * @param int $id task id
     * @return \Illuminate\Http\Response
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
     * change task status using the checkbox
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus() {

        //validate the task exists
        $task = \App\Models\Task::Where('task_id', request()->route('task'))->first();

        //update the task
        if (request('toggle_task_status') == 'on') {
            $task->task_previous_status = $task->task_status;
            $task->task_status = 2;
            $task->save();
        } else {
            $task->task_status = $task->task_previous_status;
            $task->save();
        }

        //stop all running timers
        if ($task->task_status == 2) {
            $this->timerrepo->stopRunningTimers([
                'task_id' => request()->route('task'),
            ]);

        }

        //get refreshed task
        $tasks = $this->taskrepo->search(request()->route('task'));
        $task = $tasks->first();

        //apply permissions
        $this->applyPermissions($task);

        //process
        $this->processTask($task);

        //record event (task completed)
        if ($task->task_status == 2) {

            /** ----------------------------------------------
             * record event [comment]
             * see database table to details of each key
             * ----------------------------------------------*/
            $data = [
                'event_creatorid' => auth()->id(),
                'event_item' => 'task',
                'event_item_id' => $task->task_id,
                'event_item_lang' => 'event_changed_task_status_completed',
                'event_item_content' => $task->task_title,
                'event_item_content2' => '',
                'event_clientid' => $task->task_clientid,
                'event_parent_type' => 'project',
                'event_parent_id' => $task->task_projectid,
                'event_parent_title' => $task->project_title,
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'yes',
                'eventresource_type' => 'project',
                'eventresource_id' => $task->task_projectid,
                'event_notification_category' => 'notifications_tasks_activity',
            ];
            //record event
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $this->taskpermissions->check('users', $task);
                //record notification
                $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
            }

            /** ----------------------------------------------
             * send email [comment
             * ----------------------------------------------*/
            if (isset($emailusers) && is_array($emailusers)) {
                //additional data
                $data = [];
                //send to users
                if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                    foreach ($users as $user) {
                        $mail = new \App\Mail\TaskStatusChanged($user, $data, $task);
                        $mail->build();
                    }
                }
            }

        }

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'task_id' => request()->route('task'),
            'stats' => $this->statsWidget(),
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     * @param object AttachmentRepository instance of the repository
     * @param int $id task id
     */
    public function attachFiles(Request $request, AttachmentRepository $attachmentrepo, $id) {

        //validate the task exists
        $task = $this->taskmodel::find($id);

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
                'attachment_clientid' => $task->task_clientid,
                'attachment_uniqiueid' => $uniqueid,
                'attachment_directory' => $directory,
                'attachment_filename' => $filename,
                'attachment_extension' => $extension,
                'attachment_type' => $file_type,
                'attachment_size' => humanFileSize(filesize($file_path)),
                'attachment_thumbname' => $thumb_name,
                'attachmentresource_type' => 'task',
                'attachmentresource_id' => $id,
            ];
            $attachment_id = $attachmentrepo->create($data);

            //get refreshed attachment
            $attachments = $attachmentrepo->search($attachment_id);
            $attachment = $attachments->first();
            //apply permissions
            $this->applyAttachmentPermissions($attachment);

            //get task
            $tasks = $this->taskrepo->search($id);
            $task = $tasks->first();
            $this->processTask($task);

            /** ----------------------------------------------
             * record event [attachment]
             * ----------------------------------------------*/
            $data = [
                'event_creatorid' => auth()->id(),
                'event_item' => 'attachment',
                'event_item_id' => $attachment_id,
                'event_item_lang' => 'event_attached_a_file',
                'event_item_content' => $filename,
                'event_item_content2' => "tasks/download-attachment/$uniqueid",
                'event_parent_type' => 'task',
                'event_parent_id' => $task->task_id,
                'event_parent_title' => $task->task_title,
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'no',
                'event_clientid' => $task->task_clientid,
                'eventresource_type' => 'project',
                'eventresource_id' => $task->task_projectid,
                'event_notification_category' => 'notifications_tasks_activity',
            ];
            //record event
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $this->taskpermissions->check('users', $task);
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
                        $mail = new \App\Mail\TaskFileUploaded($user, $data, $task);
                        $mail->build();
                    }
                }
            }

            //reponse payload
            $payload = [
                'attachments' => $attachments,
                'tasks' => $tasks,
            ];

            //show the form
            return new AttachFilesResponse($payload);
        }
    }

    /**
     * delete task attachment
     * @param int $id task id
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
     * download task attachment
     * @param int $id task id
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
        abort(404, __('lang.file_not_found'));
    }

    /**
     * delete a task comment
     * @param object DestroyRepository instance of the repository
     * @param object Comment instance of the comment model object
     * @param int $id task id
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
     * delete checklist
     * @param object Checklist instance of the request object
     * @param object ChecklistRepository instance of the repository
     * @param int $id task id
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
            'checklistresource_type' => 'task',
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
     * @param object Checklist instance of the request validation object
     * @param object ChecklistRepository instance of the repository
     * @param int $id task id
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
            'checklistresource_type' => 'task',
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
     * @param object checklists instance of the checklists model object
     * @return object
     */
    private function checklistProgress($checklists) {

        $progress['bar'] = 'w-0'; //css width %
        $progress['completed'] = '---';

        //sanity - make sure this is a valid tasks object
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
     * apply permissions.
     * @param object $task instance of the task model object
     * @return object
     */
    private function applyPermissions($task = '') {

        //sanity - make sure this is a valid task object
        if ($task instanceof \App\Models\Task) {

            //[sql optimisation]
            $project = $task->project()->first();
            $assigned_users = $task->assigned->pluck('id');
            $assigned_project_users = $project->assigned->pluck('id');
            $project_managers = $task->projectmanagers->pluck('id');

            //project tasks
            if ($task->task_projectid > 0) {
                //edit permissions
                $task->permission_edit_task = $this->taskpermissions->check('edit', $task, $project, $assigned_users, $assigned_project_users, $project_managers);
                //delete permissions
                $task->permission_delete_task = $this->taskpermissions->check('delete', $task, $project, $assigned_users, $assigned_project_users, $project_managers);
                //delete participate
                $task->permission_participate = $this->taskpermissions->check('participate', $task, $project, $assigned_users, $assigned_project_users, $project_managers);
                //super user
                $task->permission_assign_users = $this->taskpermissions->check('assign-users', $task, $project, $assigned_users, $assigned_project_users, $project_managers);
                //super user
                $task->permission_super_user = $this->taskpermissions->check('super-user', $task, $project, $assigned_users, $assigned_project_users, $project_managers);
            }
            //template tasks
            if ($task->task_projectid < 0) {
                //edit permissions
                $task->permission_edit_task = (auth()->user()->role->role_templates_projects >= 2) ? true : false;
                //delete permissions
                $task->permission_delete_task = (auth()->user()->role->role_templates_projects >= 2) ? true : false;
                //delete participate
                $task->permission_participate = (auth()->user()->role->role_templates_projects >= 2) ? true : false;
                //super user
                $task->permission_assign_users = false;
                //super user
                $task->permission_super_user = false;
            }
        }
    }

    /**
     * apply permissions to each comment
     * @param object $comment instance of the comment model object
     * @return object
     */
    private function applyCommentPermissions($comment = '') {

        //sanity - make sure this is a valid object
        if ($comment instanceof \App\Models\Comment) {
            //delete permissions
            $comment->permission_delete_comment = $this->commentpermissions->check('delete', $comment);
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
     * apply permissions to each checklist
     * @param object $checklist instance of the checklist model object
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
     * update a cards position (kanban drag & drop)
     * @return null
     */
    public function updatePosition() {

        //validation
        if (!request()->filled('status')) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }
        if (\App\Models\TaskStatus::Where('taskstatus_id', request('status'))->doesntExist()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }
        if (!$task = $this->taskmodel::find(request('task_id'))) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the task
        $tasks = $this->taskrepo->search(request('task_id'));
        $task = $tasks->first();

        //old status
        $old_status = $task->task_status;

        //(scenario - 1) card is placed in between 2 other cards
        if (is_numeric(request('previous_task_id')) && is_numeric(request('next_task_id'))) {
            //get previous task
            if (!$previous_task = $this->taskmodel::find(request('previous_task_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //get next task
            if (!$next_task = $this->taskmodel::find(request('next_task_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //calculate this tasks new position & update it
            $new_position = ($previous_task->task_position + $next_task->task_position) / 2;
            $task->task_position = $new_position;
            $task->task_status = request('status');
            $task->save();
        }

        //(scenario - 2) card is placed at the end of a list
        if (is_numeric(request('previous_task_id')) && !request()->filled('next_task_id')) {
            //get previous task
            if (!$previous_task = $this->taskmodel::find(request('previous_task_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //calculate this tasks new position & update it
            $new_position = $previous_task->task_position + config('settings.db_position_increment');
            $task->task_position = $new_position;
            $task->task_status = request('status');
            $task->save();
        }

        //(scenario - 3) card is placed at the start of a list
        if (is_numeric(request('next_task_id')) && !request()->filled('previous_task_id')) {
            //get next task
            if (!$next_task = $this->taskmodel::find(request('next_task_id'))) {
                abort(409, __('lang.error_request_could_not_be_completed'));
            }
            //calculate this tasks new position & update it
            $new_position = $next_task->task_position / 2;
            $task->task_position = $new_position;
            $task->task_status = request('status');
            $task->save();
        }

        //(scenario - 4) card is placed on an empty board
        if (!request()->filled('previous_task_id') && !request()->filled('next_task_id')) {
            //update only status
            $task->task_status = request('status');
            $task->save();
        }

        //status was changed - record event
        if ($old_status != $task->task_status) {
            //get refreshed task
            $tasks = $this->taskrepo->search(request('task_id'));
            $task = $tasks->first();

            /** ----------------------------------------------
             * record event [status]
             * ----------------------------------------------*/
            $data = [
                'event_creatorid' => auth()->id(),
                'event_item' => 'status',
                'event_item_id' => '',
                'event_item_lang' => 'event_changed_task_status',
                'event_item_content' => $task->task_status,
                'event_item_content2' => '',
                'event_parent_type' => 'task',
                'event_parent_id' => $task->task_id,
                'event_parent_title' => $task->task_title,
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'no',
                'event_clientid' => $task->task_clientid,
                'eventresource_type' => 'project',
                'eventresource_id' => $task->task_projectid,
                'event_notification_category' => 'notifications_tasks_activity',
            ];
            //record event
            if ($event_id = $this->eventrepo->create($data)) {
                //get users
                $users = $this->taskpermissions->check('users', $task);
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
                        $mail = new \App\Mail\TaskStatusChanged($user, $data, $task);
                        $mail->build();
                    }
                }
            }
        }

        //reponse payload
        $payload = [
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        // 5 aug 2021 - the response is not working because the ajax call was not made using nextloop ajax (see app.js line 1754)
        //return new UpdatePositionResponse($payload);

    }

    /**
     * Archive a task
     * @param object TimerRepository instance of the repository
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function archive($id) {

        //get task and update status
        $task = \App\Models\Task::Where('task_id', $id)->first();
        $task->task_active_state = 'archived';
        $task->save();

        //get refreshed task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //apply permissions
        $this->applyPermissions($task);

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'action' => 'archive',
        ];

        //show the form
        return new ArchiveResponse($payload);
    }

    /**
     * Activate a task
     * @param object TimerRepository instance of the repository
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function activate($id) {

        //get task and update status
        $task = \App\Models\Task::Where('task_id', $id)->first();
        $task->task_active_state = 'active';
        $task->save();

        //get refreshed task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //apply permissions
        $this->applyPermissions($task);

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'action' => 'archive',
        ];

        //show the form
        return new ActivateResponse($payload);
    }

    /**
     * show custom fields data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCustomFields($id) {

        //get tasks
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //get customfields
        request()->merge([
            'sort_by' => 'customfields_position',
            'filter_field_status' => 'enabled',
        ]);
        $fields = $this->getCustomFields($task);

        //package to send to response
        $payload = [
            'type' => 'show-custom-fields',
            'task' => $task,
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

        //get tasks
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //get customfields
        request()->merge([
            'sort_by' => 'customfields_position',
            'filter_field_status' => 'enabled',
        ]);
        $fields = $this->getCustomFields($task);

        //package to send to response
        $payload = [
            'type' => 'edit-custom-fields',
            'task' => $task,
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

        //get tasks
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //get customfields
        request()->merge([
            'sort_by' => 'customfields_position',
            'filter_field_status' => 'enabled',
        ]);
        $fields = $this->getCustomFields($task);

        //update
        foreach ($fields as $field) {
            \App\Models\Task::where('task_id', $id)
                ->update([
                    $field->customfields_name => $_POST[$field->customfields_name],
                ]);
        }

        //refeshed data
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();
        $fields = $this->getCustomFields($task);

        //package to send to response
        $payload = [
            'type' => 'show-custom-fields',
            'task' => $task,
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

        //get tasks
        if ($note = \App\Models\Note::Where('noteresource_type', 'task')
            ->Where('noteresource_id', $id)
            ->Where('note_creatorid', auth()->id())->first()) {
            $has_note = true;
        } else {
            $note = [];
            $has_note = false;
        }

        //refeshed data
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //package to send to response
        $payload = [
            'type' => 'show-notes',
            'note' => $note,
            'task' => $task,
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

        //get tasks
        $note = \App\Models\Note::Where('noteresource_type', 'task')
            ->Where('noteresource_id', $id)
            ->Where('note_creatorid', auth()->id())->first();

        //refeshed data
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //package to send to response
        $payload = [
            'type' => 'edit-notes',
            'note' => $note,
            'task' => $task,
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
        \App\Models\Note::Where('noteresource_type', 'task')
            ->where('noteresource_id', $id)
            ->where('note_creatorid', auth()->id())->delete();

        //refeshed data
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        $payload = [
            'type' => 'show-notes',
            'note' => [],
            'task' => $task,
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
        \App\Models\Note::Where('noteresource_type', 'task')
            ->where('noteresource_id', $id)
            ->where('note_creatorid', auth()->id())->delete();

        //refeshed data
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        $payload = [
            'type' => 'create-notes',
            'note' => [],
            'task' => $task,
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
        if (!request()->filled('task_mynotes')) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //delete all notes by this user
        \App\Models\Note::Where('noteresource_type', 'task')
            ->where('noteresource_id', $id)
            ->where('note_creatorid', auth()->id())->delete();

        //create note
        $note = new \App\Models\Note();
        $note->noteresource_type = 'task';
        $note->noteresource_id = $id;
        $note->note_creatorid = auth()->id();
        $note->note_description = request('task_mynotes');
        $note->note_visibility = 'private';
        $note->save();

        //refeshed data
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //package to send to response
        $payload = [
            'type' => 'show-notes',
            'note' => $note,
            'task' => $task,
            'has_note' => true,
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
    public function cloneTask($id) {

        //get task
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //payload
        $payload = [
            'task' => $task,
        ];

        //show the view
        return new CloneResponse($payload);

    }

    /**
     * show form for cloning tasks
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cloneStore(ProjectRepository $projectrepo, ProjectPermissions $projectpermissions, TaskAssignedRepository $assignedrepo, $id) {

        //validate task and milestones
        if (!request()->filled('task_milestoneid') || !request()->filled('project_id')) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //project exists
        if (\App\Models\Project::Where('project_id', request('project_id'))->doesntExist()) {
            abort(409, __('lang.project') . ' - ' . __('lang.is_invalid'));
        }

        //project exists
        if (\App\Models\Milestone::Where('milestone_id', request('task_milestoneid'))->where('milestone_projectid', request('project_id'))->doesntExist()) {
            abort(409, __('lang.milestone') . ' - ' . __('lang.is_invalid'));
        }

        //get users projects
        if (auth()->user()->is_team) {
            if (auth()->user()->is_admin) {
                $projects = \App\Models\Project::Where('project_type', 'project')->get();
                $project_list = [];
                foreach ($projects as $project) {
                    $project_list[] = $project->project_id;
                }
            } else {
                $project_list = $projectrepo->usersAssignedAndManageProjects(auth()->id(), 'list');
            }
        } else {
            $project_list = $rojectrepo->clientsProjects(auth()->user()->clientid, 'list');
        }

        //validate the project is valid for this user
        if (!in_array(request('project_id'), $project_list)) {
            abort(409, __('lang.invalid_permissions_for_project'));
        }

        //task
        $task = \App\Models\Task::Where('task_id', $id)->first();

        //project
        $project = \App\Models\Project::Where('project_id', request('project_id'))->first();

        //clone the task
        $data = [
            'task_title' => request('task_title'),
            'task_status' => request('task_status'),
            'task_milestoneid' => request('task_milestoneid'),
            'copy_checklist' => (request('copy_checklist') == 'on') ? true : false,
            'copy_files' => (request('copy_files') == 'on') ? true : false,
        ];
        $new_task = $this->taskrepo->cloneTask($task, $project, $data);

        //assign the task to self, for none admin users
        if (auth()->user()->is_team) {
            if (!$projectpermissions->check('super-user', $project)) {
                $assigned_users = $assignedrepo->add($new_task->task_id, auth()->id());
            }
        }

        //get table friendly collection
        $tasks = $this->taskrepo->search($new_task->task_id);

        //process for timers
        $this->processTasks($tasks);

        //apply some permissions
        if ($tasks) {
            foreach ($tasks as $task) {
                $this->applyPermissions($task);
            }
        }

        //apply custom fields
        if ($tasks) {
            foreach ($tasks as $task) {
                $task->fields = $this->getCustomFields($task);
            }
        }

        //payload
        $payload = [
            'task' => $tasks->first(),
            'tasks' => $tasks,
        ];

        //show the view
        return new CloneStoreResponse($payload);

    }

    /**
     * Show the form for editing the specified task
     * @param  int  $task task id
     * @return \Illuminate\Http\Response
     */
    public function recurringSettings($id) {

        //get the project
        $task = \App\Models\Task::Where('task_id', $id)->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'task' => $task,
        ];

        //modal request
        if (request('source') == 'modal') {
            $html = view('pages/task/components/recurring', compact('task'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
            //ajax response
            return response()->json($jsondata);
        }

        //response
        return new RecurringSettingsResponse($payload);
    }

    /**
     * Update recurring settings
     * @param object TaskRecurrringSettings instance of the request validation object
     * @param  int  $task task id
     * @return \Illuminate\Http\Response
     */
    public function recurringSettingsUpdate(TaskRecurrringSettings $request, $id) {

        //get project
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //update
        $task->task_recurring = 'yes';
        $task->task_recurring_duration = request('task_recurring_duration');
        $task->task_recurring_period = request('task_recurring_period');
        $task->task_recurring_cycles = request('task_recurring_cycles');
        $task->task_recurring_next = request('task_recurring_next');
        $task->task_recurring_copy_checklists = (request('task_recurring_copy_checklists') == 'on') ? 'yes' : 'no';
        $task->task_recurring_copy_files = (request('task_recurring_copy_files') == 'on') ? 'yes' : 'no';
        $task->task_recurring_automatically_assign = (request('task_recurring_automatically_assign') == 'on') ? 'yes' : 'no';
        $task->save();

        //reset for infinite tasks (incase it had previously been set to finished)
        if ($task->task_recurring_cycles == 0) {
            $task->task_recurring_finished = 'no';
            $task->save();
        }

        //get refreshed
        $tasks = $this->taskrepo->search($id);
        $task = $tasks->first();

        //apply permissions
        $this->applyPermissions($task);

        //process
        $this->processTask($task);

        //reponse payload
        $payload = [
            'type' => 'update-recurring',
            'action' => 'update',
            'page' => $this->pageSettings('edit'),
            'tasks' => $tasks,
            'task' => $task,
        ];

        //response
        return new UpdateResponse($payload);
    }

    /**
     * stop an task from recurring
     * @return \Illuminate\Http\Response
     */
    public function stopRecurring() {

        //get the task
        $task = \App\Models\Task::Where('task_id', request()->route('task'))->first();

        //update the task
        $task->task_recurring = 'no';
        $task->task_recurring_duration = null;
        $task->task_recurring_period = null;
        $task->task_recurring_cycles = null;
        $task->task_recurring_next = null;
        $task->save();

        //get refreshed
        $tasks = $this->taskrepo->search(request()->route('task'));
        $task = $tasks->first();

        //apply permissions
        $this->applyPermissions($task);

        //process
        $this->processTask($task);

        //reponse payload
        $payload = [
            'type' => 'update-recurring',
            'action' => 'stop',
            'page' => $this->pageSettings('edit'),
            'tasks' => $tasks,
            'task' => $task,
        ];

        //show the form
        return new UpdateResponse($payload);
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
                __('lang.tasks'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'tasks',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_tasks' => 'active',
            'sidepanel_id' => 'sidepanel-filter-tasks',
            'dynamic_search_url' => url('tasks/search?action=search&taskresource_id=' . request('taskresource_id') . '&taskresource_type=' . request('taskresource_type')),
            'add_button_classes' => '',
            'load_more_button_route' => 'tasks',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_task'),
            'add_modal_create_url' => url('tasks/create?taskresource_id=' . request('taskresource_id') . '&taskresource_type=' . request('taskresource_type')),
            'add_modal_action_url' => url('tasks?taskresource_id=' . request('taskresource_id') . '&taskresource_type=' . request('taskresource_type') . '&count=' . ($data['count'] ?? '')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //tasks list page
        if ($section == 'tasks') {
            $page += [
                'meta_title' => __('lang.tasks'),
                'heading' => __('lang.tasks'),
                'mainmenu_tasks' => 'active',
            ];
            return $page;
        }

        //task page
        if ($section == 'task') {
            //adjust
            $page['page'] = 'task';
            //add
            $page += [
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
        $stats = [];

        foreach (config('task_statuses') as $status) {
            $stat = [
                'value' => \App\Models\Task::where('task_status', $status->taskstatus_id)->count(),
                'title' => runtimeLang($status->taskstatus_title),
                'percentage' => '100%',
                'color' => 'bg-' . $status->taskstatus_color,
            ];
            array_push($stats, $stat);
        }

        //return
        return $stats;
    }
}