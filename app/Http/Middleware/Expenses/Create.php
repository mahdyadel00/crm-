<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Expenses;
use Closure;
use Log;

class Create {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] expenses
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.expenses')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //frontend
        $this->fronteEnd();

        //permission: does user have permission create expenses
        if (auth()->user()->role->role_expenses >= 2) {
            
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][expenses][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default: show client and project options
        config([
            'visibility.expense_modal_client_project_fields' => true,
            'visibility.expense_modal_edit_client_and_project' => true,
            ]);

        /**
         * [embedded request]
         * the add new expense request is being made from an embedded view (project page)
         *      - validate the project
         *      - do no display 'project' & 'client' options in the modal form
         *  */
        if (request()->filled('expenseresource_id') && request()->filled('expenseresource_type')) {

            //project resource
            if (request('expenseresource_type') == 'project') {
                if ($project = \App\Models\Project::Where('project_id', request('expenseresource_id'))->first()) {

                    //hide some form fields
                    config(['visibility.expense_modal_client_project_fields' => false]);

                    //add some form fields data
                    request()->merge([
                        'expense_projectid' => $project->project_id,
                        'expense_clientid' => $project->project_clientid,
                    ]);

                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][expenses][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }

            //client resource
            if (request('expenseresource_type') == 'client') {
                if ($client = \App\Models\Client::Where('client_id', request('expenseresource_id'))->first()) {

                    //hide some form fields
                    config(['visibility.expense_modal_client_project_fields' => false]);
                    config(['visibility.expense_modal_clients_projects' => true]);

                    //required form data
                    request()->merge([
                        'expense_clientid' => $client->client_id,
                    ]);

                    //clients projects list
                    $projects = \App\Models\Project::Where('project_clientid', request('expenseresource_id'))->get();
                    config(
                        [
                            'settings.clients_projects' => $projects,
                        ]
                    );
                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['process' => '[permissions][expenses][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }
        }

        
        //clicked from topnav 'add' button
        if(request('ref') == 'quickadd'){
            config([
                'visibility.expense_show_expense_option' => true,
            ]);
        }
    }
}
