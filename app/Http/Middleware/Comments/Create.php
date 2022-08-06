<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for comments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Comments;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Create {

    /**
     * The project permisson repository instance.
     */
    protected $projectpermissons;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $projectpermissons) {

        $this->projectpermissons = $projectpermissons;
    }

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] comments
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //frontend
        $this->fronteEnd();

        //validation - attempted direct access to comments or invalid resource_type
        if (!in_array(request('commentresource_type'), [
            'project',
        ])) {
            Log::error("commentresource_type was not provided", ['process' => '[permissions][comments][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409);
        }

        //[project] - does user have 'project level' viewing permissions
        if (request('commentresource_type') == 'project') {
            if ($this->projectpermissons->check('comments-post', request('commentresource_id'))) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][comments][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //project resource
        if (request('commentresource_type') == 'project') {
            if ($project = \App\Models\Project::Where('project_id', request('commentresource_id'))->first()) {
                //add some form fields data
                request()->merge([
                    'comment_clientid' => $project->project_clientid,
                ]);
            } else {
                //error not found
                Log::error("the resource project could not be found", ['process' => '[permissions][comments][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(404);
            }
        }

        //lead resource
        if (request('commentresource_type') == 'task') {
            if ($task = \App\Models\Task::Where('task_id', request('commentresource_id'))->first()) {
                //add some form fields data
                request()->merge([
                    'comment_clientid' => $lead->task_clientid,
                ]);
            } else {
                //error not found
                Log::error("the resource project could not be found", ['process' => '[permissions][comments][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(404);
            }
        }

    }
}
