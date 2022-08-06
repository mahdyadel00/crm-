<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for estimates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Estimates;
use Closure;
use Log;

class Create {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] estimates
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.estimates')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //frontend
        $this->fronteEnd();

        //permission: does user have permission create estimates
        if (auth()->user()->role->role_estimates >= 2) {

            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][estimates][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default: show client and project options
        config(['visibility.estimate_modal_client_fields' => true]);

        /**
         * [embedded request]
         * the add new estimate request is being made from an embedded view (project page)
         *      - validate the project
         *      - do no display 'project' & 'client' options in the modal form
         *  */
        if (request()->filled('estimateresource_id') && request()->filled('estimateresource_type')) {

            //client resource
            if (request('estimateresource_type') == 'client') {
                if ($client = \App\Models\Client::Where('client_id', request('estimateresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'visibility.estimate_modal_client_fields' => false,
                    ]);

                    //required form data
                    request()->merge([
                        'bill_clientid' => $client->client_id,
                    ]);

                } else {
                    //error not found
                    Log::error("the resource client could not be found", ['process' => '[permissions][estimates][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }

            //project resource
            if (request('estimateresource_type') == 'project') {
                if ($project = \App\Models\Project::Where('project_id', request('estimateresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'visibility.estimate_modal_client_fields' => false,
                    ]);

                    //add some form fields data
                    request()->merge([
                        'bill_projectid' => $project->project_id,
                        'bill_clientid' => $project->project_clientid,
                    ]);

                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][estimates][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }
        }
    }
}
