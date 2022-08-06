<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Expenses;

use App\Models\Expense;
use Closure;
use Log;

class Index {

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

        //set for dynamically loading the expense modal
        $this->dynamicLoad();

        //embedded request: limit by supplied resource data
        if (request()->filled('expenseresource_type') && request()->filled('expenseresource_id')) {
            //project expenses
            if (request('expenseresource_type') == 'project') {
                request()->merge([
                    'filter_expense_projectid' => request('expenseresource_id'),
                ]);
            }
            //client exenses
            if (request('expenseresource_type') == 'client') {
                request()->merge([
                    'filter_expense_clientid' => request('expenseresource_id'),
                ]);
            }
        }

        //permission: does user have permission view expenses
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_expenses >= 1) {
                //limit to own expenses, if applicable
                if (auth()->user()->role->role_expenses_scope == 'own') {
                    request()->merge([
                        'filter_expense_creatorid' => auth()->id(),
                    ]);
                }
                return $next($request);
            }
        }

        //client - allow to view only embedded. Also as per project settings
        if (auth()->user()->is_client) {
            if (request()->ajax() && request()->filled('expenseresource_id')) {
                if ($project = \App\Models\Project::Where('project_id', request('expenseresource_id'))->first()) {
                    if ($project->clientperm_expenses_view == 'yes') {
                        return $next($request);
                    }
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][expenses][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('expense') with urlResource('expense'), in blade templated
         * */
        if (request('expenseresource_type') != '' || is_numeric(request('expenseresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&expenseresource_type=' . request('expenseresource_type') . '&expenseresource_id=' . request('expenseresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.expenses_col_client' => true,
            'visibility.expenses_col_project' => true,
            'visibility.expenses_col_category' => true,
            'visibility.expenses_col_id' => true,
            'visibility.expenses_col_date' => true,
            'visibility.expenses_col_description' => true,
            'visibility.expenses_col_user' => true,
            'visibility.expenses_col_amount' => true,
            'visibility.expenses_col_status' => true,
            'visibility.expenses_col_action' => true,
            'visibility.filter_panel_client' => true,
            'visibility.filter_panel_project' => true,
        ]);

        if (auth()->user()->is_admin) {
            config([
                'visibility.expenses_col_user' => true,
            ]);
        }

        //permissions -viewing
        if (auth()->user()->role->role_expenses >= 1) {
            if (auth()->user()->is_team) {
                config([
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                    'visibility.stats_toggle_button' => true,
                ]);
            }
            if (auth()->user()->is_client) {
                config([
                    'visibility.list_page_actions_search' => true,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_expenses >= 2 && auth()->user()->is_team) {
            config([
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.expenses_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_expenses >= 3 && auth()->user()->is_team) {
            config([
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('expenseresource_type') == 'project') {
            config([
                'visibility.expenses_col_client' => false,
                'visibility.expenses_col_project' => false,
                'visibility.expenses_col_project' => false,
                'visibility.filter_panel_client' => false,
                'visibility.filter_panel_project' => false,
            ]);
        }

        //columns visibility
        if (request('expenseresource_type') == 'client') {
            config([
                'visibility.expenses_col_client' => false,
                'visibility.expenses_col_category' => false,
                'visibility.filter_panel_client' => false,
                'visibility.filter_panel_project' => false,
                'visibility.filter_panel_clients_projects' => true,
            ]);
        }

        //calling this fron invoice page
        if (request('itemresource_type') == 'invoice') {
            config([
                'visibility.expenses_col_client' => false,
                'visibility.expenses_col_project' => true,
                'visibility.expenses_col_category' => false,
                'visibility.expenses_col_id' => false,
                'visibility.expenses_col_date' => true,
                'visibility.expenses_col_description' => true,
                'visibility.expenses_col_user' => false,
                'visibility.expenses_col_amount' => true,
                'visibility.expenses_col_status' => false,
                'visibility.expenses_col_action' => false,
                'settings.trimmed_title' => true,
            ]);
        }
    }

    /*
     * set the front end to load the modal dynamically
     */
    private function dynamicLoad() {
        //validate that the url is for loading a expense dynmically
        if (is_numeric(request()->route('expense')) && request()->segment(2) == 'v') {
            config([
                'visibility.dynamic_load_modal' => true,
                'settings.dynamic_trigger_dom' => '#dynamic-expense-content',
            ]);
        }
    }
}
