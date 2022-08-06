<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for tasks
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tasks;
use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use Closure;
use Log;

class Create {

    /**
     * The permisson repository instance.
     */
    protected $taskpermissions;

    /**
     * The permisson repository instance.
     */
    protected $projectpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(TaskPermissions $taskpermissions, ProjectPermissions $projectpermissions) {

        //permissions repos
        $this->taskpermissions = $taskpermissions;
        $this->projectpermissions = $projectpermissions;
    }

    /**
     * This middleware does the following:
     *   1. checks users permissions to [create] a new resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.tasks')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //frontend
        $this->fronteEnd();

        //team user - project task
        if (auth()->user()->is_team) {
            if (request()->filled('taskresource_id') && request()->filled('taskresource_type') == 'project') {
                if ($project = \App\Models\Project::Where('project_id', request('taskresource_id'))->first()) {
                    //assigned user
                    config([
                        'project.assigned' => $project->assigned()->get(),
                        'visibility.projects_assigned_users' => true,
                    ]);
                    if ($this->projectpermissions->check('tasks-add', $project)) {
                        return $next($request);
                    }
                    //project templates
                    if($project->project_type = 'template' && auth()->user()->role->role_templates_projects >= 2){
                        return $next($request);
                    }
                }
            }
        }

        //team tasks from list page
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tasks >= 2) {
                return $next($request);
            }
        }

        //client user
        if (auth()->user()->is_client) {

            //prefill some data
            request()->merge([
                'task_billable' => null,
            ]);

            if (request()->filled('taskresource_id') && request()->filled('taskresource_type') == 'project') {
                if ($project = \App\Models\Project::Where('project_id', request('taskresource_id'))->first()) {
                    //view & add tasks
                    if ($project->clientperm_tasks_create == 'yes') {
                        return $next($request);
                    }
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][tasks][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //defaults
        config([
            'visibility.task_modal_project_option' => true,
            'visibility.task_modal_additional_options' => true,
            'visibility.tasks_standard_features' => true, //things to show for tasks linked to project (not templates)
        ]);

        //show milestone dropdown only from project page
        if (request()->filled('taskresource_id') && request()->filled('taskresource_type') == 'project') {
            config([
                'visibility.task_modal_milestone_option' => true,
            ]);
        }

        //assigning a task and setting its manager
        if (request('taskresource_type') == 'team') {
            if (auth()->user()->role->role_assign_tasks == 'yes') {
                config(['visibility.task_modal_assign_fields' => true]);
            } else {
                //assign only to current user and also make manager
                request()->merge([
                    'assigned' => [auth()->id()],
                ]);
            }
        }

        //project is already specified
        if (request()->filled('taskresource_id')) {
            request()->merge([
                'task_projectid' => request('taskresource_id'),
            ]);
            config([
                'visibility.task_modal_project_option' => false,
            ]);
        }

        //client options
        if (auth()->user()->is_client) {
            request()->merge([
                'task_status' => (is_numeric(request('task_status'))) ? request('task_status') : 1,
                'task_billable' => 'yes',
                'task_priority' => 'normal',
                'task_client_visibility' => 'on',
            ]);
            config([
                'visibility.task_modal_additional_options' => false,
            ]);
        }

        //clicked from topnav 'add' button
        if (request('ref') == 'quickadd') {
            config([
                'visibility.task_show_task_option' => true,
                'visibility.task_modal_project_option' => true,
            ]);
        }

        //hide elements for tasks linked to project templates
        if (is_numeric(request('taskresource_id')) && request('taskresource_id') < 0) {
            config([
                'visibility.tasks_standard_features' => false,
                'visibility.tasks_card_assigned' => false,
            ]);
        }
    }
}
