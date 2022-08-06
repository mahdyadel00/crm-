<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for various feeds
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;
use App\Permissions\ProjectPermissions;
use App\Repositories\ClientRepository;
use App\Repositories\LeadRepository;
use App\Repositories\MilestoneRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Log;

class Feed extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //team only stuff
        $this->middleware('teamCheck')->only([
            'companyNames',
            'contactNames',
            'tags',
            'emailAddress',
            'projects',
            'leads',
        ]);

        //no search term provided
        if (request('term') == '') {
            $results = [];
            return response()->json($results);
        }

    }

    /**
     * ajax search results for company name
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function companyNames(ClientRepository $clientrepo) {

        $feed = $clientrepo->autocompleteFeed('company_name', request('term'));

        return response()->json($feed);
    }

    /**
     * ajax search results for contact names (client & team)
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function contactNames(UserRepository $userepo) {

        $feed = $userepo->autocompleteNames(request('type'), request('term'));

        return response()->json($feed);
    }

    /**
     * ajax search results for email address
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function emailAddress(UserRepository $clientrepo) {

        $feed = $clientrepo->autocompleteEmail(request('type'), request('term'));

        return response()->json($feed);
    }

    /**
     * ajax search results for tags
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function tags(TagRepository $tagrepo) {

        $feed = $tagrepo->autocompleteFeed(request('type'), request('term'));

        return response()->json($feed);
    }

    /**
     * ajax search results for projects
     * calls to this function MUST have a request('ref'), so that a specific check in done
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function projects(ProjectRepository $projectrepo) {

        //default
        $feed = [];

        /**
         * The returned projects are limted as follows:
         *          - all the projects. Limited by the active users 'projects permissions' level and scope
         * */
        if (request('ref') == 'general' && auth()->user()->role->role_projects > 0) {
            if (auth()->user()->role->role_projects_scope == 'global') {
                $feed = $projectrepo->autocompleteFeed('', '', request('term'));
            } else {
                $feed = $projectrepo->autocompleteFeed('', 'assigned', request('term'));
            }
        }

        /**
         * The returned projects are limted as follows:
         *      - all project belonging to the specified client. Limited by the active users 'projects permissions' level and scope
         *      - client ID is optional. If not specified, then all general projects are returned (nb: its needed this way by other processes)
         * */
        if (request('ref') == 'clients_projects') {
            if (auth()->user()->role->role_projects_scope == 'global') {
                $feed = $projectrepo->autocompleteClientsProjectsFeed('', '', request('client_id'), request('term'));
            } else {
                $feed = $projectrepo->autocompleteClientsProjectsFeed('', 'assigned', request('client_id'), request('term'));
            }
        }

        //default
        return response()->json($feed);
    }

    /**
     * ajax search results for leads
     * calls to this function MUST have a request('ref'), so that a specific check in done
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function leads(LeadRepository $leadrepo) {

        $feed = [];

        /**
         * The returned projects are limted as follows:
         *          - only the leads that a user is assigned to
         *          - all the leads, if the user has global project viewing permissions
         * */
        if (request('ref') == 'general' && auth()->user()->role->role_leads > 0) {
            if (auth()->user()->role->role_leads_scope == 'global') {
                $feed = $leadrepo->autocompleteFeed('', '', request('term'));
            } else {
                $feed = $leadrepo->autocompleteFeed('', 'assigned', request('term'));
            }
            return response()->json($feed);
        }

        //default
        return response()->json($feed);
    }

    /**
     * ajax search results for leads
     * calls to this function MUST have a request('ref'), so that a specific check in done
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function leadNames(LeadRepository $leadrepo) {

        $feed = [];

        /**
         * The returned projects are limted as follows:
         *          - only the leads that a user is assigned to
         *          - all the leads, if the user has global project viewing permissions
         * */
        if (request('ref') == 'general' && auth()->user()->role->role_leads > 0) {
            if (auth()->user()->role->role_leads_scope == 'global') {
                $feed = $leadrepo->autocompleteFeedNames('', '', request('term'));
            } else {
                $feed = $leadrepo->autocompleteFeedNames('', 'assigned', request('term'));
            }
            return response()->json($feed);
        }

        //default
        return response()->json($feed);
    }

    /**
     * ajax search results for contact names (client & team)
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function projectAssignedUsers(ProjectPermissions $projectpermissions) {

        $feed = [];

        //validate
        if (!request()->filled('project_id')) {
            Log::error("no project id was provided in the request", ['process' => '[Feed--projectAssignedUsers]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response()->json([]);
        }

        //fetch the project
        if (!$project = \App\Models\Project::Where('project_id', request('project_id'))->first()) {
            Log::error("project could not be found", ['process' => '[Feed--projectAssignedUsers]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response()->json([]);
        }

        //check users permissions
        if (!$projectpermissions->check('super-user', request('project_id'))) {
            Log::error("user does not have adequet permissions on this project", ['process' => '[Feed--projectAssignedUsers]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => request('project_id'), 'user_id' => auth()->id()]);
            return response()->json([]);
        }

        //get the users assiged to the project and return as array
        foreach ($project->assigned()->get() as $user) {
            $feed[] = [
                'value' => $user->full_name,
                'id' => $user->id,
            ];
        }

        return response()->json($feed);
    }

    /**
     * ajax search results for contact names (client & team)
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function projectAssignedTasks(TaskRepository $taskrepo) {

        $feed = [];

        //validate
        if (!request()->filled('project_id')) {
            Log::error("no project id was provided in the request", ['process' => '[Feed--projectAssignedUsers]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response()->json([]);
        }

        //filter for tasks assigned to the user and for this project
        request()->merge([
            'filter_my_tasks' => true,
            'filter_task_projectid' => request('project_id'),
        ]);
        if (!$tasks = $taskrepo->search()) {
            Log::error("project could not be found", ['process' => '[Feed--projectAssignedUsers]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response()->json([]);
        }

        //get the tasks and turn into array for select2
        foreach ($tasks as $task) {
            $feed[] = [
                'value' => $task->task_title,
                'id' => $task->task_id,
            ];
        }

        return response()->json($feed);
    }

    /**
     * a list of projects to show when cloning a task
     * @return \Illuminate\Http\Response
     */
    public function cloneTaskProjects(ProjectRepository $projectrepo) {

        //admin user
        if (auth()->user()->is_team) {
            if (auth()->user()->is_admin) {
                $feed = $projectrepo->autocompleteFeed('', '', request('term'));
            } else {
                $feed = $projectrepo->usersAssignedAndManageProjects(auth()->id(), 'feed');
            }
        }

        //client user
        if (auth()->user()->is_client) {
            $feed = $projectrepo->clientsProjects(auth()->user()->clientid, 'feed');
        }

        return response()->json($feed);
    }

    /**
     * ajax search results a projects milestones
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function projectsMilestones(MilestoneRepository $milestonerepo, ProjectPermissions $projectpermissions) {

        //check users permissions
        if (!$projectpermissions->check('view', request('project_id'))) {
            return response()->json([]);
        }

        $feed = $milestonerepo->autocompleteProjectsMilestones(request('project_id'));

        return response()->json($feed);
    }

    /**
     * ajax search results for contact names (client & team)
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function projectClientUsers() {

        $feed = [];

        //validate
        if (!request()->filled('project_id')) {
            Log::error("no project id was provided in the request", ['process' => '[feed--clientProjectUsers]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response()->json([]);
        }

        //fetch the project
        if (!$project = \App\Models\Project::Where('project_id', request('project_id'))->first()) {
            Log::error("project could not be found", ['process' => '[feed--clientProjectUsers]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response()->json([]);
        }

        //get client users
        $client_users = \App\Models\User::Where('clientid', $project->project_clientid)->orderBy('first_name', 'asc')->get();

        //get the users assiged to the project and return as array
        foreach ($client_users as $user) {
            $feed[] = [
                'value' => $user->full_name,
                'id' => $user->id,
            ];
        }

        return response()->json($feed);
    }
}