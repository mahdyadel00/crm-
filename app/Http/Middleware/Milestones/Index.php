<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for milestones
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Milestones;

use App\Models\Milestone;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Index {

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
     *   2. checks users permissions to [view] milestones
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //various frontend and visibility settings
        $this->fronteEnd();

        if (!$project = \App\Models\Project::Where('project_id', request('milestoneresource_id'))->first()) {
            Log::error("milestones project could not be found", ['process' => '[permissions][milestones][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project id' => request('milestoneresource_id')]);
            abort(404);
        }

        //team user - management options
        if (auth()->user()->is_team) {
            if ($project->assignedperm_milestone_manage == 'yes' || $this->projectpermissons->check('super-user', request('milestoneresource_id'))) {
                if (config('system.settings_projects_assignedperm_milestone_manage') == 'yes') {
                    config([
                        //visibility
                        'visibility.list_page_actions_add_button' => true,
                        'visibility.milestone_actions' => true,
                    ]);
                }
            }
        }

        //user permissions
        if ($this->projectpermissons->check('view', request('milestoneresource_id')) || $this->projectpermissons->check('super-user', request('milestoneresource_id'))) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][milestones][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource_type and resource_id (for easy appending in blade templates - action url's)
         * [usage]
         *   replace the usual url('milestone/edit/etc') with urlResource('milestone/edit/etc'), in blade templated
         *   usually in the ajax.blade.php files (actions links)
         * */
        request()->merge([
            'resource_query' => 'ref=list&milestoneresource_type=' . request('milestoneresource_type') . '&milestoneresource_id=' . request('milestoneresource_id'),
            'filter_milestone_projectid' => request('milestoneresource_id'),
        ]);

    }
}
