<?php

namespace App\Permissions;

use App\Repositories\LeadRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Log;

class TaskPermissions {

    /**
     * The task repository instance.
     */
    protected $taskrepo;

    /**
     * The project repository instance.
     */
    protected $projectrepo;

    /**
     * The lead repository instance.
     */
    protected $leadrepo;

    /**
     * Inject dependecies
     */
    public function __construct(
        TaskRepository $taskrepo,
        ProjectRepository $projectrepo,
        LeadRepository $leadrepo
    ) {

        $this->taskrepo = $taskrepo;
        $this->projectrepo = $projectrepo;
        $this->leadrepo = $leadrepo;

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
            'participate',
            'show',
            'timers',
            'super-user',
            'assign-users',
            'users',
            'assigned',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified Task ONLY.
     *
     * [EXAMPLE USAGE]
     *          if (!$this->taskpermissons->check($task_id, 'delete')) {
     *                 abort(413)
     *          }
     *
     * @param numeric $task object or id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @param object $project optional
     * @param object $assigned_users optional
     * @param object $assigned_project_users optional
     * @param object $project_managers optional
     * @return bool true if user has permission
     */
    public function check($action = '', $task = '', $project = '', $assigned_users = '', $assigned_project_users = '', $project_managers = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][task]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($task)) {
            if (!$task = \App\Models\Task::Where('task_id', $task)->first()) {
                Log::error("the task coud not be found", ['process' => '[permissions][task]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[IMPORTANT]: any passed task object must from taskrepo->search() method, not the task model
        if ($task instanceof \App\Models\Task || $task instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //array of assigned users
            if (!isset($assigned_users) || $assigned_users == '') {
                $assigned_users = $task->assigned->pluck('id');
            }
            //array of project managers for parent project
            if (!isset($project_managers) || $project_managers == '') {
                $project_managers = $task->projectmanagers->pluck('id');
            }
            //the task project
            if (!isset($project) || $project == '') {
                $project = $task->project()->first();
            }
            if (!isset($assigned_project_users)) {
                $assigned_project_users = $project->assigned->pluck('id');
            }
        } else {
            Log::error("the task coud not be found", ['process' => '[permissions][task]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
                    if ($user->type == 'team') {
                        //assigned with editing permissions
                        if ($assigned_users->contains($user->id) && $user->role->role_tasks >= 2) {
                            $list[] = $user->id;
                            continue;
                        }
                        //user with global access
                        if ($user->role->role_tasks_scope == 'global' && $user->role->role_tasks >= 2) {
                            $list[] = $user->id;
                            continue;
                        }
                        //creator of the task
                        if ($task->task_creatorid == $user->id) {
                            $list[] = $user->id;
                            continue;
                        }
                        //project managers of parent project
                        if ($project_managers->contains($user->id)) {
                            $list[] = $user->id;
                            continue;
                        }
                    }
                    //client
                    if ($user->type == 'client') {
                        //project allows client participation
                        if ($project instanceof \App\Models\Project) {
                            if ($project->project_clientid == $user->clientid) {
                                if ($project->clientperm_tasks_view == 'yes' && $task->task_client_visibility == 'yes') {
                                    $list[] = $user->id;
                                    continue;
                                }
                            }
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
         * [ADMIN LEVEL USER]
         * Check if a user has super user/admin level permissions on the task
         *
         */
        if ($action == 'super-user') {
            //project managers of parent project
            if ($project_managers->contains(auth()->id())) {
                return true;
            }
            //admin user
            if (auth()->user()->role_id == 1) {
                return true;
            }
            //project templates
            if ($project->project_type = 'template' && auth()->user()->role->role_templates_projects >= 2) {
                return true;
            }
        }

        /**
         * Check is logged in user is assigned to this task
         */
        if ($action == 'assigned') {
            if ($assigned_users->contains(auth()->id())) {
                return true;
            }
        }

        /**
         * [ASSIGN USERS]
         * Check if a user has assigning permissions on the task
         *
         */
        if ($action == 'assign-users') {
            //project managers of parent project
            if ($project_managers->contains(auth()->id())) {
                return true;
            }
            //admin user
            if (auth()->user()->role_id == 1) {
                return true;
            }
            //generally allowed
            if (auth()->user()->role->role_assign_tasks == 'yes') {
                return true;
            }
        }

        /**
         * [EDITING A TASK]
         *   grant permissions as follows:
         *   - assigned task members with general task editing permissions
         *   - team user with global task editing permissions
         *   - client/team user who created the task
         */
        if ($action == 'edit') {

            //team
            if (auth()->user()->is_team) {
                //assigned with editing permissions
                if ($assigned_users->contains(auth()->id()) && auth()->user()->role->role_tasks >= 2) {
                    return true;
                }
                //user with global access
                if (auth()->user()->role->role_tasks_scope == 'global' && auth()->user()->role->role_tasks >= 2) {
                    return true;
                }
                //creator of the task
                if ($task->task_creatorid == auth()->id()) {
                    return true;
                }
                //project managers of parent project
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //project templates
                if ($project->project_type = 'template' && auth()->user()->role->role_templates_projects >= 2) {
                    return true;
                }
            }

            //client
            if (auth()->user()->is_client) {
                //creator of the task
                if ($task->task_creatorid == auth()->id()) {
                    return true;
                }
                //assigned to the task
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
            }
        }

        /**
         * [VIEW A TASK]
         *   grant permissions as follows:
         *   - assigned task members with general task editing permissions
         *   - team user with global task editing permissions
         *   - client/team user who created the task
         */
        if ($action == 'view') {

            if (auth()->user()->is_team) {
                //assigned with editing permissions
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //user with global access
                if (auth()->user()->role->role_tasks_scope == 'global' && auth()->user()->role->role_tasks >= 1) {
                    return true;
                }
                //creator of the task
                if ($task->task_creatorid == auth()->id()) {
                    return true;
                }
                //project managers of parent project
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //project allows task collaboration by other members of the project
                if ($project instanceof \App\Models\Project) {
                    if ($assigned_project_users->contains(auth()->id())) {
                        if ($project->assignedperm_tasks_collaborate == 'yes') {
                            return true;
                        }
                    }
                }
                //project templates
                if ($project->project_type = 'template' && auth()->user()->role->role_templates_projects >= 2) {
                    return true;
                }
            }

            //client
            if (auth()->user()->is_client) {
                //project allows client participation
                if ($project instanceof \App\Models\Project) {
                    if ($project->project_clientid == auth()->user()->clientid) {
                        if ($project->clientperm_tasks_view == 'yes' && $task->task_client_visibility == 'yes') {
                            return true;
                        }
                    }
                }
            }

        }

        /**
         * [DELETING A TASK]
         *   grant permissions as follows:
         *   - assigned task members with general task deleteing permissions
         *   - team user with global task deleteing permissions
         *   - client/team user who created the task
         */
        if ($action == 'delete') {

            //team
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_tasks >= 3) {
                    //global
                    if (auth()->user()->role->role_tasks_scope == 'global') {
                        return true;
                    }
                }
                //creator
                if ($task->task_creatorid == auth()->id()) {
                    return true;
                }
                //project managers of parent project
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //project templates
                if ($project->project_type = 'template' && auth()->user()->role->role_templates_projects >= 2) {
                    return true;
                }
            }

            //client
            if (auth()->user()->is_client) {
                //creator of the task
                if ($task->task_creatorid == auth()->id()) {
                    return true;
                }
            }
        }

        /**
         * [PARTICIPATE]
         * - comments, attach files, create checklists, etc
         */
        if ($action == 'participate') {

            if (auth()->user()->is_team) {
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
                //user with global access
                if (auth()->user()->role->role_tasks_scope == 'global' && auth()->user()->role->role_tasks >= 2) {
                    return true;
                }
                //creator
                if ($task->task_creatorid == auth()->id()) {
                    return true;
                }
                //project managers of parent project
                if ($project_managers->contains(auth()->id())) {
                    return true;
                }
                //project allows task collaboration by other members of the project
                if ($project instanceof \App\Models\Project) {
                    if ($assigned_project_users->contains(auth()->id())) {
                        if ($project->assignedperm_tasks_collaborate == 'yes') {
                            return true;
                        }
                    }
                }
                //project templates
                if ($project->project_type = 'template' && auth()->user()->role->role_templates_projects >= 2) {
                    return true;
                }
            }

            //client
            if (auth()->user()->is_client) {
                //project allows task participation
                if ($project instanceof \App\Models\Project) {
                    if ($project->project_clientid == auth()->user()->clientid) {
                        if ($project->clientperm_tasks_collaborate == 'yes' && $task->task_client_visibility == 'yes') {
                            return true;
                        }
                    }
                }
            }

        }

        /**
         * [TIMERS]
         * - comments, attach files, create checklists, etc
         */
        if ($action == 'timers') {

            if (auth()->user()->is_team) {
                //assigned
                if ($assigned_users->contains(auth()->id())) {
                    return true;
                }
            }
        }

        //failed
        Log::info("permissions denied on this task", ['process' => '[permissions][tasks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}