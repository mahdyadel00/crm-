<?php

namespace App\Permissions;

use App\Repositories\ProjectRepository;
use Illuminate\Support\Facades\Log;

class ProjectPermissions {

    /**
     * The repository instance.
     */
    protected $projectrepo;

    protected $project;

    protected $assigned_users;

    protected $project_managers;

    /**
     * Inject dependecies
     */
    public function __construct(ProjectRepository $projectrepo) {
        $this->projectrepo = $projectrepo;
    }

    /**
     * The array of checks that are available.
     * NOTE: when a new check is added, you must also add it to this array
     * @return array
     */
    public function permissionChecksArray() {
        $checks = [
            'view',
            'edit',
            'delete',
            'files-view',
            'files-upload',
            'notes-view',
            'notes-create',
            'comments-view',
            'comments-post',
            'tasks-view',
            'tasks-participate',
            'tasks-add',
            'milestone-manage',
            'milestones-view',
            'tickets-view',
            'timesheets-view',
            'invoices-view',
            'payments-view',
            'expenses-view',
            'super-user',
            'users',
            'assigned',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified project ONLY.
     *
     * [EXAMPLE USAGE]
     *          if (!$projectpermissons->check('delete', $project->project_id)) {
     *                 abort(413)
     *          }
     *
     * @param numeric $resource id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @param mixed $project can be the project id or the actual project object. [IMPORTANT]: passed project object must from projectrepo->search()
     * @return bool true if user has permission
     */
    public function check($action = '', $project = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][project]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($project)) {
            if (!$project = \App\Models\Project::Where('project_id', $project)->first()) {
                Log::error("the project coud not be found", ['process' => '[permissions][project]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => $project ?? '']);
            }
        }

        //[IMPORTANT]: any passed project object must from projectrepo->search() method, not the project model
        if ($project instanceof \App\Models\Project || $project instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //array of assigned users
            $assigned_users = $project->assigned->pluck('id');
            //project managers
            $project_managers = $project->managers->pluck('id');
        } else {
            Log::error("the project coud not be found", ['process' => '[permissions][project]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        /**
         * [ARRAY OF USERS (with view level permssions)]
         * [NOTES] this must have the same logic as $action == 'view' below
         */
        if ($action == 'users') {

            $list = [];
            $users = \App\Models\User::with('role')->get();

            foreach ($users as $user) {
                if ($user->id > 0) {
                    //team
                    if ($user->type == 'team') {
                        //gloabl user
                        if ($user->role->role_projects >= 1 && $user->role->role_projects_scope == 'global') {
                            $list[] = $user->id;
                            continue;
                        }
                        //managers
                        if ($project_managers->contains($user->id)) {
                            $list[] = $user->id;
                            continue;
                        }
                        //assigned
                        if ($assigned_users->contains($user->id)) {
                            $list[] = $user->id;
                            continue;
                        }
                    }
                    //client
                    if ($user->type == 'client') {
                        if ($project->client->client_id == $user->clientid) {
                            $list[] = $user->id;
                            continue;
                        }
                    }
                }
            }
            return $list;
        }

        /**
         * [ADMIN]
         * Grant full permission for whatever request
         *
         */
        if (auth()->user()->role_id == 1) {
            return true;
        }

        /**
         * Check is logged in user is assigned to this project
         */
        if ($action == 'assigned') {
            if ($assigned_users->contains(auth()->id())) {
                return true;
            }
        }

        //save
        $this->assigned_users = $assigned_users;
        $this->project = $project;
        $this->project_managers = $project_managers;

        /**
         * [RETURN ADMIN LEVEL USERS]
         * Check if a user has super user/admin level permissions on the project
         *
         */
        if ($action == 'super-user') {
            //managers
            if ($project_managers->contains(auth()->id())) {
                return true;
            }

            //user who created the project
            if ($project->project_creatorid == auth()->id()) {
                return true;
            }

            //admin users
            if (auth()->user()->role_id == 1) {
                return true;
            }
            //project templates
            if ($project->project_type == 'template' && auth()->user()->role->role_templates_projects >= 2) {
                return true;
            }
        }

        /**
         * [VIEW A PROJECT]
         */
        if ($action == 'view') {

            //team
            if (auth()->user()->is_team) {
                //gloabl user
                if (auth()->user()->role->role_projects >= 1 && auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }

            //client
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid) {
                    return true;
                }
            }
        }

        /**
         * [EDITING A PROJECT]
         */
        if ($action == 'edit') {
            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_projects >= 2 && auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }
        }

        /**
         * [DELETING A PROJECT]
         */
        if ($action == 'delete') {
            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_projects >= 3) {
                    //global
                    if (auth()->user()->role->role_projects_scope == 'global') {
                        return true;
                    }
                    //creator
                    if ($project->project_creatorid == auth()->id()) {
                        return true;
                    }
                }
            }
        }

        /**
         * [VIEW PROJECT FILES]
         * uselful for displaying menu item
         */
        if ($action == 'files-view') {

            //team
            if (auth()->user()->is_team) {
                //global
                if (auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }

            //client
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid) {
                    return true;
                }
            }
        }

        /**
         * [UPLOAD PROJECT FILES]
         * uselful for displaying menu item & upload button
         */
        if ($action == 'files-upload') {

            //team
            if (auth()->user()->is_team) {
                //global
                if (auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }

            //client user with permission to upload project files (on own projects)
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid) {
                    return true;
                }
            }
        }

        /**
         * [VIEW PROJECT COMMENTS]
         * uselful for displaying menu item
         */
        if ($action == 'comments-view') {

            //team
            if (auth()->user()->is_team) {
                //global
                if (auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }

            //client user with permission to view project comments (on own projects)
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid) {
                    return true;
                }
            }
        }

        /**
         * [POST PROJECT COMMENTS]
         * uselful for displaying menu item, post comment form
         */
        if ($action == 'comments-post') {

            //team
            if (auth()->user()->is_team) {
                //global
                if (auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }

            //client user with permission to upload project comments (on own projects)
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid) {
                    return true;
                }
            }
        }

        /**
         * [CREATE AND VIEW PROJECT NOTES]
         * uselful for displaying menu item
         */
        if ($action == 'notes-view') {
            //team
            if (auth()->user()->is_team) {
                //global
                if (auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }
        }

        /**
         * [VIEW PROJECT NOTES]
         * uselful for displaying menu item & post form
         */
        if ($action == 'notes-create') {

            //team
            if (auth()->user()->is_team) {
                //global
                if (auth()->user()->role->role_projects_scope == 'global') {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
            }
        }

        /**
         * [VIEW PROJECT TASKS]
         * uselful for displaying menu item
         */
        if ($action == 'tasks-view') {

            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_tasks >= 1) {
                    //global
                    if (auth()->user()->role->role_tasks_scope == 'global') {
                        return true;
                    }
                    //managers
                    if ($project_managers->contains(auth()->id())) {
                        return true;
                    }
                    //assigned
                    if ($assigned_users->contains(auth()->id())) {
                        return true;
                    }
                    //creator
                    if ($project->project_creatorid == auth()->id()) {
                        return true;
                    }
                }
            }

            //client user with permission to view project tasks (on own projects)
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid && $project->clientperm_tasks_view == 'yes') {
                    return true;
                }
            }

        }

        /**
         * [ADD A NEW TASK]
         */
        if ($action == 'tasks-add') {
            //team
            if (auth()->user()->is_team) {
                //admin
                if (auth()->user()->is_admin) {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
            }
            //client on project that is enabled to add tasks
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid && $project->clientperm_tasks_create == 'yes') {
                    return true;
                }
            }
        }

        /**
         * [TASK PARTICIPATION]
         */
        if ($action == 'tasks-participate') {
            //team
            if (auth()->user()->is_team) {
                //admin
                if (auth()->user()->is_admin) {
                    return true;
                }
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
            }
            //client on project that is enabled to participate
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid && $project->clientperm_tasks_collaborate == 'yes') {
                    return true;
                }
            }
        }

        /**
         * [VIEW PROJECT MILESTONE]
         * uselful for displaying menu item
         */
        if ($action == 'milestones-view') {

            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_tasks >= 1) {
                    //global
                    if (auth()->user()->role->role_tasks_scope == 'global') {
                        return true;
                    }
                    //managers
                    if ($project_managers->contains(auth()->id())) {
                        return true;
                    }
                    //assigned
                    if ($assigned_users->contains(auth()->id())) {
                        return true;
                    }
                    //creator
                    if ($project->project_creatorid == auth()->id()) {
                        return true;
                    }
                }
            }

            //client user with permission to view project milestones (on own projects)
            if (auth()->user()->is_client) {
                if ($project->client->client_id == auth()->user()->clientid) {
                    return true;
                }
            }
        }

        /**
         * [VIEW PROJECT TIMESHEETS]
         * uselful for displaying menu item
         */
        if ($action == 'timesheets-view') {

            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_timesheets >= 1) {
                    //global
                    if (auth()->user()->role->role_timesheets_scope == 'global') {
                        return true;
                    }
                    //managers
                    if ($project_managers->contains(auth()->id())) {
                        return true;
                    }
                    //assigned
                    if ($assigned_users->contains(auth()->id())) {
                        return true;
                    }
                    //creator
                    if ($project->project_creatorid == auth()->id()) {
                        return true;
                    }
                }
            }

            //client user with permission to view timesheets (on own projects)
            if (auth()->user()->is_client) {
                if (auth()->user()->account_owner == 'yes') {
                    if ($project->client->client_id == auth()->user()->clientid && $project->clientperm_timesheets_view == 'yes') {
                        return true;
                    }
                }
            }

        }

        /**
         * [VIEW PROJECT EXPENSES]
         * uselful for displaying menu item
         */
        if ($action == 'expenses-view') {

            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_expenses >= 1) {
                    //global
                    if (auth()->user()->role->role_expenses_scope == 'global') {
                        return true;
                    }
                    //managers
                    if ($project_managers->contains(auth()->id())) {
                        return true;
                    }
                    //assigned
                    if ($assigned_users->contains(auth()->id())) {
                        return true;
                    }
                    //creator
                    if ($project->project_creatorid == auth()->id()) {
                        return true;
                    }
                }
            }

            //client user with permission to view expenses (on own projects)
            if (auth()->user()->is_client) {
                if (auth()->user()->account_owner == 'yes') {
                    if ($project->client->client_id == auth()->user()->clientid && $project->clientperm_expenses_view == 'yes') {
                        return true;
                    }
                }
            }
        }

        /**
         * [VIEW PROJECT INVOICES]
         * uselful for displaying menu item
         */
        if ($action == 'invoices-view') {

            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_invoices >= 1) {
                    return true;
                }
            }

            //client user
            if (auth()->user()->is_client) {
                if (auth()->user()->account_owner == 'yes') {
                    if ($project->client->client_id == auth()->user()->clientid) {
                        return true;
                    }
                }
            }
        }

        /**
         * [VIEW PROJECT PAYMENTS]
         */
        if ($action == 'payments-view') {

            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_invoices >= 1) {
                    return true;
                }
            }

            //client user
            if (auth()->user()->is_client) {
                if (auth()->user()->account_owner == 'yes') {
                    if ($project->client->client_id == auth()->user()->clientid) {
                        return true;
                    }
                }
            }
        }

        /**
         * [VIEW PROJECT TICKETS]
         */
        if ($action == 'tickets-view') {
            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_tickets >= 1) {
                    return true;
                }
            }
        }

        /**
         * [MANAGE MILESTONE]
         */
        if ($action == 'milestones-manage') {

            //team
            if (auth()->user()->is_team) {
                //managers
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //assigned - and project that allows milestome management
                if ($assigned_users->contains(auth()->id())) {
                    if ($project->assignedperm_milestone_manage == 'yes') {
                        return true;
                    }
                }
                //creator
                if ($project->project_creatorid == auth()->id()) {
                    return true;
                }
            }
        }

        //passed
        Log::info("user does not have the requested permission level ($action) for this project", ['process' => '[permissions][project]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
        return false;
    }

}