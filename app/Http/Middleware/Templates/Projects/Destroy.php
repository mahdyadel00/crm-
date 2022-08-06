<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] precheck processes for project templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Templates\Projects;
use Closure;
use Log;

class Destroy {

    /**
     * This 'bulk actions' middleware does the following
     *   1. If the request was for a sinle item
     *         - single item actions must have a query string '?id=123'
     *         - this id will be merged into the expected 'ids' request array (just as if it was a bulk request)
     *   2. loop through all the 'ids' that are in the post request
     *
     * HTML for the checkbox is expected to be in this format:
     *   <input type="checkbox" name="ids[{{ $project->project_id }}]"
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        
        //project id
        $project_id = $request->route('project');

        //loop through each project and check permissions
        if ($project = \App\Models\Project::Where('project_id', $project_id)->Where('project_type', 'template')->first()) {    
            //permission: does user have permission edit projects
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_templates_projects < 3) {
                    abort(403);
                }
            }
        } else {
            //no items were passed with this request
            Log::error("no items were sent with this request", ['process' => '[project templates][action-bar]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => $project_id ?? '']);
            abort(409);
        }

        //all is on - passed
        return $next($request);
    }
}
