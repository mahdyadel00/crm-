<?php

namespace App\Http\Middleware\Templates\Projects;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the project exists
     *   2. checks users permissions to [view] the project
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
            abort(404);
        }

        //team: does user have permission edit projects
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_templates_projects >= 1) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[project templates][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //team permissions
        if (auth()->user()->is_team) {

            if (auth()->user()->role->role_templates_projects >= 2) {
                config([
                    'visibility.edit_project_button' => true,
                ]);
            }
            if (auth()->user()->role->role_templates_projects >= 3) {
                config([
                    'visibility.delete_project_button' => true,
                ]);
            }
        }

    }

}
