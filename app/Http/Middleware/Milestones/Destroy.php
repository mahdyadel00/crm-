<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] precheck processes for milestones
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Milestones;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Destroy {

    /**
     * The permisson repository instance.
     */
    protected $projectpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $projectpermissions) {

        //milestone permissions repo
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

        //milestone id
        $milestone_id = $request->route('milestone');

        //does the milestone exist
        if (!$milestone = \App\Models\Milestone::Where('milestone_id', $milestone_id)->first()) {
            Log::error("milestone could not be found", ['process' => '[permissions][milestones][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'milestone id' => $milestone_id ?? '']);
            abort(404);
        }

        //team user - project milestone
        if (auth()->user()->is_team) {
            if ($project = \App\Models\Project::Where('project_id', $milestone->milestone_projectid)->first()) {
                if ($this->projectpermissions->check('milestone-manage', $milestone->milestone_projectid) || $this->projectpermissions->check('super-user', $milestone->milestone_projectid)) {
                    request()->merge([
                        'milestone_projectid' => $milestone->milestone_projectid,
                    ]);
                    return $next($request);
                }
            }
        }
        
        //permission denied
        Log::error("permission denied", ['process' => '[permissions][milestones][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
