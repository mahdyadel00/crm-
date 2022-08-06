<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Subscriptions;
use Closure;
use Log;

class Create {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] subscriptions
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.subscriptions')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //frontend
        $this->fronteEnd();

        //permission: does user have permission create subscriptions
        if (auth()->user()->role->role_subscriptions >= 2) {      
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][subscriptions][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default: show client and project options
        config(['visibility.subscription_modal_client_project_fields' => true]);

        /**
         * [embedded request]
         * the add new subscription request is being made from an embedded view (project page)
         *      - validate the project
         *      - do no display 'project' & 'client' options in the modal form
         *  */
        if (request()->filled('subscriptionresource_id') && request()->filled('subscriptionresource_type')) {

            //project resource
            if (request('subscriptionresource_type') == 'project') {
                if ($project = \App\Models\Project::Where('project_id', request('subscriptionresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'visibility.subscription_modal_client_project_fields' => false,
                    ]);

                    //add some form fields data
                    request()->merge([
                        'subscription_projectid' => $project->project_id,
                        'subscription_clientid' => $project->project_clientid,
                    ]);

                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][subscriptions][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }

            //client resource
            if (request('subscriptionresource_type') == 'client') {
                if ($client = \App\Models\Client::Where('client_id', request('subscriptionresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'visibility.subscription_modal_client_project_fields' => false,
                        'visibility.subscription_modal_clients_projects' => true,
                    ]);

                    //required form data
                    request()->merge([
                        'subscription_clientid' => $client->client_id,
                    ]);

                    //clients projects list
                    $projects = \App\Models\Project::Where('project_clientid', request('subscriptionresource_id'))->get();
                    config(
                        [
                            'settings.clients_projects' => $projects,
                        ]
                    );
                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][subscriptions][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }
        }
    }
}
