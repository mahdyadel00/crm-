<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for files
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Files;
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
     *   2. checks users permissions to [view] files
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //frontend
        $this->fronteEnd();

        //validation - attempted direct access to files or invalid resource_type
        if (!in_array(request('fileresource_type'), [
            'project',
            'client',
        ])) {
            Log::error("fileresource_type was not provided", ['process' => '[permissions][files][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(403);
        }

        //[project] - does user have 'project level' viewing permissions
        if (request('fileresource_type') == 'project') {
            //project files
            if (request('fileresource_id') > 0) {
                if ($this->projectpermissons->check('files-upload', request('fileresource_id'))) {
                    return $next($request);
                }
            } else {
                //projet template files
                if (auth()->user()->role->role_templates_projects >= 2) {
                    return $next($request);
                }
            }
        }

        //client files (not client project files)
        if (request('fileresource_type') == 'client') {
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_clients >= 2) {
                    return $next($request);
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][files][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default: show client and project options
        config([
            'visibility.file_modal_client_project_fields' => true,
            'visibility.file_modal_visible_to_client_option' => true,
        ]);

        /**
         * [embedded request]
         * the add new file request is being made from an embedded view (project page)
         *      - validate the project
         *      - do no display 'project' & 'client' options in the modal form
         *  */
        if (request()->filled('fileresource_id') && request()->filled('fileresource_type')) {

            //project resource
            if (request('fileresource_type') == 'project') {
                if ($project = \App\Models\Project::Where('project_id', request('fileresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'visibility.file_modal_client_project_fields' => false,
                    ]);

                    //client
                    if (auth()->user()->is_client) {
                        request()->merge([
                            'file_visibility_client' => 'on',
                        ]);
                        config([
                            'visibility.file_modal_visible_to_client_option' => false,
                        ]);
                    }

                    //add some form fields data
                    request()->merge([
                        'file_clientid' => $project->project_clientid,
                    ]);

                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][files][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }

            //client files (not project files)
            if (request('fileresource_type') == 'client') {
                request()->merge([
                    'file_visibility_client' => 'no',
                    'file_clientid' => request('fileresource_id'),
                ]);
                config([
                    'visibility.file_modal_visible_to_client_option' => false,
                ]);
            }

        }
    }
}
