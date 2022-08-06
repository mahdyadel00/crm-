<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for project templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Templates\Projects;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] projects
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //project id
        $project_id = $request->route('project');

        //frontend
        $this->fronteEnd();

        //does the project exist
        if ($project_id == '' || !$project = \App\Models\Project::Where('project_id', $project_id)->Where('project_type', 'template')->first()) {
            Log::error("project could not be found", ['process' => '[project templates][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit projects
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_templates_projects >= 2) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][project templates][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //some settings
        config([
            'settings.project' => true,
            'settings.bar' => true,
        ]);
    }
}
