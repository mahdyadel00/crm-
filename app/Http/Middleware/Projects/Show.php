<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Projects;

use App\Models\Project;
use App\Permissions\ProjectPermissions;
use App\Repositories\ProjectRepository;
use Closure;
use Log;

class Show {

    //vars
    protected $projectpermissions;
    protected $projectmodel;
    protected $projectrepo;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $projectpermissions, Project $projectmodel, ProjectRepository $projectrepo) {

        $this->projectpermissions = $projectpermissions;
        $this->projectmodel = $projectmodel;
        $this->projectrepo = $projectrepo;

    }

    /**
     * This middleware does the following:
     *   1. validates that the project exists
     *   2. checks users permissions to [show] the resource
     *   3. sets various visibility and permissions settings (e.g. menu items, edit buttons etc)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.projects')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //project id
        $project_id = $request->route('project');

        //basic validation
        if (!$project = \App\Models\Project::Where('project_id', $project_id)->first()) {
            Log::error("project could not be found", ['process' => '[permissions][projects][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
            abort(404);
        }

        //friendly format
        $projects = $this->projectrepo->search($project_id);
        $project = $projects->first();

        //frontend
        $this->fronteEnd($project);

        //permission: does user have permission to view this project
        if ($this->projectpermissions->check('view', $project)) {
            //permission granted
            return $next($request);
        }

        Log::error("permission denied", ['process' => '[permissions][projects][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($project = '') {

        //defaults
        config([
            'visibility.projects_d3_vendor' => true,
            'visibility.project_show_custom_fields' => false,
        ]);

        //all users
        if ($this->projectpermissions->check('view', $project)) {
            config([
                'settings.project_permissions_view_tasks' => $this->projectpermissions->check('tasks-view', $project),
                'settings.project_permissions_view_milestones' => $this->projectpermissions->check('milestones-view', $project),
                'settings.project_permissions_view_files' => $this->projectpermissions->check('files-view', $project),
                'settings.project_permissions_view_comments' => $this->projectpermissions->check('comments-view', $project),
                'settings.project_permissions_view_timesheets' => $this->projectpermissions->check('timesheets-view', $project),
                'settings.project_permissions_view_invoices' => $this->projectpermissions->check('invoices-view', $project),
                'settings.project_permissions_view_payments' => $this->projectpermissions->check('payments-view', $project),
                'settings.project_permissions_view_expenses' => $this->projectpermissions->check('expenses-view', $project),
                'settings.project_permissions_view_tickets' => $this->projectpermissions->check('tickets-view', $project),
                'settings.project_permissions_view_notes' => $this->projectpermissions->check('notes-view', $project),
            ]);
        }

        //team permissions
        if (auth()->user()->is_team) {

            if ($this->projectpermissions->check('edit', $project)) {
                config([
                    'visibility.edit_project_button' => true,
                ]);
            }
            if ($this->projectpermissions->check('delete', $project)) {
                config([
                    'visibility.delete_project_button' => true,
                ]);
            }
            if (auth()->user()->role->role_projects_billing >= 1) {
                config([
                    'visibility.project_billing_summary' => true,
                ]);
            }
        }

        //client permissions
        if (auth()->user()->is_client) {
            config([
                'visibility.project_billing_summary' => true,
            ]);
        }

        //show we show the customer fields left panel section
        $count = \App\Models\CustomField::where('customfields_type', 'projects')->where('customfields_standard_form_status', 'enabled')->where('customfields_status', 'enabled')->count();
        if ($count > 0) {
            config([
                'visibility.project_show_custom_fields' => true,
            ]);
        }

    }

}
