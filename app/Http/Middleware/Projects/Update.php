<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [update] precheck processes for projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Projects;

use App\Models\Project;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Update {

    /**
     * The permisson repository instance.
     */
    protected $project_permissions;

    /**
     * The project model instance
     */
    protected $project_model;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $project_permissions, Project $project_model) {

        //project permissions repo
        $this->project_permissions = $project_permissions;
        //project model
        $this->project_model = $project_model;

    }

    /**
     * This middleware does the following
     *   1. validates that the project exists
     *   2. checks users permissions to [edit] the resource
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
        if (!$project = $this->project_model::find($project_id)) {
            Log::error("project could not be found", ['process' => '[permissions][projects][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
            abort(409, __('lang.error_not_found'));
        }

        //permission: does user have permission to edit this project
        if ($this->project_permissions->check('edit', $project)) {
            /**
             * [SANITIZE POST] - [PERMISSIONS OPTIONS]
             * make sure that in coming project permissions settings make sense:
             *    - i.e. 'clientperm_tasks_create' is enabled, then so must 'clientperm_tasks_view'
             * */
            if (request('clientperm_tasks_create') == 'on' || request('clientperm_tasks_collaborate') == 'on') {
                request()->merge(['clientperm_tasks_view' => 'on']);
            }

            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][projects][update]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
        abort(403);
    }
}
