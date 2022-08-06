<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for tasks
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tasks;

use App\Models\Task;
use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use Closure;
use Log;

class Index {

    /**
     * The project permisson repository instance.
     */
    protected $projectpermissons;

    /**
     * The permisson repository instance.
     */
    protected $taskpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $projectpermissons, TaskPermissions $taskpermissions) {

        $this->projectpermissons = $projectpermissons;

        $this->taskpermissions = $taskpermissions;
    }

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] tasks
     *   3. modifies the request object as needed
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

        //various frontend and visibility settings
        $this->fronteEnd();

        //default - show only project tasks (and not template) - do not apply this filter when in project page
        if (!request()->filled('taskresource_type')) {
            if (request('filter_pt') == 'template') {
                //do nothing
            } else {
                request()->merge([
                    'filter_project_type' => 'project',
                ]);
            }
        }

        //toggle layout
        $this->toggleKanbanView();

        //[limit] - for users with only local level scope
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tasks_scope == 'own') {
                request()->merge(['filter_my_tasks' => array(auth()->id())]);
            }
        }

        //first check - if this is a dynamic task url
        if (is_numeric(request()->route('task')) && request()->segment(2) == 'v') {
            if ($this->taskpermissions->check('view', request()->route('task'))) {
                config([
                    'visibility.dynamic_load_modal' => true,
                    'settings.dynamic_trigger_dom' => '#dynamic-task-content',
                ]);
                //return $next($request);
            } else {
                abort(404);
            }
        }

        //team user permission
        if (auth()->user()->is_team) {

            //viewing from projetc page
            if (request()->filled('taskresource_type') == 'project' && request()->filled('taskresource_id')) {
                if ($project = \App\Models\Project::Where('project_id', request('taskresource_id'))->first()) {
                    //template project
                    if ($project->project_type == 'template' && auth()->user()->role->role_templates_projects >= 2) {
                        request()->merge(['filter_my_tasks' => null]);
                        return $next($request);
                    }
                    //if user is a super user on the project - show all tasks
                    if ($this->projectpermissons->check('super-user', request('taskresource_id'))) {
                        //toggle 'my tasks' button opntions
                        $this->toggleOwnFilter();
                        return $next($request);
                    }
                }
            }

            //generally
            if (auth()->user()->role->role_tasks >= 1) {
                //toggle 'my tasks' button opntions
                $this->toggleOwnFilter();
                return $next($request);
            }

        }

        //client - view tasks lists only when requested via an ajax call from projects page
        if (auth()->user()->is_client) {
            request()->merge([
                'filter_task_client_visibility' => 'yes',
                'filter_as_per_project_permissions' => 'yes',
                'filter_task_clientid' => auth()->user()->clientid,
            ]);
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][tasks][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //defaults
        config([
            'visibility.tasks_col_project' => true,
            'visibility.tasks_col_priority' => true,
            'visibility.tasks_kanban_actions' => true,
            'visibility.tasks_col_project' => true,
            'visibility.tasks_col_milestone' => false,
            'visibility.tasks_col_priority' => false,
        ]);

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('task') with urlResource('task'), in blade templated
         * */
        if (request('taskresource_type') != '' || is_numeric(request('taskresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&taskresource_type=' . request('taskresource_type') . '&taskresource_id=' . request('taskresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //permissions -viewing
        if (auth()->user()->role->role_tasks >= 1) {
            if (auth()->user()->is_team) {
                config([
                    'visibility.tasks_col_assigned' => true,
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                    'visibility.stats_toggle_button' => true,
                    'visibility.tasks_checkbox' => true,
                    'visibility.tasks_col_mytime' => true,
                ]);
                if (auth()->user()->role_id == 1) {
                    config([
                        'visibility.tasks_col_all_time' => false,
                    ]);
                }
            }
            if (auth()->user()->is_client) {
                config([
                    //visibility
                    'visibility.list_page_actions_search' => true,
                    'visibility.tasks_col_project' => false,
                    'visibility.tasks_nocheckbox' => true,
                ]);
                if (config('system.settings_projects_clientperm_assigned_view') == 'yes') {
                    config([
                        'visibility.tasks_col_assigned' => true,
                    ]);
                }
            }
        }

        //permissions -adding geeral tasks
        if (request('taskresource_type') == '' || request('taskresource_type') == 'client') {
            //team member with global permissions
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_tasks >= 2 && auth()->user()->role->role_tasks_scope == 'gobal') {
                    config([
                        //visibility
                        'visibility.list_page_actions_add_button' => true,
                        'visibility.tasks_col_checkboxes' => true,
                    ]);
                }
            }
        }

        //columns visibility
        if (request('taskresource_type') == 'client') {
            config([
                //visibility
                'visibility.tasks_col_client' => false,
                'visibility.filter_panel_client_task' => false,
                'visibility.tasks_col_category' => false,
            ]);
        }

        //visibility of 'filter assigned" in filter panel
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tasks_scope == 'global') {
                config([
                    //visibility
                    'visibility.filter_panel_assigned' => true,
                ]);
            }
        }

        //team users, show more important columns
        if (auth()->user()->is_team) {
            if (!auth()->user()->is_admin) {
                config([
                    'visibility.tasks_col_assigned' => false,
                    'visibility.tasks_col_priority' => true,
                ]);
            }
        }

        //visibility from project page
        if (request()->filled('taskresource_id') && request('taskresource_type') == 'project') {
            config([
                'visibility.tasks_filter_milestone' => true,
                'visibility.tasks_col_project' => false,
            ]);

            //with permission to add tasks (team or client)
            if ($this->projectpermissons->check('tasks-add', request('taskresource_id'))) {
                config([
                    'visibility.list_page_actions_add_button' => true,
                ]);

                //for team members, allow to add on any board/stage
                if (auth()->user()->is_team) {
                    config([
                        'visibility.kanban_board_add_buttons' => true,
                    ]);
                }
            }

            //hide items when viewing [template] tasks
            if (request('filter_pt') == 'template') {
                config([
                    'visibility.list_page_actions_filter_button' => false,
                    'visibility.stats_toggle_button' => false,
                ]);

                if (auth()->user()->role->role_templates_projects >= 2) {
                    config([
                        'visibility.list_page_actions_add_button' => true,
                    ]);

                }
            }

        }

    }

    function toggleOwnFilter() {

        //visibility of 'my task(s" button - only users with globa scope need this button
        if (auth()->user()->role->role_tasks_scope == 'global') {
            config([
                //visibility
                'visibility.own_tasks_toggle_button' => true,
            ]);
        }

        //show toggle archived tasks button
        if (auth()->user()->is_team) {
            config([
                'visibility.archived_tasks_toggle_button' => true,
            ]);
        }

        //hide items when viewing [template] tasks
        if (request('filter_pt') == 'template') {
            config([
                'visibility.own_tasks_toggle_button' => false,
                'visibility.archived_tasks_toggle_button' => false,
            ]);
        }

        //update 'own tasks filter'
        if (request('toggle') == 'pref_filter_own_tasks') {
            //toggle database settings
            auth()->user()->pref_filter_own_tasks = (auth()->user()->pref_filter_own_tasks == 'yes') ? 'no' : 'yes';
            auth()->user()->save();
        }

        //update show archived tasks
        if (request('toggle') == 'pref_filter_show_archived_tasks') {
            //toggle database settings
            auth()->user()->pref_filter_show_archived_tasks = (auth()->user()->pref_filter_show_archived_tasks == 'yes') ? 'no' : 'yes';
            auth()->user()->save();
        }

        //a filter panel search has been done with assigned - so reset 'my tasks' to 'no'
        if (request()->filled('filter_assigned')) {
            if (auth()->user()->pref_filter_own_tasks == 'yes') {
                auth()->user()->pref_filter_own_tasks = 'no';
                auth()->user()->save();
            }
        }

        //set
        if (auth()->user()->pref_filter_own_tasks == 'yes') {
            request()->merge(['filter_my_tasks' => auth()->id()]);
        }

        //set
        if (auth()->user()->pref_filter_show_archived_tasks == 'yes') {
            request()->merge(['filter_show_archived_tasks' => 'yes']);
        }

    }

    function toggleKanbanView() {
        //update 'own tasks filter'
        if (request('toggle') == 'layout') {
            //toggle database settings
            auth()->user()->pref_view_tasks_layout = (auth()->user()->pref_view_tasks_layout == 'kanban') ? 'list' : 'kanban';
            auth()->user()->save();
        }

        //css setting for body
        if (auth()->user()->pref_view_tasks_layout == 'kanban') {
            config([
                'settings.css_kanban' => 'kanban',
                'visibility.kanban_tasks_sorting' => true,
            ]);
        }
    }

}