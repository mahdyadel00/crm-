<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for invoices
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Invoices;
use Closure;
use Log;

class Create {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] invoices
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.invoices')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //frontend
        $this->fronteEnd();

        //permission: does user have permission create invoices
        if (auth()->user()->role->role_invoices >= 2) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][invoices][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default: show client and project options
        config(['visibility.invoice_modal_client_project_fields' => true]);

        /**
         * [embedded request]
         * the add new invoice request is being made from an embedded view (project page)
         *      - validate the project
         *      - do no display 'project' & 'client' options in the modal form
         *  */
        if (request()->filled('invoiceresource_id') && request()->filled('invoiceresource_type')) {

            //project resource
            if (request('invoiceresource_type') == 'project') {
                if ($project = \App\Models\Project::Where('project_id', request('invoiceresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'visibility.invoice_modal_client_project_fields' => false,
                    ]);

                    //add some form fields data
                    request()->merge([
                        'bill_projectid' => $project->project_id,
                        'bill_clientid' => $project->project_clientid,
                    ]);

                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][invoices][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }

            //client resource
            if (request('invoiceresource_type') == 'client') {
                if ($client = \App\Models\Client::Where('client_id', request('invoiceresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'visibility.invoice_modal_client_project_fields' => false,
                        'visibility.invoice_modal_clients_projects' => true,
                    ]);

                    //required form data
                    request()->merge([
                        'bill_clientid' => $client->client_id,
                    ]);

                    //clients projects list
                    $projects = \App\Models\Project::Where('project_clientid', request('invoiceresource_id'))->get();
                    config(
                        [
                            'settings.clients_projects' => $projects,
                        ]
                    );
                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][invoices][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }
        }
    }
}
