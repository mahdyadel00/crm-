<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for milestones
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Milestones;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Create {

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

        //team user - project milestone
        if (auth()->user()->is_team) {
            if (request()->filled('milestoneresource_id')) {
                if ($project = \App\Models\Project::Where('project_id', request('milestoneresource_id'))->first()) {
                    if ($this->projectpermissions->check('milestone-manage', request('milestoneresource_id')) || $this->projectpermissions->check('super-user', request('milestoneresource_id'))) {
                        request()->merge([
                            'milestone_projectid' => request('milestoneresource_id'),
                        ]);
                        return $next($request);
                    }
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][milestones][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
