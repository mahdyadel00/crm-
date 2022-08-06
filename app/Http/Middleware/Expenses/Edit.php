<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Expenses;
use Closure;
use Log;

class Edit {

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

        //expense id
        $expense_id = $request->route('expense');

        //does the expense exist
        if ($expense_id == '' || !$expense = \App\Models\Expense::Where('expense_id', $expense_id)->first()) {
            Log::error("expense could not be found", ['process' => '[permissions][expenses][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'expense id' => $expense_id ?? '']);
            abort(409, __('lang.expense_not_found'));
        }

        //frontend
        $this->fronteEnd($expense);

        //permission: does user have permission edit expenses
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_expenses >= 2) {
                //global permissions
                if (auth()->user()->role->role_expenses_scope == 'global') {

                    return $next($request);
                }
                //own permissions
                if (auth()->user()->role->role_expenses_scope == 'own') {
                    if ($expense->expense_creatorid == auth()->id()) {

                        return $next($request);
                    }
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][expenses][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($expense) {

        //default: show client and project options
        config(['visibility.expense_modal_client_project_fields' => true]);

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

        //invoiced already
        if ($expense->expense_billing_status == 'invoiced') {
            config(['visibility.expense_modal_edit_client_and_project' => false]);
        } else {

            //show the client/project selects
            config(['visibility.expense_modal_edit_client_and_project' => true]);

            //if client is already set, trigger select2 to show the clients projects
            if (is_numeric($expense->expense_clientid) && !is_numeric($expense->expense_projectid)) {
                config(['visibility.expense_modal_trigger_clients_project_list' => 'show']);
            }

        }
    }
}
