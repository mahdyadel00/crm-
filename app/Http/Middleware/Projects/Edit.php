<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Projects;

use App\Models\Project;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Edit {

    /**
     * The permisson repository instance.
     */
    protected $projectpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $projectpermissions, Project $project_model) {

        //project permissions repo
        $this->projectpermissions = $projectpermissions;

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

        //frontend
        $this->fronteEnd();

        //basic validation
        if (!$project = \App\Models\Project::Where('project_id', $project_id)->first()) {
            Log::error("project could not be found", ['process' => '[permissions][projects][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
            abort(409, __('lang.project_not_found'));
        }

        //permission: does user have permission to edit this project
        if ($this->projectpermissions->check('edit', $project_id)) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][projects][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //assigning a project and setting its manager
        if (auth()->user()->role->role_assign_projects == 'yes') {
            config(['visibility.project_modal_assign_fields' => true]);
            request()->merge([
                'edit_assigned' => true,
            ]);
        }

        //set project permissions
        if (auth()->user()->role->role_set_project_permissions == 'yes') {
            config(['visibility.project_modal_project_permissions' => true]);
        }

    }
}
