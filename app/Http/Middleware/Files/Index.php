<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for files
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Files;
use App\Permissions\LeadPermissions;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Index {

    /**
     * The project permisson repository instance.
     */
    protected $projectpermissons;

    /**
     * The lead permisson repository instance.
     */
    protected $leadpermissons;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $projectpermissons, LeadPermissions $leadpermissons) {

        $this->projectpermissons = $projectpermissons;
        $this->leadpermissons = $leadpermissons;

    }

    /**
     * This middleware does the following:
     *     1. ensures that the files controller is not being accessed directly
     *       - i.e. it must only be accessed from a related resource (e.g. project, lead, etc)
     *     2. the permission for the files controller are derived from the permissions that the user has on the linked resource
     *       - e.g. if the user has permission to view a 'project' then they will have permission to view its files
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //various frontend and visibility settings
        $this->fronteEnd();

        //only allow external/embedded requests
        if (request('source') != 'ext' && !request()->ajax()) {
            Log::error("the request was not ajax as expected", ['process' => '[permissions][files][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(404);
        }

        //project files permission
        if (request('fileresource_type') == 'project') {
            //client - limit only visible files
            if (auth()->user()->is_client) {
                //sanity client
                request()->merge([
                    'filter_file_visibility_client' => 'yes',
                    'filter_file_clientid' => auth()->user()->clientid,
                ]);
            }
            //permission
            if (is_numeric(request('fileresource_id'))) {
                if ($this->projectpermissons->check('view', request('fileresource_id')) || $this->projectpermissons->check('super-user', request('fileresource_id'))) {
                    return $next($request);
                }
            }

            //permission - viewing clients project files, from client page
            if (auth()->user()->is_team) {
                if (is_numeric(request('filter_file_clientid'))) {
                    if (auth()->user()->role->role_clients >= 1) {
                        return $next($request);
                    }
                }
            }
        }

        //client files (not client project files)
        if (request('fileresource_type') == 'client' && request()->filled('fileresource_id')) {
            //only this client
            request()->merge([
                'filter_file_clientid' => request('fileresource_id'),
            ]);
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_clients >= 1) {
                    return $next($request);
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][files][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('foo') with urlResource('foo'), in blade templated
         * */
        if (request('fileresource_type') != '' || is_numeric(request('fileresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&fileresource_type=' . request('fileresource_type') . '&fileresource_id=' . request('fileresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //defaults
        config([
            'visibility.list_page_actions_search' => true,
            'visibility.files_col_visibility' => true,
        ]);

        //[project]
        if (request('fileresource_type') == 'project') {
            //everyone
            if ($this->projectpermissons->check('files-upload', request('fileresource_id')) || $this->projectpermissons->check('super-user', request('fileresource_id'))) {
                config(['visibility.list_page_actions_add_button' => true]);
            }
            //client
            if (auth()->user()->is_client) {
                config([
                    'visibility.files_col_visibility' => false,
                    'visibility.action_buttons_edit' => false,
                ]);
            }
        }

        //client files (not client project files)
        if (request('fileresource_type') == 'client' && request()->filled('fileresource_id')) {
            if (auth()->user()->role->role_clients >= 2) {
                config([
                    'visibility.list_page_actions_add_button' => true,
                    'visibility.files_col_visibility' => false,
                    'visibility.action_buttons_edit' => false,
                ]);
            }
        }

    }
}