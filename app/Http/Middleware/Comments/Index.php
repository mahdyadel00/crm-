<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for comments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Comments;
use App\Permissions\LeadPermissions;
use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use Closure;
use Log;

class Index {

    /**
     * The project permisson repository instance.
     */
    protected $projectpermissions;

    /**
     * The lead permisson repository instance.
     */
    protected $leadpermissons;

    /**
     * The task permisson repository instance.
     */
    protected $taskpermissons;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(
        ProjectPermissions $projectpermissions,
        LeadPermissions $leadpermissons,
        TaskPermissions $taskpermissons
    ) {

        $this->projectpermissions = $projectpermissions;
        $this->leadpermissons = $leadpermissons;
        $this->taskpermissons = $taskpermissons;

    }

    /**
     * This middleware does the following:
     *     1. ensures that the comments controller is not being accessed directly
     *       - i.e. it must only be accessed from a related resource (e.g. project, lead, etc)
     *     2. the permission for the comments controller are derived from the permissions that the user has on the linked resource
     *       - e.g. if the user has permission to view a 'project' then they will have permission to view its comments
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //various frontend and visibility settings
        $this->fronteEnd();

        //validation - attempted direct access to comments or invalid resource_type
        if (!in_array(request('commentresource_type'), [
            'project',
        ])) {
            Log::error("commentresource_type was not provided", ['process' => '[permissions][comments][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409);
        }

        //only allow external/embedded requests
        if (request('source') != 'ext' && !request()->ajax()) {
            Log::error("the request was not ajax as expected", ['process' => '[permissions][comments][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(404);
        }

        //[permissions][project]
        if (request('commentresource_type') == 'project') {
            if ($this->projectpermissions->check('view', request('commentresource_id'))) {
                return $next($request);
            }
        }

        //[permissions][lead]
        if (request('commentresource_type') == 'lead') {
            if ($this->leadpermissons->check('participate', request('commentresource_id'))) {
                return $next($request);
            }
        }

        //[permissions][lead]
        if (request('commentresource_type') == 'task') {
            if ($this->taskpermissons->check('participate', request('commentresource_id'))) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][comments][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id()]);
        abort(403);
    }

    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('foo') with urlResource('foo'), in blade templated
         * */
        if (request('commentresource_type') != '' || is_numeric(request('commentresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&commentresource_type=' . request('commentresource_type') . '&commentresource_id=' . request('commentresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //defaults
        config([
            'visibility.list_page_actions_search' => true,
            'visibility.comments_col_visibility' => true,
            'visibility.comments_col_visibility' => true,
            'visibility.post_new_comment_form' => false,
        ]);

        //posting new coment permissions - project
        if (request('commentresource_type') == 'project') {
            config([
                'visibility.post_new_comment_form' => $this->projectpermissions->check('comments-post', request('commentresource_id')),
            ]);
        }

        //posting new coment permissions - lead
        if (request('commentresource_type') == 'lead') {
            config([
                'visibility.post_new_comment_form' => $this->leadpermissons->check('participate', request('commentresource_id')),
            ]);
        }

        //posting new coment permissions - task
        if (request('commentresource_type') == 'task') {
            config([
                'visibility.post_new_comment_form' => $this->taskpermissons->check('participate', request('commentresource_id')),
            ]);
        }

    }
}